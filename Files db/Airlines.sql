/*DROP SCHEMA IF EXISTS Airlines;
CREATE SCHEMA Airlines;
USE Airlines;*/
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS Anagrafiche;
DROP TABLE IF EXISTS Utenti;
DROP TABLE IF EXISTS Dipendenti;
DROP TABLE IF EXISTS Aerei;
DROP TABLE IF EXISTS Luoghi;
DROP TABLE IF EXISTS Aeroporti;
DROP TABLE IF EXISTS Compagnie;
DROP TABLE IF EXISTS Bagagli;
DROP TABLE IF EXISTS TariffeBagagli;
DROP TABLE IF EXISTS Tratte;
DROP TABLE IF EXISTS Voli;
DROP TABLE IF EXISTS Viaggi;
DROP TABLE IF EXISTS Scali;
DROP TABLE IF EXISTS Offerte;
DROP TABLE IF EXISTS Assistenze;
DROP TABLE IF EXISTS Posti;
DROP TABLE IF EXISTS Prenotazioni;
DROP TABLE IF EXISTS ViaggiVoli;
DROP TABLE IF EXISTS ViaggiAerei;
DROP TABLE IF EXISTS DettagliViaggi;

/* Crea la tabella Anagrafiche */

CREATE TABLE Anagrafiche (
	idAnag		INT AUTO_INCREMENT PRIMARY KEY,
	nome			VARCHAR(15) NOT NULL,
	cognome		VARCHAR(15) NOT NULL,
	nascita		DATE NOT NULL,
	sesso		ENUM('M','F') DEFAULT "M",
	email		VARCHAR(25),
	UNIQUE (email)
) ENGINE=InnoDB;

/* Crea la tabella Utenti */

CREATE TABLE Utenti (
	idAnag		INT PRIMARY KEY,
	password		VARCHAR(40) NOT NULL,
	type         ENUM('Guest','Admin') DEFAULT "Guest",
	FOREIGN KEY (idAnag) 	REFERENCES Anagrafiche (idAnag)
				ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Dipendenti */

CREATE TABLE Dipendenti (
	   idAnag		INT PRIMARY KEY,
	   matricola	INT(10),
	   grado		ENUM('assistente','comandante','vice'),
	   idCompagnia	INT,	
	   UNIQUE (matricola),
	   FOREIGN KEY (idAnag) REFERENCES Anagrafiche (idAnag)
				ON UPDATE CASCADE,
	   FOREIGN KEY (idCompagnia)	REFERENCES Compagnie (idCompagnia)
					ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Aerei */

CREATE TABLE Aerei (
	matricola	VARCHAR(10) PRIMARY KEY,
	marca		VARCHAR(10),
	modello		VARCHAR(25),
	anno			YEAR,
	postiPrima	INT(3),
	postiSeconda	INT(3),
	idCompagnia	INT,
	FOREIGN KEY (idCompagnia) REFERENCES Compagnie (idCompagnia)
				  ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Luoghi */

CREATE TABLE Luoghi (
	idLuogo			INT AUTO_INCREMENT PRIMARY KEY,
	nomecitta	VARCHAR(15) NOT NULL,
	nazione		VARCHAR(15)
) ENGINE=InnoDB;

/* Crea la tabella Aeroporti */

CREATE TABLE Aeroporti (
	idAeroporto	INT AUTO_INCREMENT PRIMARY KEY,
	nome	     	VARCHAR(15) NOT NULL,
	idLuogo		INT NOT NULL,
	FOREIGN KEY (idLuogo)	REFERENCES Luoghi (idLuogo)
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
	peso		INT(2)
)ENGINE=InnoDB;

/* Crea la tabella TariffeBagaglio*/

CREATE TABLE TariffeBagagli(
	idBagaglio	INT,
	idCompagnia	INT,
	prezzo		INT,
	PRIMARY KEY(idBagaglio,idCompagnia),
	FOREIGN KEY(idBagaglio) REFERENCES Bagagli(idBagaglio) 
				ON UPDATE CASCADE,
	FOREIGN KEY(idCompagnia) REFERENCES Compagnie(idCompagnia)
				 ON UPDATE CASCADE
)ENGINE=InnoDB;



/* Crea la tabella Tratta */

CREATE TABLE Tratte (
	idTratta	INT AUTO_INCREMENT PRIMARY KEY,
	da			INT,
	a			INT,
	FOREIGN KEY (a) REFERENCES Aeroporti (idAeroporto)
		        ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (da) REFERENCES Aeroporti (idAeroporto)
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
	FOREIGN KEY(idTratta) 	REFERENCES Tratte (idTratta) 
				ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Crea la tabella Viaggi */

CREATE TABLE Viaggi (
	idViaggio	INT AUTO_INCREMENT PRIMARY KEY,
	giorno		DATE,
	stato		ENUM('effettuato','previsto','soppresso') DEFAULT 'previsto',
	comandante	INT(10), 
	vice			INT(10),
	InseritoDa   INT,       
	FOREIGN KEY (InseritoDa) REFERENCES Utenti (idAnag)
                                 ON UPDATE CASCADE,
	FOREIGN KEY (comandante) REFERENCES Dipendenti (matricola)
				 ON UPDATE CASCADE,
	FOREIGN KEY (vice) REFERENCES Dipendenti (matricola)
			   ON UPDATE CASCADE              		
) ENGINE=InnoDB;


/* Crea la tabella Scali */

CREATE TABLE Scali(
	idViaggio		INT,
	idAeroporto		INT,
	ordine			INT,
	PRIMARY KEY(idViaggio,idAeroporto),
	FOREIGN KEY(idViaggio) 		REFERENCES Viaggi (idViaggio),
	FOREIGN KEY(idAeroporto)	REFERENCES Aeroporti (idAeroporto)
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


/* Crea la tabella Posti */

CREATE TABLE Posti(
	idPosto		INT AUTO_INCREMENT PRIMARY KEY,
	numero		VARCHAR(3),
	aereo		VARCHAR(10),
	FOREIGN KEY (aereo) 		REFERENCES Aerei (matricola)
                            	ON DELETE CASCADE ON UPDATE CASCADE  

)ENGINE=InnoDB;

/* Crea la tabella Prenotazioni */

CREATE TABLE Prenotazioni (
	idPrenotazione	INT(100) AUTO_INCREMENT PRIMARY KEY,
	idViaggio	INT,
	acquirente	INT,
	passeggero	INT,
	numeroBagagli	INT(3),
	type		ENUM('prima','seconda') DEFAULT 'seconda',
	stato		ENUM('valido','annullato','rimborsato') DEFAULT 'valido',
	idPosto		INT,
	FOREIGN KEY (idPosto)	REFERENCES Posti (idPosto) 
				ON UPDATE CASCADE,
	FOREIGN KEY (idViaggio)	REFERENCES Viaggi (idViaggio)
                            	ON UPDATE CASCADE,
	FOREIGN KEY (acquirente)REFERENCES Utenti (idAnag)
                            	ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (passeggero)REFERENCES Anagrafiche (idAnag)
                            	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


/* Crea la tabella ViaggiVoli*/

CREATE TABLE ViaggiVoli(
	idViaggio		INT,
	idVolo			VARCHAR(7),
	PRIMARY KEY(idViaggio,idVolo),
	FOREIGN KEY (idViaggio)	REFERENCES Viaggi(idViaggio)
				ON UPDATE CASCADE,
	FOREIGN KEY (idVolo)	REFERENCES Voli(numero)
				ON UPDATE CASCADE
)ENGINE=InnoDB;


/* Crea la tabella ViaggiAerei*/

CREATE TABLE ViaggiAerei(
	idViaggio		INT,
	aereo			VARCHAR(10),
	PRIMARY KEY(idViaggio,aereo),
	FOREIGN KEY (idViaggio)	REFERENCES Viaggi(idViaggio)
							ON UPDATE CASCADE,
	FOREIGN KEY (aereo)		REFERENCES Aerei(matricola)
							ON UPDATE CASCADE
)ENGINE=InnoDB;


CREATE TABLE DettagliViaggi(
	idViaggio		INT,
	aereo			VARCHAR(10),
	idVolo			VARCHAR(7),
	PRIMARY KEY(idViaggio, idVolo, aereo),
	FOREIGN KEY (idViaggio)	REFERENCES Viaggi(idViaggio)
							ON UPDATE CASCADE,
	FOREIGN KEY (idVolo)	REFERENCES Voli(numero)
				ON UPDATE CASCADE,
	FOREIGN KEY (aereo)		REFERENCES Aerei(matricola)
							ON UPDATE CASCADE
)ENGINE=InnoDB;



/*
LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/Filesdb/Assistenze.txt' INTO TABLE Assistenze FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n' IGNORE 4 LINES;
*/
SET FOREIGN_KEY_CHECKS = 1;

