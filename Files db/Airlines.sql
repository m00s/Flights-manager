DROP TABLE IF EXISTS Prenotazioni;
DROP TABLE IF EXISTS Assistenze;
DROP TABLE IF EXISTS Viaggi;
DROP TABLE IF EXISTS Voli;
DROP TABLE IF EXISTS Aeroporti;
DROP TABLE IF EXISTS Aerei;
DROP TABLE IF EXISTS Stipendiati;
DROP TABLE IF EXISTS Dipendenti;
DROP TABLE IF EXISTS Utenti;

SET FOREIGN_KEY_CHECKS = 0;

/* Crea la tabella Utenti */

CREATE TABLE Utenti (
       id           INT(10) AUTO_INCREMENT,
       nome			VARCHAR(15) NOT NULL,
       cognome		VARCHAR(15) NOT NULL,
       nascita		DATE NOT NULL,
       sesso		ENUM('M','F') DEFAULT "M",
       mail			VARCHAR(25) NOT NULL,
       password		VARCHAR(40) NOT NULL,
       type         ENUM('Guest','Admin') DEFAULT "Guest",
       UNIQUE (mail),
       PRIMARY KEY(id)
) ENGINE=InnoDB;

/* Crea la tabella Dipendenti */

CREATE TABLE Dipendenti (
       matricola	INT(10),
       nome			VARCHAR(15) NOT NULL,
       cognome		VARCHAR(15) NOT NULL,
       nascita		DATE NOT NULL,
       sesso		ENUM('M','F') DEFAULT "M",
       grado		ENUM('assistente','comandante','vice'),
       UNIQUE (matricola),
       PRIMARY KEY(matricola)
) ENGINE=InnoDB;

/* Crea la tabella Stipendiati */

CREATE TABLE Stipendiati (
       idDipendente	INT(10),
       tipocontratto	ENUM ('determinato','indeterminato'),
       stipdendiomese	DECIMAL(10,2),
       PRIMARY KEY(idDipendente),
       FOREIGN KEY (idDipendente)	REFERENCES Dipendenti (matricola)
					ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Aerei */

CREATE TABLE Aerei (
       matricola	VARCHAR(10) PRIMARY KEY,
       marca		VARCHAR(10),
       modello		VARCHAR(25),
       anno			YEAR,
       posti		INT(3)
) ENGINE=InnoDB;

/* Crea la tabella Aeroporti */

CREATE TABLE Aeroporti (
       id		INT(5) AUTO_INCREMENT,
       citta		VARCHAR(15) NOT NULL,
       nome	     	VARCHAR(15) NOT NULL,
       nazione		VARCHAR(15),
       INDEX (citta),
       PRIMARY KEY(Id)
) ENGINE=InnoDB;

/* Crea la tabella Voli */

CREATE TABLE Voli (
       numero	VARCHAR(7) PRIMARY KEY,
       oraP		TIME NOT NULL,
       oraA		TIME NOT NULL,
       da		INT(5) NOT NULL,
       a		INT(5) NOT NULL,
       FOREIGN KEY (a) REFERENCES Aeroporti (id)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (da)	REFERENCES Aeroporti (id)
                            	ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Viaggi */

CREATE TABLE Viaggi (
       giorno		DATE,
       voloId		VARCHAR(7),
       stato		ENUM('effettuato','previsto','soppresso') DEFAULT 'previsto',
       aereo		VARCHAR(10) NOT NULL,
       comandante	INT(10) NOT NULL,
       vice			INT(10) NOT NULL,
       prezzo		DECIMAL(10,2),
       postiliberi	INT (3),
       InseritoDa   INT(10) NOT NULL,
       
       PRIMARY KEY (giorno,voloId),

       FOREIGN KEY (InseritoDa)     REFERENCES Utenti (id)
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


/* Crea la tabella delle Assistenze */

CREATE TABLE Assistenze (
       giorno		DATE,
       voloId		VARCHAR(7),
       assistente	INT(10),
       PRIMARY KEY (giorno,voloId,assistente),
       FOREIGN KEY (giorno)	REFERENCES Viaggi (giorno)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (voloId) 	REFERENCES Viaggi (voloId)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (assistente) REFERENCES Dipendenti (matricola)
                            	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;



/* Crea la tabella Prenotazioni */

CREATE TABLE Prenotazioni (
       id		int(100) AUTO_INCREMENT,
       giorno		DATE,
       voloId	    VARCHAR(7),
       utente		INT(10),
       acquistati	INT(3),
       
       PRIMARY KEY (id),
       FOREIGN KEY (giorno) 	REFERENCES Viaggi (giorno)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (voloId) 	REFERENCES Viaggi (voloId)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (utente) 	REFERENCES Utenti (id)
                            	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Trigger admin */

delimiter $$
CREATE TRIGGER PostiDisp
AFTER INSERT ON Viaggi
FOR EACH ROW
BEGIN
	SET NEW.postiliberi=PostiDisponibili(NEW.aereo);
END;$$
delimiter ;


CREATE FUNCTION PostiDisponibili (aereo INT)
	RETURNS INT DETERMINISTIC
    RETURN SELECT posti FROM Aerei WHERE aereo=matricola;


/*
CREATE VIEW dettagliVolo AS
SELECT  vo.cittaP as Da, vo.cittaA as A, vo.OraP as Partenza, vo.OraA as Arrivo, TIMEDIFF(vo.OraA, vo.OraP) as Durata, pe.nome, pe.cognome, vi.prezzo, vi.postiliberi, a.marca, a.modello
FROM Voli vo, Viaggi vi, Aerei a, Personale pe
WHERE vi.voloId='AZ112' AND vi.dat='2012-08-12' AND vo.numero=vi.voloID AND pe.matricola=vi.comandante AND a.matricola=vi.aereo
*/

LOAD DATA LOCAL INFILE '/Users/msartoretto/Desktop/Airlines/Files db/Prenotazioni.txt' INTO TABLE Prenotazioni FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE '/Users/msartoretto/Desktop/Airlines/Files db/Assistenze.txt' INTO TABLE Assistenze FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE '/Users/msartoretto/Desktop/Airlines/Files db/Viaggi.txt' INTO TABLE Viaggi FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE '/Users/msartoretto/Desktop/Airlines/Files db/Voli.txt' INTO TABLE Voli FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE '/Users/msartoretto/Desktop/Airlines/Files db/Aeroporti.txt' INTO TABLE Aeroporti FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE '/Users/msartoretto/Desktop/Airlines/Files db/Aerei.txt' INTO TABLE Aerei FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE '/Users/msartoretto/Desktop/Airlines/Files db/Stipendiati.txt' INTO TABLE Stipendiati FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE '/Users/msartoretto/Desktop/Airlines/Files db/Dipendenti.txt' INTO TABLE Dipendenti FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
LOAD DATA LOCAL INFILE '/Users/msartoretto/Desktop/Airlines/Files db/Utenti.txt' INTO TABLE Utenti FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
