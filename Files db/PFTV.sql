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
DROP PROCEDURE IF EXISTS eliminaUtente;
DROP VIEW IF EXISTS viewVoli;

delimiter $
								
CREATE EVENT `StatoViaggi` ON SCHEDULE EVERY1 DAY STARTS '2013-07-15 00:00:00' 
ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Cambia lo stato dei viaggi eseguiti'
 DO call setViaggiEffettuati();

CREATE PROCEDURE setViaggiEffettuati()
BEGIN
	DECLARE Done INT DEFAULT 0;
	DECLARE idV INT;
	DECLARE Cur CURSOR FOR
			SELECT idViaggio FROM Viaggi v WHERE v.stato = 'previsto' AND v.giorno < CURDATE();
	DECLARE CONTINUE HANDLER FOR NOT FOUND
		SET Done=1;
	
	OPEN Cur;
	REPEAT
		FECTH Cur INTO idV;
		IF NOT Done THEN
			UPDATE Viaggi v SET v.stato = 'effettuato' WHERE idViaggio=idV;
			DELETE FROM Offerte WHERE idViaggio=idV;
		ENDIF;
	UNTIL Done END REPEAT;
	CLOSE Cur;
END;


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
END; $ 

CREATE PROCEDURE InserisciViaggio(IN Volo VARCHAR(7),IN giorno DATE,IN prezzoPrima INT,IN prezzoSeconda INT, IN idTratta INT,IN inseritoDa INT,
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
SELECT v.idViaggio, v.giorno, vt.Partenza AS da, vt.Arrivo AS a, l1.nomecitta AS luogoP, l2.nomecitta AS luogoA, vo.oraP, vo.oraA, TIMEDIFF(vo.oraA, vo.oraP) AS durata, v.stato, v.prezzoPrima, v.prezzoSeconda,
		v.postiPrima, v.postiSeconda, c.nome AS compagnia, v.inseritoDa AS admin
FROM Viaggi v JOIN ViaggiDiretti vd ON (v.idViaggio=vd.idViaggioDiretto) JOIN viewTratte vt ON (v.idTratta=vt.Tratta)
		JOIN Voli vo ON vd.idVolo=vo.idVolo JOIN Compagnie c ON (vd.idCompagniaEsec=c.idCompagnia) JOIN Tratte t ON 
		vt.Tratta=t.idTratta JOIN Luoghi l1 ON (t.da=l1.idLuogo) JOIN Luoghi l2 ON (t.a=l2.idLuogo)

CREATE VIEW viewViaggiConScali AS
SELECT v.idViaggio, v.giorno, vt.Partenza AS da, vt.Arrivo AS a, v.stato, v.prezzoPrima, v.prezzoSeconda, v.postiPrima,
		v.postiSeconda,v.inseritoDa AS admin
FROM Viaggi v JOIN ViaggiConScali vcs ON (v.idViaggio=vcs.idViaggioConScali) JOIN viewTratte vt ON (v.idTratta=vt.Tratta)
														