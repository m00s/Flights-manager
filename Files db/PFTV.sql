/*Procedura inserimento Utente*/
DROP PROCEDURE IF EXISTS InserisciUtente;
DROP PROCEDURE IF EXISTS InserisciDipendente;
DROP PROCEDURE IF EXISTS PostiLiberi;
DROP PROCEDURE IF EXISTS PrezzoPrenotazione;
DROP PROCEDURE IF EXISTS AggiornaViaggi;
DROP PROCEDURE IF EXISTS AggiornaPrenotazioni;
DROP PROCEDURE IF EXISTS CreaVistaVoliUtente;
DROP TRIGGER IF EXISTS CancellazioneCompagnia;
DROP TRIGGER IF EXISTS AnnullaPrenotazioni;
DROP TRIGGER IF EXISTS EliminaAereo;
DROP VIEW IF EXISTS viewVoli;

delimiter $

CREATE PROCEDURE eliminaUtente (IN idAnagraf INT) 
BEGIN
	DELETE FROM Prenotazioni WHERE acquirente=idAnagraf OR passeggero=idAnagraf;
	DELETE FROM	Utenti WHERE idAnag=idAnagraf;
	DELETE FROM Anagrafiche WHERE idAnag=idAnagraf;
END; $

CREATE PROCEDURE InserisciUtente (IN nome VARCHAR(15),IN cognome VARCHAR(15),IN nascita DATE,IN sesso VARCHAR(1),IN mail VARCHAR(25),
									IN psw VARCHAR(40),IN tipo VARCHAR(5),OUT inserito BOOL) 
BEGIN
	DECLARE Test INT;
	DECLARE ultimoId INT;
	SELECT COUNT(*) INTO Test FROM Anagrafiche a WHERE a.email=mail;
	IF Test=0 THEN
		INSERT INTO Anagrafiche (nome, cognome, nascita, sesso, email) VALUES (nome,cognome,nascita,sesso,mail);
		SELECT MAX(idAnag) INTO ultimoId FROM Anagrafiche;
		INSERT INTO Utenti VALUES (ultimoId,psw,tipo);
		SET inserito=1;
	ELSE
		SET inserito=0;
	END IF;
END; $


CREATE PROCEDURE InserisciDipendente  (IN nome VARCHAR(15),IN cognome VARCHAR(15),IN nascita DATE,IN sesso VARCHAR(1),IN mail VARCHAR(25),
										IN matricola INT,IN grado VARCHAR(10),IN compagnia INT,OUT inserito BOOL) 
BEGIN
	DECLARE Test INT;
	DECLARE ultimoId INT;
	SELECT COUNT(*) INTO Test FROM Dipendenti d WHERE d.email=mail;
	IF Test=0 THEN
		INSERT INTO Anagrafiche VALUES (nome,cognome,nascita,sesso,mail);
		SELECT COUNT(*) INTO ultimoId FROM Anagrafiche;
		INSERT INTO Dipendenti VALUES (ultimoId,matricola,grado,compagnia);
		SET inserito=1;
	ELSE
		SET inserito=0;
	END IF;
END; $

CREATE FUNCTION getPosti(classe BOOL, aereo VARCHAR(10)) RETURNS INT
BEGIN
	DECLARE Posti INT;
	IF(classe=0) THEN 
		SELECT postiPrima INTO Posti FROM Aerei WHERE matricola=aereo;
	ELSE 
		SELECT postiSeconda INTO Posti FROM Aerei WHERE matricola=aereo;
	END IF;
	RETURN Posti;
END

CREATE PROCEDURE InserisciViaggio  (IN Volo VARCHAR(7),IN giorno DATE,IN prezzoPrima INT,IN prezzoSeconda INT, IN idTratta INT,IN inseritoDa INT,
					IN compagnia INT,IN aereo VARCHAR(10),IN comandante INT(10), IN vice INT(10), IN ridottoPerc INT) 
BEGIN
	DECLARE ultimoId INT;
	INSERT INTO Viaggi (giorno, prezzoPrima, prezzoSeconda, postiPrima, postiSeconda, idTratta, inseritoDa) VALUES
						(giorno, prezzoPrima, prezzoSeconda, getPosti(0,aereo), getPosti(1,aereo), idTratta, inseritoDa);
	SELECT MAX(IdViaggio) INTO ultimoId FROM Viaggi;
	INSERT INTO ViaggiDiretti VALUES (ultimoId, Volo, aereo, comandante, vice, ridottoPerc, compagnia);
END; $

CREATE PROCEDURE inserisciViaggioConScali (IN giorno DATE,IN prezzoPrima INT,IN prezzoSeconda INT,IN postiPrima INT,
IN postiSeconda INT,IN idTratta INT,IN inseritoDa INT,OUT VIAGGIO INT)
BEGIN
	INSERT INTO Viaggi (giorno, prezzoPrima, prezzoSeconda, postiPrima, postiSeconda, idTratta, inseritoDa) VALUES
						(giorno, prezzoPrima, prezzoSeconda, postiPrima, postiSeconda, idTratta, inseritoDa);
	SELECT MAX(IdViaggio) INTO VIAGGIO FROM Viaggi;
	INSERT INTO ViaggiConScali VALUES (VIAGGIO);
END; $

/* Can't update table 'ViaggiDiretti' in stored function/trigger because it is already used by statement which invoked this stored function/trigger */
CREATE TRIGGER setPosti
BEFORE INSERT ON ViaggiDiretti
FOR EACH ROW
BEGIN
	DECLARE postiP INT;
	DECLARE postiS INT;
	SELECT postiPrima INTO postiP FROM Aerei a WHERE a.matricola=NEW.aereo;
	SELECT postiSeconda INTO postiS FROM Aerei a WHERE a.matricola=NEW.aereo;
	UPDATE ViaggiDiretti SET postiPrima=postiP, postiSeconda=postiS WHERE idViaggioDiretto=NEW.idViaggioDiretto;
END; $


CREATE PROCEDURE PostiLiberi(IN idV INT,OUT PostiPrima INT,OUT PostiSeconda INT) 
BEGIN
	DECLARE PPrenotati INT;
	DECLARE PTotali INT;
	SELECT a.postiPrima INTO PTotali FROM Viaggi v JOIN Aerei a ON(v.aereo=a.matricola) WHERE v.idViaggio=idV;
	SELECT COUNT(*) INTO PPrenotati FROM Prenotazioni p WHERE p.idViaggio=idV AND p.type='prima' AND p.stato='valido';
	SET PostiPrima=PTotali-PPrenotati;
	SET PTotali=0;
	SET PPrenotati=0;
	SELECT a.postiSeconda INTO PTotali FROM Viaggi v JOIN Aerei a ON(v.aereo=a.matricola) WHERE v.idViaggio=idV;
	SELECT COUNT(*) INTO PPrenotati FROM Prenotazioni p WHERE p.idViaggio=idV AND p.type='seconda' AND p.stato='valido';
	SET PostiSeconda=PTotali-PPrenotati;
END; $


CREATE PROCEDURE PrezzoPrenotazione (IN idP INT,OUT Prezzo INT) 
BEGIN
	DECLARE tipo VARCHAR(7);
	DECLARE nBagagli INT;
	DECLARE prBagagli INT;
	DECLARE prBiglietto INT;
	SET Prezzo=0;
	SELECT type INTO tipo FROM Prenotazioni WHERE id=idP;		
	SELECT bagagli INTO nBagagli FROM Prenotazioni WHERE id=idP;
	SELECT b.prezzo INTO prBagagli FROM (Bagagli b NATURAL JOIN Compagnie c)NATURAL JOIN Voli v 
									WHERE v.numero=(SELECT vi.idViaggio FROM Viaggi vi NATURAL JOIN Prenotazioni p WHERE p.id=idP);
	IF tipo='prima' THEN
		SELECT v.prezzoPrima INTO prBiglietto FROM (Prenotazioni p NATURAL JOIN Viaggi v) WHERE p.id=idP;
		IF nBagagli > 0 THEN
			SET nBagagli=nBagagli-1;
		END IF;
	ELSE
		SELECT v.prezzoSeconda INTO prBiglietto FROM Prenotazioni p NATURAL JOIN Viaggi v WHERE p.id=idP;
	END IF;

	SET Prezzo=(prBiglietto)+(prBagagli*nBagagli);
END; $

CREATE TRIGGER CancellazioneCompagnia
AFTER DELETE ON Compagnie
FOR EACH ROW
BEGIN
	DECLARE idDip INT;
	DECLARE idComp INT;
	DECLARE CurDip CURSOR FOR SELECT idAnag,idCompagnia FROM Dipendenti;
	DECLARE EXIT HANDLER FOR NOT FOUND
	BEGIN END;
	
	UPDATE Viaggi SET idCompagnia=0,comandante=0,vice=0,stato='soppresso' WHERE idViaggio IN 
											(SELECT idViaggio FROM Viaggi v 
												WHERE v.idCompagniaEsec=OLD.idCompagnia AND v.giorno>=CURRENT_DATE());

	UPDATE Itinerari SET stato='soppresso' WHERE idItinerario IN 
											(SELECT idItinerario FROM DettagliItinerari di NATURAL JOIN Viaggi v 
												WHERE v.idCompagniaEsec=OLD.idCompagnia AND v.giorno>=CURRENT_DATE());
	DELETE FROM DettagliItinerari WHERE idViaggio IN 
											(SELECT idViaggio FROM Viaggi v 
												WHERE v.idCompagniaEsec=OLD.idCompagnia AND v.giorno>=CURRENT_DATE());


	DELETE FROM Offerte WHERE idViaggio IN (SELECT idViaggio FROM Viaggi v 
							WHERE v.idCompagniaEsec=OLD.idCompagnia AND v.giorno>=CURRENT_DATE());

	DELETE FROM Scali WHERE idItinerario IN (SELECT idItinerario FROM Viaggi v NATURAL JOIN DettagliItinerario di 
												WHERE v.idCompagniaEsec=OLD.idCompagnia AND v.giorno>=CURRENT_DATE());

	DELETE FROM PostiPrimaClasse WHERE aereo IN (SELECT matricola FROM Aerei WHERE idCompagnia=OLD.idCompagnia);
	DELETE FROM Bagagli WHERE idCompagnia=OLD.idCompagnia;
	DELETE FROM TariffeBagagli WHERE idCompagnia=OLD.idCompagnia;
	DELETE FROM Voli WHERE idCompagni=OLD.idCompagnia;
	DELETE FROM Aerei WHERE idCompagni=OLD.idCompagnia;
	
	OPEN CurDip;
	LOOP
		FETCH CurDip INTO idDip,idComp;
		IF idComp=OLD.idCompagnia THEN
			DELETE FROM Dipendenti WHERE idAnag=idDip;
			DELETE FROM Anagrafiche WHERE idAnag=idDip;
		END IF;
	END LOOP;
	CLOSE CurDip;
END; $

CREATE TRIGGER AnnullaPrenotazioniViaggi
BEFORE UPDATE ON Viaggi
FOR EACH ROW
BEGIN 
	IF NEW.stato='soppresso' THEN
		UPDATE Prenotazioni p SET p.stato='annullato' WHERE p.idViaggio=NEW.idViaggio;
		UPDATE Itinerari i SET i.stato='soppresso' WHERE i.idItinerario IN
													(SELECT idItinerario FROM DettagliItinerari di WHERE di.idViaggio=NEW.idViaggio);
	END IF;
END; $

delimiter ;

CREATE VIEW viewComandanti AS
SELECT d.matricola, a.nome, a.cognome, a.sesso, a.nascita, c.nome AS Compagnia
FROM Dipendenti d NATURAL JOIN Anagrafiche a JOIN Compagnie c ON (d.idCompagnia=c.idCompagnia)
WHERE d.grado='comandante';

CREATE VIEW viewTratte AS
SELECT t.idTratta AS Tratta, a1.nome AS Partenza, a2.nome AS Arrivo
FROM Tratte t JOIN Aeroporti a1 ON (t.da=a1.idAeroporto) JOIN Aeroporti a2 ON (t.a=a2.idAeroporto);

CREATE VIEW viewViaggiDiretti AS
SELECT v.idViaggio, v.giorno, vt.Partenza AS da, vt.Arrivo AS a, l1.nomecitta AS luogoP, l2.nomecitta AS luogoA, vo.oraP, vo.oraA, v.stato, v.prezzoPrima, v.prezzoSeconda,
		v.postiPrima, v.postiSeconda, c.nome AS compagnia, v.inseritoDa AS admin
FROM Viaggi v JOIN ViaggiDiretti vd ON (v.idViaggio=vd.idViaggioDiretto) JOIN viewTratte vt ON (v.idTratta=vt.Tratta)
		JOIN Voli vo ON vd.idVolo=vo.idVolo JOIN Compagnie c ON (vd.idCompagniaEsec=c.idCompagnia) JOIN Tratte t ON 
		vt.Tratta=t.idTratta JOIN Luoghi l1 ON (t.da=l1.idLuogo) JOIN Luoghi l2 ON (t.a=l2.idLuogo)


CREATE VIEW viewViaggiConScali AS
SELECT v.idViaggio, v.giorno, vt.Partenza AS da, vt.Arrivo AS a, vvd1.oraP, vvd2.oraA, v.stato, v.prezzoPrima, v.prezzoSeconda, v.postiPrima, v.postiSeconda,v.inseritoDa AS admin
FROM Viaggi v JOIN ViaggiConScali vcs ON (v.idViaggio=vcs.idViaggioConScali) JOIN viewTratte vt ON (v.idTratta=vt.Tratta) JOIN Scali s1
	ON (vcs.idViaggioConScali=s1.idViaggioConScali) JOIN viewViaggiDiretti vvd1 ON (s1.idViaggioDiretto = vvd1.idViaggio)JOIN Scali s2
	ON (vcs.idViaggioConScali=s2.idViaggioConScali) JOIN viewViaggiDiretti vvd2 ON (s2.idViaggioDiretto = vvd2.idViaggio)
WHERE s1.ordine=1 AND s2.ordine=(select MAX(ordine) from Scali where Scali.idViaggioConScali=vcs.idViaggioConScali)


DROP VIEW IF EXISTS viewViaggi;
CREATE VIEW viewViaggi AS 
SELECT  l1.nomeCitta AS Partenza, ap.nome AS A1, vo.oraP AS OraPartenza,l2.nomeCitta AS Arrivo, aa.nome AS A2, 
		vo.oraA AS OraArrivo, v.giorno AS Giorno, TIMEDIFF(vo.oraA,vo.oraP) AS Durata,v.prezzoPrima,v.prezzoSeconda,
		v.postiPrima,v.postiSeconda, 0 AS Scali,v.idViaggio AS Viaggio,v.inseritoDa	
FROM (((((Viaggi v JOIN ViaggiDiretti vd ON (v.idViaggio=vd.idViaggioDiretto))JOIN Voli vo ON (vd.idVolo=vo.idVolo))
		JOIN Compagnie co ON(co.idCompagnia=vd.idCompagniaEsec))JOIN Aerei ae ON (ae.matricola=vd.aereo))JOIN Tratte t ON(v.idTratta=t.idTratta)),
	(Tratte t1 JOIN Aeroporti ap ON(t1.da=ap.idAeroporto))JOIN Luoghi l1 ON(ap.idLuogo=l1.idLuogo),
	(Tratte t2 JOIN Aeroporti aa ON(t2.a=aa.idAeroporto))JOIN Luoghi l2 ON(aa.idLuogo=l2.idLuogo)
WHERE t.da=t1.da AND t.a=t2.a 
UNION
SELECT  l1.nomeCitta AS Partenza, ap.nome AS A1, vo.oraP AS OraPartenza,l2.nomeCitta AS Arrivo, aa.nome AS A2, 
		vo1.oraA AS OraArrivo, v.giorno AS Giorno, TIMEDIFF(vo1.oraA,vo.oraP) AS Durata,v.prezzoPrima,v.prezzoSeconda,
		v.postiPrima,v.postiSeconda, MAX(s1.ordine)-1 AS Scali,v.idViaggio AS Viaggio,v.inseritoDa		
FROM (((((((Viaggi v JOIN ViaggiConScali vcs ON(v.idViaggio=vcs.idViaggioConScali))JOIN Scali s ON(vcs.idViaggioConScali=s.idViaggioConScali))
	JOIN ViaggiDiretti vd ON (s.idViaggioDiretto=vd.idViaggioDiretto))JOIN Voli vo ON (vd.idVolo=vo.idVolo))
	JOIN Compagnie co ON(co.idCompagnia=vd.idCompagniaEsec))JOIN Aerei ae ON (ae.matricola=vd.aereo))JOIN Tratte t ON(v.idTratta=t.idTratta)),
	
	(((((((Viaggi v1 JOIN ViaggiConScali vcs1 ON(v1.idViaggio=vcs1.idViaggioConScali))JOIN Scali s1 ON(vcs1.idViaggioConScali=s1.idViaggioConScali))
	JOIN ViaggiDiretti vd1 ON (s1.idViaggioDiretto=vd1.idViaggioDiretto))JOIN Voli vo1 ON (vd1.idVolo=vo1.idVolo))
	JOIN Compagnie co1 ON(co1.idCompagnia=vd1.idCompagniaEsec))JOIN Aerei ae1 ON (ae1.matricola=vd1.aereo))JOIN Tratte t1 ON(v1.idTratta=t1.idTratta)),
	
	(Tratte t3 JOIN Aeroporti ap ON(t3.da=ap.idAeroporto))JOIN Luoghi l1 ON(ap.idLuogo=l1.idLuogo),
	(Tratte t4 JOIN Aeroporti aa ON(t4.a=aa.idAeroporto))JOIN Luoghi l2 ON(aa.idLuogo=l2.idLuogo)
	
WHERE t.da=t3.da AND t.a=t4.a AND s.ordine=(SELECT MIN(s2.ordine)
													FROM Scali s2 
													WHERE s2.idViaggioConScali=vcs.idViaggioConScali)
		AND s1.ordine=(SELECT MAX(s2.ordine)
								FROM Scali s2 
								WHERE s2.idViaggioConScali=vcs.idViaggioConScali);