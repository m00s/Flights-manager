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
											(SELECT idViaggio FROM Viaggi v NATURAL JOIN CompagnieViaggi cv 
												WHERE cv.idCompagniaEsec=idCompagnia AND v.giorno>=CURRENT_DATE());

	DELETE FROM Offerte WHERE idViaggio IN (SELECT idViaggio FROM Viaggi v NATURAL JOIN CompagnieViaggi cv 
							WHERE cv.idCompagniaEsec=idCompagnia AND v.giorno>=CURRENT_DATE());

	DELETE FROM ViaggiVolo WHERE idViaggio IN (SELECT idViaggio FROM Viaggi v NATURAL JOIN CompagnieViaggi cv 
							WHERE cv.idCompagniaEsec=idCompagnia AND v.giorno>=CURRENT_DATE());

	DELETE FROM ViaggiAereo WHERE idViaggio IN (SELECT idViaggio FROM Viaggi v NATURAL JOIN CompagnieViaggi cv 
							WHERE cv.idCompagniaEsec=idCompagnia AND v.giorno>=CURRENT_DATE());


	DELETE FROM Scali WHERE idViaggio IN (SELECT idViaggio FROM Viaggi v NATURAL JOIN CompagnieViaggi cv 
												WHERE cv.idCompagniaEsec=idCompagnia AND v.giorno>=CURRENT_DATE());

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

CREATE TRIGGER AnnullaPrenotazioni
BEFORE UPDATE ON Viaggi
FOR EACH ROW
BEGIN 
	IF NEW.stato='soppresso' THEN
		UPDATE Prenotazioni p SET p.stato='annullato' WHERE p.idViaggio=NEW.idViaggio;
	END IF;
END; $

delimiter ;

CREATE VIEW viewVoli AS 
SELECT l1.nomeCitta AS Partenza,ap.nome AS A1,v1.oraP AS OraPartenza,
	l2.nomeCitta AS Arrivo,aa.nome AS A2,v1.oraA AS OraArrivo,vi.giorno,vi.prezzoSeconda,c.nome AS Compagnia,vi.idViaggio,vi.inseritoDa
	FROM (Viaggi vi NATURAL JOIN Tratte t) JOIN Compagnie c ON (vi.idCompagniaEsec=c.idCompagnia),
		(Tratte t1 JOIN Aeroporti ap ON(t1.da=ap.idAeroporto))JOIN Luoghi l1 ON(ap.idLuogo=l1.idLuogo),
		(Tratte t2 JOIN Aeroporti aa ON(t2.a=aa.idAeroporto))JOIN Luoghi l2 ON(aa.idLuogo=l2.idLuogo),
		(((Viaggi vi2 JOIN DettagliViaggi dv ON (vi2.idViaggio=dv.idViaggio))JOIN Voli v1 
		ON(dv.idVolo=v1.idVolo))JOIN Tratte t3 ON(v1.idTratta=t3.idTratta))
	WHERE t.da=t1.da AND t.a=t2.a AND vi.idViaggio=vi2.idViaggio
	GROUP BY vi.idViaggio;
