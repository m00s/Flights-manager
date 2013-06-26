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

CREATE PROCEDURE InserisciUtente (IN nome VARCHAR(15),IN cognome VARCHAR(15),IN nascita DATE,IN sesso VARCHAR(1),IN mail VARCHAR(25),
									IN password VARCHAR(40),IN tipo VARCHAR(5),OUT inserito BOOL) 
BEGIN
	DECLARE Test INT;
	DECLARE ultimoId INT;
	SELECT COUNT(*) INTO Test FROM Utenti u WHERE u.email=mail;
	IF Test=0 THEN
		INSERT INTO Anagrafiche VALUES (nome,cognome,nascita,sesso,mail);
		SELECT COUNT(*) INTO ultimoId FROM Anagrafiche;
		INSERT INTO Utenti VALUES (ultimoId,password,tipo);
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


CREATE PROCEDURE InserisciViaggio  (IN Volo VARCHAR(7),IN giorno DATE,IN prezzoPrima INT,IN prezzoSeconda INT, IN idTratta INT,IN inseritoDa INT,
					IN compagnia INT,IN aereo VARCHAR(10),IN comandante INT(10), IN vice INT(10), IN ridottoPerc INT) 
BEGIN
	DECLARE ultimoId INT;
	INSERT INTO Viaggi (giorno, prezzoPrima, prezzoSeconda, postiPrima, postiSeconda, idTratta, inseritoDa) VALUES
						(giorno, prezzoPrima, prezzoSeconda,0,0, idTratta, inseritoDa);
	SELECT MAX(IdViaggio) INTO ultimoId FROM Viaggi;
	INSERT INTO ViaggiDiretti VALUES (ultimoId, Volo, aereo, comandante, vice, ridottoPerc, compagnia);
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

/* View voli utente */

CREATE VIEW viewVoli AS 
SELECT l1.nomeCitta AS Partenza, ap.nome AS A1, v1.oraP AS OraPartenza,l2.nomeCitta AS Arrivo, aa.nome AS A2, 
v1.oraA AS OraArrivo, i.giorno AS Giorno, TIMEDIFF(v1.oraA,v1.oraP) AS Durata,i.prezzoSeconda, MAX(sc.ordine) AS Scali, i.idItinerario
FROM ((((Itinerari i JOIN DettagliItinerari di ON(i.idItinerario=di.idItinerario)) JOIN Viaggi vi ON(di.idViaggio=vi.idViaggio)))
JOIN Tratte t ON (i.idTratta=t.idTratta),  Viaggi vi1 JOIN Voli v1 ON (vi1.idVolo=v1.idVolo)) JOIN Scali sc ON (i.idItinerario=sc.idItinerario),
(Tratte t1 JOIN Aeroporti ap ON(t1.da=ap.idAeroporto))JOIN Luoghi l1 ON(ap.idLuogo=l1.idLuogo),
(Tratte t2 JOIN Aeroporti aa ON(t2.a=aa.idAeroporto))JOIN Luoghi l2 ON(aa.idLuogo=l2.idLuogo)
WHERE t.da=t1.da AND t.a=t2.a AND vi1.idViaggio=vi.idViaggio
GROUP BY i.idItinerario
	UNION
SELECT l1.nomeCitta AS Partenza, ap.nome AS A1, v1.oraP AS OraPartenza,l2.nomeCitta AS Arrivo, aa.nome AS A2, 
v1.oraA AS OraArrivo, i.giorno AS Giorno, TIMEDIFF(v1.oraA,v1.oraP) AS Durata,i.prezzoSeconda, 0, i.idItinerario
FROM ((((Itinerari i JOIN DettagliItinerari di ON(i.idItinerario=di.idItinerario)) JOIN Viaggi vi ON(di.idViaggio=vi.idViaggio)))
JOIN Tratte t ON (i.idTratta=t.idTratta),  Viaggi vi1 JOIN Voli v1 ON (vi1.idVolo=v1.idVolo)),
(Tratte t1 JOIN Aeroporti ap ON(t1.da=ap.idAeroporto))JOIN Luoghi l1 ON(ap.idLuogo=l1.idLuogo),
(Tratte t2 JOIN Aeroporti aa ON(t2.a=aa.idAeroporto))JOIN Luoghi l2 ON(aa.idLuogo=l2.idLuogo)
WHERE t.da=t1.da AND t.a=t2.a AND vi1.idViaggio=vi.idViaggio
GROUP BY i.idItinerario;

CREATE VIEW viewComandanti AS
SELECT d.matricola, a.nome, a.cognome, a.sesso, a.nascita, c.nome AS Compagnia
FROM Dipendenti d NATURAL JOIN Anagrafiche a JOIN Compagnie c ON (d.idCompagnia=c.idCompagnia)
WHERE d.grado='comandante'

CREATE VIEW viewTratte AS
SELECT t.idTratta AS Tratta, a1.nome AS Partenza, a2.nome AS Arrivo
FROM Tratte t JOIN Aeroporti a1 ON (t.da=a1.idAeroporto) JOIN Aeroporti a2 ON (t.a=a2.idAeroporto)

CREATE VIEW viewVoli AS
SELECT 
FROM Viaggi v NATURAL JOIN ViaggiDiretti JOIN ViaggiConScali ON (idViaggio = )
WHERE


DROP VIEW IF EXISTS viewVoli;
CREATE VIEW viewVoli AS
SELECT l1.nomeCitta AS Partenza,ap.nome AS AeroportoPartenza,vo.oraP,l2.nomeCitta AS Arrivo,aa.nome AS AeroportoArrivo,vo.oraA,
		TIMEDIFF(vo.oraA,vo.oraP) AS Durata,v.giorno,v.prezzoSeconda,v.postiSeconda,v.idViaggio
FROM (Viaggi v JOIN ViaggiDiretti vd ON (v.idViaggio = vd.idViaggioDiretto))JOIN Voli vo ON (vo.idVolo=vd.idViaggioDiretto)
		JOIN Tratte t ON (v.idTratta=t.idTratta),
	(Tratte t1 JOIN Aeroporti ap ON(t1.da=ap.idAeroporto))JOIN Luoghi l1 ON(ap.idLuogo=l1.idLuogo),
	(Tratte t2 JOIN Aeroporti aa ON(t2.a=aa.idAeroporto))JOIN Luoghi l2 ON(aa.idLuogo=l2.idLuogo)
WHERE t.da=t1.da AND t.a=t2.a 













