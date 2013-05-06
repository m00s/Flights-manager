/* Creazione dello schema Persone per la prima esercitazione di laboratorio */

/* Ripulisce, eliminando le tabelle qualora esistessero gia` */

DROP TABLE IF EXISTS Genitori;
DROP TABLE IF EXISTS Persone;


/* Crea la tabella delle Persone */

CREATE TABLE Persone (
       Id    	     CHAR(2) PRIMARY KEY,
       Nome	     VARCHAR(10) NOT NULL,
       Reddito	     INT 	 DEFAULT 0,
       Eta	     TINYINT,
       Sesso	     ENUM('M','F')
) ENGINE=InnoDB;


/* Crea la tabella Genitori */

CREATE TABLE Genitori (
       Figlio	  CHAR(2),
       Genitore   CHAR(2),
       PRIMARY KEY (Figlio,Genitore),
       FOREIGN KEY (Figlio) REFERENCES Persone (Id)
                            ON DELETE CASCADE,
       FOREIGN KEY (Genitore) REFERENCES Persone (Id)
                            ON DELETE CASCADE
) ENGINE=InnoDB;



/* Popola le tabelle */

INSERT INTO Persone  VALUES 
       ('A1','Aldo',25,15,'M'),
       ('A2','Andrea',27,21,'M'),
       ('A3','Antonino',44,40,'M'),
       ('A7','Aldo',25,20,'M'),
       ('M1','Marco',11,10,'M'),
       ('F1','Filippo',26,30,'M'),
       ('F2','Franco',60,20,'M'),
       ('L1','Leonardo',79,30,'M'),
       ('L2','Luigi',50,40,'M'),
       ('M2','Michele',79,30,'M'),
       ('S1','Sergio',85,35,'M'),
       ('A4','Amelia',79,28,'F'),
       ('A5','Anna',50,29,'F'),
       ('A6','AnnaMaria',41,30,'F'),
       ('B1','Beatrice',79,30,'F'),
       ('L3','Luisa',75,87,'F'),
       ('M3','Maria',55,42,'F'),
       ('O1','Olga',30,41,'F');


INSERT INTO Genitori VALUES 
       ('A1','F2'),
       ('A1','M3'),
       ('A2','F2'),
       ('A2','M3'),
       ('A3','B1'),
       ('A3','L1'),
       ('M1','A6'),
       ('M1','A3'),
       ('F1','A5'),
       ('F1','L2'),
       ('F2','S1'),
       ('L2','L3'),
       ('A6','A4'),
       ('A6','M2'),
       ('M3','L3'),
       ('O1','A5'),
       ('O1','L2'),
       ('A7','A6'),
       ('A7','A3');
