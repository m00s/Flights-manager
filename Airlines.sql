DROP SCHEMA IF EXISTS Airlines;
CREATE SCHEMA Airlines;
USE Airlines;
DROP TABLE IF EXISTS Prenotazioni;
DROP TABLE IF EXISTS Assistenze;
DROP TABLE IF EXISTS Offerte;
DROP TABLE IF EXISTS Tariffe;
DROP TABLE IF EXISTS Viaggi;
DROP TABLE IF EXISTS Voli;
DROP TABLE IF EXISTS Aeroporti;
DROP TABLE IF EXISTS Luoghi;
DROP TABLE IF EXISTS Aerei;
DROP TABLE IF EXISTS Dipendenti;
DROP TABLE IF EXISTS Utenti;
DROP TABLE IF EXISTS Anagrafiche;
DROP TABLE IF EXISTS Bagagli;
DROP TABLE IF EXISTS Compagnie;

SET FOREIGN_KEY_CHECKS = 0;

/* Crea la tabella Anagrafiche */

CREATE TABLE Anagrafiche (
       email		VARCHAR(25),
       nome		VARCHAR(15) NOT NULL,
       cognome		VARCHAR(15) NOT NULL,
       nascita		DATE NOT NULL,
       sesso		ENUM('M','F') DEFAULT "M",
       PRIMARY KEY(email)
) ENGINE=InnoDB;

/* Crea la tabella Utenti */

CREATE TABLE Utenti (
       email		VARCHAR(25),
       password		VARCHAR(40) NOT NULL,
       type         ENUM('Guest','Admin') DEFAULT "Guest",
       PRIMARY KEY(email),
       FOREIGN KEY (email) REFERENCES Anagrafiche (email)
			ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Dipendenti */

CREATE TABLE Dipendenti (
       email		VARCHAR(25),
       matricola	INT(10),
       grado		ENUM('assistente','comandante','vice'),
       UNIQUE (matricola),
       PRIMARY KEY(matricola),
       FOREIGN KEY (email) REFERENCES Anagrafiche (email)
			ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Aerei */

CREATE TABLE Aerei (
       matricola	VARCHAR(10) PRIMARY KEY,
       marca		VARCHAR(10),
       modello		VARCHAR(25),
       anno			YEAR,
       postiPrima	INT(3),
       postiSeconda	INT(3)
) ENGINE=InnoDB;

/* Crea la tabella Aeroporti */

CREATE TABLE Aeroporti (
       id		INT AUTO_INCREMENT,
       nome	     	VARCHAR(15) NOT NULL,
       idCitta		INT NOT NULL,
       PRIMARY KEY(Id),
       FOREIGN KEY (idCitta) REFERENCES Luoghi (id)
                            	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Luoghi */

CREATE TABLE Luoghi (
       id		INT AUTO_INCREMENT,
       nomecitta	VARCHAR(15) NOT NULL,
       nazione		VARCHAR(15),
       PRIMARY KEY(Id)
) ENGINE=InnoDB;

/* Crea la tabella Compagnia */

CREATE TABLE Compagnie (
	idCompagnia	INT AUTO_INCREMENT PRIMARY KEY,
	nome		VARCHAR(100),
	numTel		VARCHAR(25),
	email		VARCHAR(50),
	nazione		VARCHAR(50)
)ENGINE=InnoDB;

/* Crea la tabella Bagaglio */

CREATE TABLE Bagagli(
	id 		INT AUTO_INCREMENT PRIMARY KEY,
	peso		INT(2),
	idCompagnia	INT,
	prezzo		INT(3),
	FOREIGN KEY (idCompagnia)	REFERENCES Compagnie (idCompagnia)
                            		ON DELETE CASCADE  ON UPDATE CASCADE

)ENGINE=InnoDB;

/* Crea la tabella Voli */

CREATE TABLE Voli (
       numero		VARCHAR(7) PRIMARY KEY,
       oraP		TIME NOT NULL,
       oraA		TIME NOT NULL,
       da		INT(5) NOT NULL,
       a		INT(5) NOT NULL,
       idCompagnia	INT,
       FOREIGN KEY (a) REFERENCES Aeroporti (id)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (da)	REFERENCES Aeroporti (id)
                            	ON DELETE CASCADE  ON UPDATE CASCADE,
       FOREIGN KEY (idCompagnia) REFERENCES Compagnie (idCompagnia)
                            	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Viaggi */

CREATE TABLE Viaggi (
       idViaggio	INT AUTO_INCREMENT,
       giorno		DATE,
       voloId		VARCHAR(7),
       stato		ENUM('effettuato','previsto','soppresso') DEFAULT 'previsto',
       aereo		VARCHAR(10) NOT NULL,
       comandante	INT(10) NOT NULL,
       vice			INT(10) NOT NULL,
       InseritoDa   VARCHAR(25) NOT NULL,
       
       PRIMARY KEY (idViaggio),

       FOREIGN KEY (InseritoDa)     REFERENCES Utenti (email)
                                    ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (voloId) 		REFERENCES Voli (numero)
                            		ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (comandante) 	REFERENCES Dipendenti (matricola)
                            		ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (vice) 			REFERENCES Dipendenti (matricola)
                            		ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (aereo) 			REFERENCES Aerei (matricola)
                            		ON DELETE CASCADE ON UPDATE CASCADE               		
) ENGINE=InnoDB;

/* Crea la tabella Tariffe */

CREATE TABLE Tariffe(
	id			INT AUTO_INCREMENT PRIMARY KEY,
	idViaggio 		INT,
	aereo			VARCHAR(10),
	prezzoPostoPrima	INT(4),
	prezzoPostoSeconda	INT(4),
	FOREIGN KEY (idViaggio)		REFERENCES Viaggi (idViaggio)
                            		ON DELETE CASCADE ON UPDATE CASCADE, 
	FOREIGN KEY (aereo) 		REFERENCES Aerei (matricola)
                            		ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;


/* Crea la tabella delle Offerte */

CREATE TABLE Offerte (
       idViaggio	INT,
       scontoperc	INT,
       disponibili	INT,
       PRIMARY KEY (idViaggio),
       FOREIGN KEY (idViaggio) 	REFERENCES Viaggi (idViaggio)
                            	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


/* Crea la tabella delle Assistenze */

CREATE TABLE Assistenze (
       idViaggio	INT,
       assistente	INT(10),
       PRIMARY KEY (idViaggio,assistente),
       FOREIGN KEY (idViaggio) 	REFERENCES Viaggi (idViaggio)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (assistente) REFERENCES Dipendenti (matricola)
                            	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;



/* Crea la tabella Prenotazioni */

CREATE TABLE Prenotazioni (
       id				INT(100) AUTO_INCREMENT,
       idViaggio		INT,
       utente			VARCHAR(25),
       acquistati		INT(3),
	   numeroBagagli	INT(3),
	   type				ENUM('prima','seconda') DEFAULT 'seconda',
	   stato			ENUM('valido','annullato','rimborsato') DEFAULT 'valido',
       PRIMARY KEY (id),
       
       FOREIGN KEY (idViaggio) 	REFERENCES Viaggi (idViaggio)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (utente) 	REFERENCES Utenti (email)
                            	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


/*Procedura inserimento Utenti*/
delimiter $
CREATE PROCEDURE InserisciUtente (IN mailConferma VARCHAR(25),IN anagrafiche VARCHAR(255),IN utenti VARCHAR(255),OUT inserito BOOL) 
BEGIN
	DECLARE Test INT;
	SELECT COUNT(*) INTO Test FROM Anagrafiche WHERE email=mailConferma;
	IF Test=0 THEN
		INSERT INTO Anagrafiche VALUES (anagrafiche);
		INSERT INTO Utenti VALUES (utenti);
		SET inserito=1;
	ELSE
		SET inserito=0;
	END IF;
END; $

CREATE PROCEDURE InserisciDipendente (IN mailConferma VARCHAR(25),IN anagrafiche VARCHAR(255),IN dipendente VARCHAR(255),OUT inserito BOOL) 
BEGIN
	DECLARE Test INT;
	SELECT COUNT(*) INTO Test FROM Anagrafiche WHERE email=mailConferma;
	IF Test=0 THEN
		INSERT INTO Anagrafiche VALUES (anagrafiche);
		INSERT INTO Dipendenti VALUES (dipendente);
		SET inserito=1;
	ELSE
		SET inserito=0;
	END IF;
END; $

CREATE PROCEDURE PostiLiberi (IN idV INT,IN tipo VARCHAR(7),OUT Posti INT) 
BEGIN
	DECLARE PPrimaPrenotati INT;
	DECLARE PPrima INT;
	SELECT postiPrima INTO PPrima FROM Viaggi v JOIN Aerei a ON(v.aereo=a.matricola) WHERE v.idViaggio=idV;
	SELECT COUNT(*) INTO PPrimaPrenotati FROM Prenotazioni p WHERE p.idViaggio=idV AND p.type=tipo AND p.stato='valido';
	SET Posti=PPrima-PPrimaPrenotati;
END; $

CREATE PROCEDURE PrezzoPrenotazione (IN idP INT,OUT Prezzo INT) 
BEGIN
	DECLARE tipo VARCHAR(7);
	DECLARE nBiglietti INT;
	DECLARE nBagagli INT;
	DECLARE prBagagli INT;
	DECLARE prBiglietto INT;
	SET Prezzo=0;
	SELECT type INTO tipo FROM Prenotazioni WHERE id=idP;	
	
	IF(tipo='prima') THEN
		SELECT prezzoPostiPrima INTO prBiglietto FROM (Prenotazioni p NATURAL JOIN Viaggi v)NATURAL JOIN Tariffe t WHERE p.id=idP;		
	ELSE
		SELECT prezzoPostiSeconda INTO prBiglietto FROM (Prenotazioni p NATURAL JOIN Viaggi v)NATURAL JOIN Tariffe t WHERE p.id=idP;
	END IF;
	
	SELECT acquistati INTO nBiglietti FROM Prenotazioni WHERE id=idP;
	SELECT bagagli INTO nBagagli FROM Prenotazioni WHERE id=idP;
	SELECT b.prezzo INTO prBagagli FROM (Bagagli b NATURAL JOIN Compagnie c)NATURAL JOIN Voli v 
									WHERE v.numero=(SELECT vi.idViaggio FROM Viaggi vi NATURAL JOIN Prenotazioni p WHERE p.id=idP);
	SET Prezzo=(prBiglietto*nBiglietti)+(prBagagli*nBagagli);
END; $

delimiter ;



/*

CREATE VIEW dettagliVolo AS
SELECT  vo.cittaP as Da, vo.cittaA as A, vo.OraP as Partenza, vo.OraA as Arrivo, TIMEDIFF(vo.OraA, vo.OraP) as Durata, pe.nome, pe.cognome, vi.prezzo, vi.postiliberi, a.marca, a.modello
FROM Voli vo, Viaggi vi, Aerei a, Personale pe
WHERE vi.voloId='AZ112' AND vi.dat='2012-08-12' AND vo.numero=vi.voloID AND pe.matricola=vi.comandante AND a.matricola=vi.aereo

LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/Filesdb/Aeroporti.txt' INTO TABLE Aeroporti FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/Filesdb/Voli.txt' INTO TABLE Voli FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/Filesdb/Aerei.txt' INTO TABLE Aerei FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/Filesdb/Utenti.txt' INTO TABLE Utenti FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/Filesdb/Dipendenti.txt' INTO TABLE Dipendenti FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/Filesdb/Stipendiati.txt' INTO TABLE Stipendiati FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/Filesdb/Viaggi.txt' INTO TABLE Viaggi FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/Filesdb/Prenotazioni.txt' INTO TABLE Prenotazioni FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/Filesdb/Assistenze.txt' INTO TABLE Assistenze FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;

*/
SET FOREIGN_KEY_CHECKS = 1;

