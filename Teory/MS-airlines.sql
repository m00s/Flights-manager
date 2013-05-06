DROP TABLE IF EXISTS Assistenze;
DROP TABLE IF EXISTS Prenotazioni;
DROP TABLE IF EXISTS Viaggi;
DROP TABLE IF EXISTS Voli;
DROP TABLE IF EXISTS Personale;
DROP TABLE IF EXISTS Utenti;
DROP TABLE IF EXISTS Aeroporti;
DROP TABLE IF EXISTS Aerei;

SET FOREIGN_KEY_CHECKS = 0;

/* Crea la tabella Utenti */

CREATE TABLE Utenti (
       id    	     int(5) AUTO_INCREMENT,
       nome	     	 VARCHAR(15) NOT NULL,
       cognome	     VARCHAR(15) NOT NULL,
       CF	     	 VARCHAR(16) NOT NULL,
       nascita    	 DATE NOT NULL,
       sesso	     ENUM('M','F') DEFAULT "M",
       mail		 	 VARCHAR(25) NOT NULL,
       psw			 VARCHAR(8) NOT NULL,
       telefono	 	 INT(10),
       tipo			 ENUM('guest','admin'),
       UNIQUE (mail),
       PRIMARY KEY(Id)
) ENGINE=InnoDB;


/* Crea la tabella Personale */

CREATE TABLE Personale (
	matricola     VARCHAR(5) PRIMARY KEY,
       nome	     	 VARCHAR(15) NOT NULL,
       cognome	     VARCHAR(15) NOT NULL,
       CF	     	 VARCHAR(16) NOT NULL,
       nascita    	 DATE NOT NULL,
       sesso	     ENUM('M','F'),
       grado		 ENUM('Comandante','Vice','Assistente')
) ENGINE=InnoDB;

/* Crea la tabella Aerei */

CREATE TABLE Aerei (
       matricola     VARCHAR(10) PRIMARY KEY,
       marca	     VARCHAR(10),
       modello	     VARCHAR(25),
       anno	     	 YEAR
) ENGINE=InnoDB;


/* Crea la tabella Aeroporti */

CREATE TABLE Aeroporti (
       codice        VARCHAR(3) PRIMARY KEY,
       nome	     	 VARCHAR(15) NOT NULL,
       citta	     VARCHAR(15) NOT NULL,
       nazione	     VARCHAR(15),
       INDEX (citta)
) ENGINE=InnoDB;


/* Crea la tabella Voli */

CREATE TABLE Voli (
	   numero   VARCHAR(7) PRIMARY KEY,
       oraP		TIME NOT NULL,
       oraA	    TIME NOT NULL,
       cittaP	VARCHAR(15) NOT NULL,
       cittaA	VARCHAR(15) NOT NULL,
       FOREIGN KEY (cittaA) 	REFERENCES Aeroporti (citta)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (cittaP)		REFERENCES Aeroporti (citta)
                            	ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella delle Assistenze */

CREATE TABLE Assistenze (
	   dat     	 DATE,
       voloId	     VARCHAR(7),
       assistente	 VARCHAR(5),
       PRIMARY KEY (dat,voloId,assistente),
       FOREIGN KEY (dat) 		REFERENCES Viaggi (dat)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (voloId) 	REFERENCES Voli (numero)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (assistente) REFERENCES Personale (matricola)
                            	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Viaggi */

CREATE TABLE Viaggi (
	   dat   		DATE,
       voloId	    VARCHAR(7),
       stato		ENUM('effettuato','previsto','soppresso') DEFAULT 'previsto',
       aereo		VARCHAR(10) NOT NULL,
       comandante	VARCHAR(5) NOT NULL,
       vice			VARCHAR(5) NOT NULL,
       prezzo		DECIMAL(10,2),
       postiliberi	INT (3) DEFAULT 0,
       
       CHECK (postiliberi>=0),
       
       PRIMARY KEY (dat,VoloId),
       
       FOREIGN KEY (voloId) 		REFERENCES Voli (numero)
                            		ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (comandante) 	REFERENCES Personale (matricola)
                            		ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (vice) 			REFERENCES Personale (matricola)
                            		ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (aereo) 			REFERENCES Aerei (matricola)
                            		ON DELETE CASCADE ON UPDATE CASCADE               		
) ENGINE=InnoDB;


/* Crea la tabella Prenotazioni */

CREATE TABLE Prenotazioni (
	   dat     	 		DATE,
       voloId	     	VARCHAR(7),
       utente		 	INT(5),
       PRIMARY KEY (dat,voloId,utente),
       FOREIGN KEY (dat) 		REFERENCES Viaggi (dat)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (voloId) 	REFERENCES Voli (numero)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (utente) 	REFERENCES Utenti (id)
                            	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Trigger admin 

delimiter $$
CREATE TRIGGER admin BEFORE DELETE ON account Utenti
FOR EACH ROW
BEGIN
        DECLARE Nuovo int(5);
        
END;$$
delimiter ;
*/

/*
CREATE VIEW dettagliVolo AS
SELECT  vo.cittaP as Da, vo.cittaA as A, vo.OraP as Partenza, vo.OraA as Arrivo, TIMEDIFF(vo.OraA, vo.OraP) as Durata, pe.nome, pe.cognome, vi.prezzo, vi.postiliberi, a.marca, a.modello
FROM Voli vo, Viaggi vi, Aerei a, Personale pe
WHERE vi.voloId='AZ112' AND vi.dat='2012-08-12' AND vo.numero=vi.voloID AND pe.matricola=vi.comandante AND a.matricola=vi.aereo
*/

LOAD DATA LOCAL INFILE '/Users/massimilianosartoretto/Desktop/txt airlines/Personale.txt' INTO TABLE Personale;
LOAD DATA LOCAL INFILE '/Users/massimilianosartoretto/Desktop/txt airlines/Aeroporti.txt' INTO TABLE Aeroporti;
LOAD DATA LOCAL INFILE '/Users/massimilianosartoretto/Desktop/txt airlines/Utenti.txt' INTO TABLE Utenti;
LOAD DATA LOCAL INFILE '/Users/massimilianosartoretto/Desktop/txt airlines/Aerei.txt' INTO TABLE Aerei;
LOAD DATA LOCAL INFILE '/Users/massimilianosartoretto/Desktop/txt airlines/Voli.txt' INTO TABLE Voli;
LOAD DATA LOCAL INFILE '/Users/massimilianosartoretto/Desktop/txt airlines/Viaggi.txt' INTO TABLE Viaggi;
LOAD DATA LOCAL INFILE '/Users/massimilianosartoretto/Desktop/txt airlines/Assistenze.txt' INTO TABLE Assistenze;
LOAD DATA LOCAL INFILE '/Users/massimilianosartoretto/Desktop/txt airlines/Prenotazioni.txt' INTO TABLE Prenotazioni;