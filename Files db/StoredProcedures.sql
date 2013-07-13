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

$

CREATE PROCEDURE ScalaPosti ( IN nbiglietti INT, IN prima INT,IN idv INT,IN scali INT)
BEGIN
DECLARE idvs INT;
DECLARE idvd INT;
DECLARE Cur CURSOR FOR SELECT idViaggioDiretto,idViaggioConScali FROM Scali;
DECLARE EXIT HANDLER FOR NOT FOUND
BEGIN END;

IF scali=0 THEN
	IF prima=1 THEN
			UPDATE Viaggi SET postiPrima=postiPrima-nbiglietti WHERE idViaggio=idv;
		ELSE
			UPDATE Viaggi SET postiSeconda=postiSeconda-nbiglietti WHERE idViaggio=idv;
	END IF;
END IF;
IF scali=1 THEN
	IF prima=1 THEN
	UPDATE Viaggi SET postiPrima=postiPrima-nbiglietti WHERE idViaggio=idv;
			OPEN Cur;
				LOOP
					FETCH Cur INTO idvd,idvs;
					IF idvs=idv THEN
						UPDATE Viaggi SET postiPrima=postiPrima-nbiglietti WHERE idViaggio=idvd;
					END IF;
				END LOOP;
			CLOSE Cur;
		ELSE		
			UPDATE Viaggi SET postiSeconda=postiSeconda-nbiglietti WHERE idViaggio=idv;
			OPEN Cur;
				LOOP
					FETCH Cur INTO idvd,idvs;
					IF idvs=idv THEN
						UPDATE Viaggi SET postiSeconda=postiSeconda-nbiglietti WHERE idViaggio=idvd;
					END IF;
				END LOOP;
			CLOSE Cur;
	END IF;
END IF;
END; $

												
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

delimiter ;