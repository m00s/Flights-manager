DROP SCHEMA IF EXISTS Airlines;
CREATE SCHEMA Airlines;
USE Airlines;/*
DROP TABLE IF EXISTS Passeggeri;
DROP TABLE IF EXISTS Tratte;
DROP TABLE IF EXISTS Scali;
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
*/
SET FOREIGN_KEY_CHECKS = 0;

/* Crea la tabella Anagrafiche */

CREATE TABLE Anagrafiche (
	   idAnag		INT AUTO_INCREMENT PRIMARY KEY,
       nome			VARCHAR(15) NOT NULL,
       cognome		VARCHAR(15) NOT NULL,
       nascita		DATE NOT NULL,
       sesso		ENUM('M','F') DEFAULT "M"
) ENGINE=InnoDB;

/* Crea la tabella Utenti */

CREATE TABLE Utenti (
	   idAnag		INT PRIMARY KEY,
       email		VARCHAR(25),
       password		VARCHAR(40) NOT NULL,
       type         ENUM('Guest','Admin') DEFAULT "Guest",
	   UNIQUE (email),
       FOREIGN KEY (idAnag) 	REFERENCES Anagrafiche (idAnag)
								ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Dipendenti */

CREATE TABLE Dipendenti (
	   idAnag		INT PRIMARY KEY,
	   email		VARCHAR(25),
	   matricola	INT(10),
	   grado		ENUM('assistente','comandante','vice'),
	   idCompagnia	INT,	
	   UNIQUE (matricola),
	   UNIQUE (email),
	   FOREIGN KEY (idAnag) 		REFERENCES Anagrafiche (idAnag)
									ON UPDATE CASCADE,
	   FOREIGN KEY (idCompagnia)	REFERENCES Compagnie (idCompagnia)
									ON UPDATE CASCADE
) ENGINE=InnoDB;

/*Crea la tabella Passeggeri*/

CREATE TABLE Passeggeri(
	idAnag		INT PRIMARY KEY,
	tipo		ENUM('adulto','bambino') DEFAULT 'adulto',	
	FOREIGN KEY (idAnag)		REFERENCES Anagrafiche (idAnag)
								ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

/* Crea la tabella Aerei */

CREATE TABLE Aerei (
       matricola	VARCHAR(10) PRIMARY KEY,
       marca		VARCHAR(10),
       modello		VARCHAR(25),
       anno			YEAR,
       postiPrima	INT(3),
       postiSeconda	INT(3),
	   idCompagnia	INT,
	   FOREIGN KEY (idCompagnia)	REFERENCES Compagnie (idCompagnia)
									ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Luoghi */

CREATE TABLE Luoghi (
       idLuogo			INT AUTO_INCREMENT,
       nomecitta	VARCHAR(15) NOT NULL,
       nazione		VARCHAR(15),
       PRIMARY KEY(idLuogo)
) ENGINE=InnoDB;

/* Crea la tabella Aeroporti */

CREATE TABLE Aeroporti (
       idAeroporto	INT AUTO_INCREMENT,
       nome	     	VARCHAR(15) NOT NULL,
       idLuogo		INT NOT NULL,
       PRIMARY KEY(idAeroporto),
       FOREIGN KEY (idLuogo) 	REFERENCES Luoghi (idLuogo)
								ON UPDATE CASCADE
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
	idBagaglio	INT AUTO_INCREMENT PRIMARY KEY,
	peso		INT(2),
	prezzo		INT(3),
	idCompagnia	INT,
	FOREIGN KEY (idCompagnia)	REFERENCES Compagnie (idCompagnia)
								ON DELETE CASCADE  ON UPDATE CASCADE

)ENGINE=InnoDB;

/* Crea la tabella Tratta */

CREATE TABLE Tratte (
	idTratta	INT AUTO_INCREMENT PRIMARY KEY,
	da			INT,
	a			INT,
	FOREIGN KEY (a) 		REFERENCES Aeroporti (idAeroporto)
                        	ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (da)		REFERENCES Aeroporti (idAeroporto)
                            ON DELETE CASCADE  ON UPDATE CASCADE
)ENGINE=InnoDB;

/* Crea la tabella Voli */

CREATE TABLE Voli (
       numero	VARCHAR(7) PRIMARY KEY,
       oraP		TIME NOT NULL,
       oraA		TIME NOT NULL,
       idTratta	INT,
       idCompagnia	INT,
       FOREIGN KEY (idCompagnia)REFERENCES Compagnie (idCompagnia)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
		FOREIGN KEY(idTratta) 	REFERENCES Tratte (idTratta) ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Scali */

CREATE TABLE Scali(
	idTratta		INT,
	idAeroporto		INT,
	ordine			INT,
	PRIMARY KEY(idTratta,idAeroporto),
	FOREIGN KEY(idTratta) 		REFERENCES Tratte (idTratta),
	FOREIGN KEY(idAeroporto)	REFERENCES Aeroporti (idAeroporto)
)ENGINE=InnoDB;


/* Crea la tabella Viaggi */

CREATE TABLE Viaggi (
       idViaggio	INT AUTO_INCREMENT,
	   idCompBase	INT,
	   idCompEsec	INT,
       giorno		DATE,
       idTratta		INT,
       stato		ENUM('effettuato','previsto','soppresso') DEFAULT 'previsto',
       aereo		VARCHAR(10),
       comandante	INT(10), 
       vice			INT(10),
       InseritoDa   INT,
       
       PRIMARY KEY (idViaggio),
       FOREIGN KEY (idCompBase) REFERENCES Compagnie (idCompagnia)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (idCompEsec) REFERENCES Compagnie (idCompagnia)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
       FOREIGN KEY (InseritoDa)   REFERENCES Utenti (idAnag)
                                  ON UPDATE CASCADE,
       FOREIGN KEY (idTratta) 	REFERENCES Tratte (idTratta)
                            	ON UPDATE CASCADE,
       FOREIGN KEY (comandante) REFERENCES Dipendenti (matricola)
                            	ON UPDATE CASCADE,
       FOREIGN KEY (vice) 		REFERENCES Dipendenti (matricola)
                            	ON UPDATE CASCADE,
       FOREIGN KEY (aereo) 		REFERENCES Aerei (matricola)
                            	ON UPDATE CASCADE               		
) ENGINE=InnoDB;

/* Crea la tabella Tariffe */

CREATE TABLE Tariffe(
	idTariffa		INT AUTO_INCREMENT PRIMARY KEY,
	idViaggio 		INT,
	aereo			VARCHAR(10),
	prezzoPrima		INT(4),
	prezzoSeconda	INT(4)NOT NULL,
	ridottoperc	INT,
	idCompagnia	INT,
	FOREIGN KEY (idCompagnia)	REFERENCES Compagnie (idCompagnia)
                            	ON DELETE CASCADE  ON UPDATE CASCADE,
	FOREIGN KEY (idViaggio)		REFERENCES Viaggi (idViaggio)
                            	ON UPDATE CASCADE, 
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
                            	ON UPDATE CASCADE
) ENGINE=InnoDB;


/* Crea la tabella delle Assistenze */

CREATE TABLE Assistenze (
       idViaggio	INT,
       matricola	INT(10),
       PRIMARY KEY (idViaggio,matricola),
       FOREIGN KEY (idViaggio) 	REFERENCES Viaggi (idViaggio)
                            	ON UPDATE CASCADE,
       FOREIGN KEY (matricola) REFERENCES Dipendenti (matricola)
                            	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;



/* Crea la tabella Prenotazioni */

CREATE TABLE Prenotazioni (
       idPrenotazione	INT(100) AUTO_INCREMENT,
       idViaggio		INT,
       acquirente		INT,
	   passeggero		INT,
	   numeroBagagli	INT(3),
	   type				ENUM('prima','seconda') DEFAULT 'seconda',
	   stato			ENUM('valido','annullato','rimborsato') DEFAULT 'valido',
       PRIMARY KEY (idPrenotazione),
       
       FOREIGN KEY (idViaggio) 	REFERENCES Viaggi (idViaggio)
                            	ON UPDATE CASCADE,
       FOREIGN KEY (acquirente) 	REFERENCES Utenti (idAnag)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
		FOREIGN KEY (passeggero) 	REFERENCES Passeggeri (idAnag)
                            	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Posti */

CREATE TABLE Posti(
	idPosto		INT AUTO_INCREMENT PRIMARY KEY,
	numero		VARCHAR(3),
	aereo		VARCHAR(10),
	FOREIGN KEY (aereo) 		REFERENCES Aerei (matricola)
                            	ON UPDATE CASCADE  

)ENGINE=InnoDB;
/*
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

