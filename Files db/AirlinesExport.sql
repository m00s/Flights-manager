-- phpMyAdmin SQL Dump
-- version 3.5.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: Lug 03, 2013 alle 15:39
-- Versione del server: 5.5.25
-- Versione PHP: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Airlines`
--

DELIMITER $$
--
-- Procedure
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `eliminaUtente`(IN idAnagraf INT)
BEGIN
	DELETE FROM Prenotazioni WHERE acquirente=idAnagraf OR passeggero=idAnagraf;
	DELETE FROM	Utenti WHERE idAnag=idAnagraf;
	DELETE FROM Anagrafiche WHERE idAnag=idAnagraf;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InserisciUtente`(IN nome VARCHAR(15),IN cognome VARCHAR(15),IN nascita DATE,IN sesso VARCHAR(1),IN mail VARCHAR(25),
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InserisciViaggio`(IN Volo VARCHAR(7),IN giorno DATE,IN prezzoPrima INT,IN prezzoSeconda INT, IN idTratta INT,IN inseritoDa INT,
					IN compagnia INT,IN aereo VARCHAR(10),IN comandante INT(10), IN vice INT(10), IN ridottoPerc INT)
BEGIN
	DECLARE ultimoId INT;
	INSERT INTO Viaggi (giorno, prezzoPrima, prezzoSeconda, postiPrima, postiSeconda, idTratta, inseritoDa) VALUES
						(giorno, prezzoPrima, prezzoSeconda, getPosti(0,aereo), getPosti(1,aereo), idTratta, inseritoDa);
	SELECT MAX(IdViaggio) INTO ultimoId FROM Viaggi;
	INSERT INTO ViaggiDiretti VALUES (ultimoId, Volo, aereo, comandante, vice, ridottoPerc, compagnia);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `inserisciViaggioConScali`(IN giorno DATE,IN prezzoPrima INT,IN prezzoSeconda INT,IN postiPrima INT,
IN postiSeconda INT,IN idTratta INT,IN inseritoDa INT,OUT VIAGGIO INT)
BEGIN
	INSERT INTO Viaggi (giorno, prezzoPrima, prezzoSeconda, postiPrima, postiSeconda, idTratta, inseritoDa) VALUES
						(giorno, prezzoPrima, prezzoSeconda, postiPrima, postiSeconda, idTratta, inseritoDa);
	SELECT MAX(IdViaggio) INTO VIAGGIO FROM Viaggi;
	INSERT INTO ViaggiConScali VALUES (VIAGGIO);
END$$

--
-- Funzioni
--
CREATE DEFINER=`root`@`localhost` FUNCTION `getPosti`(classe BOOL, aereo VARCHAR(10)) RETURNS int(11)
BEGIN
	DECLARE Posti INT;
	IF(classe=0) THEN 
		SELECT postiPrima INTO Posti FROM Aerei WHERE matricola=aereo;
	ELSE 
		SELECT postiSeconda INTO Posti FROM Aerei WHERE matricola=aereo;
	END IF;
	RETURN Posti;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `Aerei`
--

CREATE TABLE IF NOT EXISTS `Aerei` (
  `matricola` varchar(10) NOT NULL,
  `marca` varchar(10) DEFAULT NULL,
  `modello` varchar(25) DEFAULT NULL,
  `anno` year(4) DEFAULT NULL,
  `postiPrima` int(3) DEFAULT NULL,
  `postiSeconda` int(3) DEFAULT NULL,
  `idCompagnia` int(11) NOT NULL,
  PRIMARY KEY (`matricola`),
  KEY `idCompagnia` (`idCompagnia`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `Aerei`
--

INSERT INTO `Aerei` (`matricola`, `marca`, `modello`, `anno`, `postiPrima`, `postiSeconda`, `idCompagnia`) VALUES
('N-014', 'Airbus', 'A380', 2012, 15, 200, 1),
('N-01AZ', 'Boeing', '737-800', 2010, 0, 250, 1),
('N-02AZ', 'Boeing', '737-800', 2011, 0, 250, 1),
('N-04AZ', 'Airbus', 'A392', 2010, 0, 350, 2),
('N-429', 'Airbus', 'A319', 2013, 0, 350, 2),
('N-453HY', 'Airbus', 'A330', 2012, 0, 400, 7),
('N-4BA', 'Boeing', '747', 2010, 0, 500, 3),
('N-659', 'Airbus', 'A320', 2011, 0, 280, 3),
('N-760PO', 'Airbus', 'A750', 2013, 15, 370, 8),
('N-765BN', 'Boeing', '737', 2012, 0, 250, 6),
('N-765ZR', 'Boeing', 'B870', 2012, 10, 400, 8),
('N-860DF', 'Airbus', 'A310', 2011, 0, 270, 7),
('N-870AA', 'Boeing', '747', 2012, 10, 400, 5),
('N-980AB', 'Boeing', '727', 2013, 10, 350, 5),
('N-981CF', 'Boeing', 'B787', 2011, 0, 300, 6),
('N-999', 'Airbus', 'A319', 2010, 0, 150, 4),
('N-9AZ', 'Boeing', '725', 2012, 10, 350, 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `Aeroporti`
--

CREATE TABLE IF NOT EXISTS `Aeroporti` (
  `idAeroporto` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(40) NOT NULL,
  `idLuogo` int(11) NOT NULL,
  PRIMARY KEY (`idAeroporto`),
  KEY `idLuogo` (`idLuogo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Dump dei dati per la tabella `Aeroporti`
--

INSERT INTO `Aeroporti` (`idAeroporto`, `nome`, `idLuogo`) VALUES
(1, 'Marco Polo', 1),
(2, 'Canova', 2),
(3, 'Termini', 3),
(4, 'Linate', 4),
(5, 'Edward Lawrence Logan', 5),
(6, 'Stansted', 6),
(7, 'Internazionale di Napoli', 7),
(8, 'Mario Mameli di Elmas', 8),
(9, 'Sandro Pertini', 9),
(10, 'San Giacomo', 10),
(11, 'Vienna-Schwechat', 11),
(12, 'Charles De Gaulle', 12),
(13, 'Schönefeld', 13),
(14, 'Barajas', 14),
(15, 'El prat', 15),
(16, 'Mosca-Domodedovo', 16),
(17, 'Schiphol', 17),
(18, 'Ataturk', 18),
(19, 'Intl', 19),
(20, 'Rabat-Salé', 20),
(21, 'Internazionale di Tripoli', 21),
(22, 'Jomo Kenyatta', 22),
(23, 'Sir Seewoosagur Ramgoolam', 23),
(24, 'Internazionale di Victoria', 24),
(25, 'Internazionale di Doha', 25),
(26, 'Changi', 26),
(27, 'Internazionale di Tokyo', 27),
(28, 'Beijing', 28),
(29, 'Chek Lap Kok', 29),
(30, 'Soekarno-Hatta', 30),
(31, 'Kingsford Smith', 31),
(32, 'Internazionale di Canberra', 32),
(33, 'Internazionale di Cancun', 33),
(34, 'General Abelardo L. Rodríguez', 34),
(35, 'Ministro Pistarini', 35),
(36, 'Rio de Janeiro-Galeão', 36),
(37, 'John Fitzgerald Kennedy', 37),
(38, 'Internazionale di Miami', 38),
(39, 'Logan International Airport', 39),
(40, 'Chicago-O''Hare', 40),
(41, 'Internazionale di Los Angeles', 41),
(42, 'Dillingham Airfield', 42),
(43, 'Oslo-Gardermoen', 43),
(44, 'Helsinki-Vantaa', 44),
(45, 'Lisbona-Portela', 45),
(46, 'Stoccolma-Skavsta', 46);

-- --------------------------------------------------------

--
-- Struttura della tabella `Anagrafiche`
--

CREATE TABLE IF NOT EXISTS `Anagrafiche` (
  `idAnag` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(15) NOT NULL,
  `cognome` varchar(15) NOT NULL,
  `nascita` date NOT NULL,
  `sesso` enum('M','F') DEFAULT 'M',
  `email` varchar(25) DEFAULT NULL,
  `tipo` enum('adulto','bambino') DEFAULT 'adulto',
  PRIMARY KEY (`idAnag`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

--
-- Dump dei dati per la tabella `Anagrafiche`
--

INSERT INTO `Anagrafiche` (`idAnag`, `nome`, `cognome`, `nascita`, `sesso`, `email`, `tipo`) VALUES
(1, 'Marco', 'Curo', '1990-06-09', 'M', 'a@dom.it', 'adulto'),
(2, 'Maria', 'Rossi', '1990-06-09', 'M', 'b@dom.it', 'adulto'),
(3, 'Mirko', 'Pavanello', '1990-05-13', 'M', 'p@admin.it', 'adulto'),
(4, 'Massimiliano', 'Sartoretto', '1991-09-20', 'M', 's@admin.it', 'adulto'),
(5, 'Franco', 'Stanly', '1970-06-09', 'M', 'e@dom.it', 'adulto'),
(6, 'Angelo', 'Duro', '1985-08-09', 'M', 'f@dom.it', 'adulto'),
(7, 'Marta', 'Stolta', '1993-12-12', 'F', 'g@dom.it', 'adulto'),
(8, 'Monica', 'Morsi', '1989-10-09', 'F', 'h@dom.it', 'adulto'),
(9, 'Milly', 'Verdi', '1986-10-09', 'F', 'i@dom.it', 'adulto'),
(10, 'Luca', 'Martini', '1975-01-31', 'M', 'j@dom.it', 'adulto'),
(11, 'Dio', 'Lupacchiotto', '1980-06-09', 'M', 'k@dom.it', 'adulto'),
(12, 'Aldo', 'Baglio', '1975-07-19', 'M', 'l@dom.it', 'adulto'),
(13, 'Antonino', 'Siomioni', '1987-09-07', 'M', 'm@dom.it', 'adulto'),
(14, 'Ramboso', 'Grinton', '1989-12-12', 'M', 'n@dom.it', 'adulto'),
(15, 'Mattia', 'Agostinetto', '1988-12-21', 'M', 'o@dom.it', 'adulto'),
(16, 'Marta', 'Micheli', '1979-10-09', 'F', 'p@dom.it', 'adulto'),
(17, 'Francesca', 'Lusi', '1989-02-17', 'F', 'q@dom.it', 'adulto'),
(18, 'Domenico', 'Giammarinaro', '1945-12-16', 'M', 'r@dom.it', 'adulto'),
(19, 'Francesco Saver', 'Borrelli', '1957-11-05', 'M', 's@dom.it', 'adulto'),
(20, 'Vittoria', 'Lisi', '1978-12-15', 'F', 't@dom.it', 'adulto'),
(21, 'Martin', 'Scalfaro', '1984-06-04', 'M', 'u@dom.it', 'adulto'),
(22, 'Lucia', 'Marchetti', '1986-08-07', 'F', 'v@dom.it', 'adulto'),
(23, 'Marco', 'Alesci', '1990-09-16', 'M', 'z@dom.it', 'adulto'),
(24, 'Marylin', 'Fruscio', '1981-10-12', 'M', 'aa@dom.it', 'adulto'),
(25, 'Federica', 'Luisi', '1976-10-23', 'F', 'cam@dom.it', 'adulto'),
(26, 'Fulvio', 'Rosina', '1987-12-28', 'M', 'fulv@dom.it', 'adulto'),
(27, 'Vitali', 'Rominov', '1986-12-31', 'M', 'vit@dom.it', 'adulto'),
(28, 'Maria', 'Venes', '1965-10-12', 'F', 'ven@dom.it', 'adulto'),
(29, 'Marzio', 'Dance', '1960-09-18', 'M', 'dj@dom.it', 'adulto'),
(30, 'Michela', 'De Bortoli', '1958-12-29', 'F', 'micdb@dom.it', 'adulto'),
(31, 'Tania', 'Marrone', '1949-10-19', 'F', 'tanya@dom.it', 'adulto'),
(32, 'James', 'Pallotta', '1976-02-09', 'M', 'jp@dom.it', 'adulto'),
(33, 'Danny', 'Ferri', '1968-10-18', 'M', 'sacre@dom.it', 'adulto'),
(34, 'Trap', 'Forensic', '1965-12-24', 'M', 'tpf@dom.it', 'adulto'),
(35, 'Anita', 'Argentero', '1978-07-23', 'F', 'asi@dom.it', 'adulto'),
(36, 'Anita', 'Blonde', '1987-10-29', 'F', 'anits@dom.it', 'adulto');

-- --------------------------------------------------------

--
-- Struttura della tabella `Assistenze`
--

CREATE TABLE IF NOT EXISTS `Assistenze` (
  `idViaggio` int(11) NOT NULL DEFAULT '0',
  `matricola` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idViaggio`,`matricola`),
  KEY `matricola` (`matricola`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Bagagli`
--

CREATE TABLE IF NOT EXISTS `Bagagli` (
  `idBagaglio` int(11) NOT NULL AUTO_INCREMENT,
  `peso` int(2) DEFAULT NULL,
  PRIMARY KEY (`idBagaglio`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `Compagnie`
--

CREATE TABLE IF NOT EXISTS `Compagnie` (
  `idCompagnia` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(30) DEFAULT NULL,
  `numTel` varchar(25) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `nazione` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idCompagnia`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dump dei dati per la tabella `Compagnie`
--

INSERT INTO `Compagnie` (`idCompagnia`, `nome`, `numTel`, `email`, `nazione`) VALUES
(1, 'EasyJet', '+393338765123', 'info@easyjet.com', 'Inghilterra'),
(2, 'Lufthansa', '+393338765019', 'info@lufthansa.com', 'Germania'),
(3, 'Alitalia', '+393338765876', 'info@alitalia.com', 'Italia'),
(4, 'Airfrance', '+393338765346', 'info@airfrance.com', 'Francia'),
(5, 'Air Arabia', '+393338712354', 'info@airarabia.com', 'Arabia Saudita'),
(6, 'Ryanair', '+393338765456', 'info@ryanair.com', 'Irlanda'),
(7, 'Air Asia', '0229319093', 'airasia@info.com', 'Malesia'),
(8, 'Air Berlin', '0219823823', 'airberlin@info.com', 'Germania');

-- --------------------------------------------------------

--
-- Struttura della tabella `Dipendenti`
--

CREATE TABLE IF NOT EXISTS `Dipendenti` (
  `idAnag` int(11) NOT NULL,
  `matricola` int(10) DEFAULT NULL,
  `grado` enum('assistente','comandante','vice') DEFAULT NULL,
  `idCompagnia` int(11) NOT NULL,
  PRIMARY KEY (`idAnag`),
  UNIQUE KEY `matricola` (`matricola`),
  KEY `idCompagnia` (`idCompagnia`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `Dipendenti`
--

INSERT INTO `Dipendenti` (`idAnag`, `matricola`, `grado`, `idCompagnia`) VALUES
(5, 219862369, 'vice', 1),
(6, 219851669, 'vice', 2),
(7, 1009119202, 'comandante', 1),
(8, 1234567878, 'comandante', 2),
(9, 2147483647, 'assistente', 1),
(10, 923781912, 'assistente', 2),
(11, 987483647, 'assistente', 1),
(12, 765465437, 'assistente', 2),
(13, 912483647, 'comandante', 3),
(14, 453657689, 'vice', 3),
(15, 534283647, 'assistente', 3),
(16, 876354610, 'assistente', 3),
(17, 764954610, 'comandante', 4),
(18, 980474610, 'vice', 4),
(19, 546353899, 'assistente', 4),
(20, 356278009, 'assistente', 4),
(21, 198276354, 'comandante', 5),
(22, 654283765, 'vice', 5),
(23, 455372689, 'assistente', 5),
(24, 119378298, 'assistente', 5),
(25, 654387899, 'comandante', 6),
(26, 109899765, 'vice', 6),
(27, 176358928, 'assistente', 6),
(28, 87629376, 'assistente', 6),
(29, 566473983, 'comandante', 7),
(30, 564783902, 'vice', 7),
(31, 177653987, 'assistente', 7),
(32, 109498793, 'assistente', 7),
(33, 765398728, 'comandante', 8),
(34, 165278938, 'vice', 8),
(35, 109876387, 'assistente', 8),
(36, 100937746, 'assistente', 8);

-- --------------------------------------------------------

--
-- Struttura della tabella `Luoghi`
--

CREATE TABLE IF NOT EXISTS `Luoghi` (
  `idLuogo` int(11) NOT NULL AUTO_INCREMENT,
  `nomecitta` varchar(40) NOT NULL,
  `nazione` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`idLuogo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Dump dei dati per la tabella `Luoghi`
--

INSERT INTO `Luoghi` (`idLuogo`, `nomecitta`, `nazione`) VALUES
(1, 'Venezia', 'Italia'),
(2, 'Treviso', 'Italia'),
(3, 'Roma', 'Italia'),
(4, 'Milano', 'Italia'),
(5, 'Boston', 'USA'),
(6, 'Londra', 'Inghilterra'),
(7, 'Napoli', 'Italia'),
(8, 'Cagliari', 'Italia'),
(9, 'Torino', 'Italia'),
(10, 'Bolzano', 'Italia'),
(11, 'Vienna', 'Austria'),
(12, 'Parigi', 'Francia'),
(13, 'Berlino', 'Germania'),
(14, 'Madrid', 'Spagna'),
(15, 'Barcellona', 'Spagna'),
(16, 'Mosca', 'Russia'),
(17, 'Amsterdam', 'Olanda'),
(18, 'Istanbul', 'Turchia'),
(19, 'Il Cairo', 'Egitto'),
(20, 'Rabat', 'Marocco'),
(21, 'Tripoli', 'Libia'),
(22, 'Nairobi', 'Kenya'),
(23, 'Port Louis', 'Mauritius'),
(24, 'Victoria', 'Seiscelle'),
(25, 'Doha', 'Qatar'),
(26, 'Singapore', 'Singapore'),
(27, 'Tokyo', 'Giappone'),
(28, 'Pechino', 'Cina'),
(29, 'Hong Kong', 'Cina'),
(30, 'Jakarta', 'Indonesia'),
(31, 'Sydney', 'Australia'),
(32, 'Canberra', 'Australia'),
(33, 'Cancun', 'Messico'),
(34, 'Tijuana', 'Messico'),
(35, 'Buenos Aires', 'Argentina'),
(36, 'Rio de Janeiro', 'Brasile'),
(37, 'New York', 'USA'),
(38, 'Miami', 'USA'),
(39, 'Boston', 'USA'),
(40, 'Chicago', 'USA'),
(41, 'Los Angeles', 'USA'),
(42, 'Honolulu', 'Hawaii'),
(43, 'Oslo', 'Norvegia'),
(44, 'Helsinki', 'Finlandia'),
(45, 'Lisbona', 'Portogallo'),
(46, 'Stoccolma', 'Svezia');

-- --------------------------------------------------------

--
-- Struttura della tabella `Offerte`
--

CREATE TABLE IF NOT EXISTS `Offerte` (
  `idViaggio` int(11) NOT NULL,
  `scontoperc` int(11) DEFAULT NULL,
  `disponibili` int(11) DEFAULT NULL,
  PRIMARY KEY (`idViaggio`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `PostiPrimaClasse`
--

CREATE TABLE IF NOT EXISTS `PostiPrimaClasse` (
  `numero` varchar(3) NOT NULL DEFAULT '',
  `aereo` varchar(10) NOT NULL,
  PRIMARY KEY (`numero`,`aereo`),
  KEY `aereo` (`aereo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `PostiPrimaClasse`
--

INSERT INTO `PostiPrimaClasse` (`numero`, `aereo`) VALUES
('A01', 'N-014'),
('A02', 'N-014'),
('A03', 'N-014'),
('A04', 'N-014'),
('A05', 'N-014'),
('B01', 'N-014'),
('B02', 'N-014'),
('B03', 'N-014'),
('B04', 'N-014'),
('B05', 'N-014'),
('C01', 'N-014'),
('C02', 'N-014'),
('C03', 'N-014'),
('C04', 'N-014'),
('C05', 'N-014'),
('A01', 'N-760PO'),
('A02', 'N-760PO'),
('A03', 'N-760PO'),
('A04', 'N-760PO'),
('A05', 'N-760PO'),
('B01', 'N-760PO'),
('B02', 'N-760PO'),
('B03', 'N-760PO'),
('B04', 'N-760PO'),
('B05', 'N-760PO'),
('C01', 'N-760PO'),
('C02', 'N-760PO'),
('C04', 'N-760PO'),
('C05', 'N-760PO'),
('C3', 'N-760PO'),
('A01', 'N-765ZR'),
('A02', 'N-765ZR'),
('A03', 'N-765ZR'),
('A04', 'N-765ZR'),
('A05', 'N-765ZR'),
('A06', 'N-765ZR'),
('A07', 'N-765ZR'),
('A08', 'N-765ZR'),
('A09', 'N-765ZR'),
('A10', 'N-765ZR'),
('A01', 'N-870AA'),
('A02', 'N-870AA'),
('A03', 'N-870AA'),
('A04', 'N-870AA'),
('A05', 'N-870AA'),
('B01', 'N-870AA'),
('B02', 'N-870AA'),
('B03', 'N-870AA'),
('B04', 'N-870AA'),
('B05', 'N-870AA'),
('A01', 'N-980AB'),
('A02', 'N-980AB'),
('A03', 'N-980AB'),
('A04', 'N-980AB'),
('A05', 'N-980AB'),
('B01', 'N-980AB'),
('B02', 'N-980AB'),
('B03', 'N-980AB'),
('B04', 'N-980AB'),
('B05', 'N-980AB'),
('A01', 'N-9AZ'),
('A02', 'N-9AZ'),
('A03', 'N-9AZ'),
('A04', 'N-9AZ'),
('A05', 'N-9AZ'),
('B01', 'N-9AZ'),
('B02', 'N-9AZ'),
('B03', 'N-9AZ'),
('B04', 'N-9AZ'),
('B05', 'N-9AZ');

-- --------------------------------------------------------

--
-- Struttura della tabella `Prenotazioni`
--

CREATE TABLE IF NOT EXISTS `Prenotazioni` (
  `idPrenotazione` int(100) NOT NULL AUTO_INCREMENT,
  `idViaggio` int(11) NOT NULL,
  `acquirente` int(11) NOT NULL,
  `passeggero` int(11) NOT NULL,
  `numeroBagagli` int(3) DEFAULT NULL,
  `type` enum('prima','seconda') DEFAULT 'seconda',
  `stato` enum('valido','annullato','rimborsato') DEFAULT 'valido',
  `prezzoPrenotazione` int(11) DEFAULT NULL,
  `posto` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`idPrenotazione`),
  KEY `posto` (`posto`),
  KEY `idViaggio` (`idViaggio`),
  KEY `acquirente` (`acquirente`),
  KEY `passeggero` (`passeggero`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `Scali`
--

CREATE TABLE IF NOT EXISTS `Scali` (
  `idViaggioConScali` int(11) NOT NULL DEFAULT '0',
  `idViaggioDiretto` int(11) NOT NULL DEFAULT '0',
  `ordine` int(11) DEFAULT '0',
  PRIMARY KEY (`idViaggioConScali`,`idViaggioDiretto`),
  KEY `idViaggioDiretto` (`idViaggioDiretto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `Scali`
--

INSERT INTO `Scali` (`idViaggioConScali`, `idViaggioDiretto`, `ordine`) VALUES
(6, 1, 2),
(6, 3, 1),
(7, 1, 2),
(7, 3, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `TariffeBagagli`
--

CREATE TABLE IF NOT EXISTS `TariffeBagagli` (
  `idBagaglio` int(11) NOT NULL DEFAULT '0',
  `idCompagnia` int(11) NOT NULL DEFAULT '0',
  `prezzo` int(11) DEFAULT NULL,
  PRIMARY KEY (`idBagaglio`,`idCompagnia`),
  KEY `idCompagnia` (`idCompagnia`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `Tratte`
--

CREATE TABLE IF NOT EXISTS `Tratte` (
  `idTratta` int(11) NOT NULL AUTO_INCREMENT,
  `da` int(11) NOT NULL,
  `a` int(11) NOT NULL,
  PRIMARY KEY (`idTratta`),
  KEY `a` (`a`),
  KEY `da` (`da`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dump dei dati per la tabella `Tratte`
--

INSERT INTO `Tratte` (`idTratta`, `da`, `a`) VALUES
(1, 2, 6),
(2, 18, 14),
(3, 14, 12),
(4, 18, 12);

-- --------------------------------------------------------

--
-- Struttura della tabella `Utenti`
--

CREATE TABLE IF NOT EXISTS `Utenti` (
  `idAnag` int(11) NOT NULL,
  `password` varchar(40) NOT NULL,
  `type` enum('Guest','Admin') DEFAULT 'Guest',
  PRIMARY KEY (`idAnag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `Utenti`
--

INSERT INTO `Utenti` (`idAnag`, `password`, `type`) VALUES
(1, '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Guest'),
(2, '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Guest'),
(3, '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Admin'),
(4, '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Admin');

-- --------------------------------------------------------

--
-- Struttura della tabella `Viaggi`
--

CREATE TABLE IF NOT EXISTS `Viaggi` (
  `idViaggio` int(11) NOT NULL AUTO_INCREMENT,
  `giorno` date NOT NULL,
  `stato` enum('effettuato','previsto','soppresso') DEFAULT 'previsto',
  `prezzoPrima` int(11) DEFAULT NULL,
  `prezzoSeconda` int(11) NOT NULL,
  `postiPrima` int(11) DEFAULT NULL,
  `postiSeconda` int(11) NOT NULL,
  `idTratta` int(11) NOT NULL,
  `inseritoDa` int(11) NOT NULL,
  PRIMARY KEY (`idViaggio`),
  KEY `inseritoDa` (`inseritoDa`),
  KEY `idTratta` (`idTratta`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dump dei dati per la tabella `Viaggi`
--

INSERT INTO `Viaggi` (`idViaggio`, `giorno`, `stato`, `prezzoPrima`, `prezzoSeconda`, `postiPrima`, `postiSeconda`, `idTratta`, `inseritoDa`) VALUES
(1, '2013-09-18', 'previsto', 700, 600, 15, 200, 3, 4),
(2, '2013-09-28', 'previsto', 800, 400, 0, 500, 1, 4),
(3, '2013-09-18', 'previsto', 600, 500, 0, 300, 2, 4),
(4, '2013-08-08', 'previsto', 600, 400, 0, 400, 2, 4),
(6, '2013-09-18', 'previsto', 1300, 1100, 0, 200, 4, 4),
(7, '2013-09-18', 'previsto', 1300, 1100, 0, 200, 4, 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `ViaggiConScali`
--

CREATE TABLE IF NOT EXISTS `ViaggiConScali` (
  `idViaggioConScali` int(11) NOT NULL,
  PRIMARY KEY (`idViaggioConScali`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `ViaggiConScali`
--

INSERT INTO `ViaggiConScali` (`idViaggioConScali`) VALUES
(6),
(7);

-- --------------------------------------------------------

--
-- Struttura della tabella `ViaggiDiretti`
--

CREATE TABLE IF NOT EXISTS `ViaggiDiretti` (
  `idViaggioDiretto` int(11) NOT NULL,
  `idVolo` varchar(7) DEFAULT NULL,
  `aereo` varchar(10) DEFAULT NULL,
  `comandante` int(10) NOT NULL,
  `vice` int(10) NOT NULL,
  `ridottoPerc` int(11) DEFAULT NULL,
  `idCompagniaEsec` int(11) NOT NULL,
  PRIMARY KEY (`idViaggioDiretto`),
  KEY `idVolo` (`idVolo`),
  KEY `aereo` (`aereo`),
  KEY `comandante` (`comandante`),
  KEY `vice` (`vice`),
  KEY `idCompagniaEsec` (`idCompagniaEsec`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `ViaggiDiretti`
--

INSERT INTO `ViaggiDiretti` (`idViaggioDiretto`, `idVolo`, `aereo`, `comandante`, `vice`, `ridottoPerc`, `idCompagniaEsec`) VALUES
(1, 'AC21J7', 'N-014', 1009119202, 219862369, 10, 1),
(2, 'AE30Y7', 'N-4BA', 912483647, 453657689, 5, 3),
(3, 'AB56A3', 'N-981CF', 654387899, 109899765, 5, 6),
(4, 'AB56A3', 'N-453HY', 566473983, 564783902, 5, 7);

-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `viewAssistenti`
--
CREATE TABLE IF NOT EXISTS `viewAssistenti` (
`matricola` int(10)
,`nome` varchar(15)
,`cognome` varchar(15)
,`sesso` enum('M','F')
,`nascita` date
,`Compagnia` varchar(30)
);
-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `viewComandanti`
--
CREATE TABLE IF NOT EXISTS `viewComandanti` (
`matricola` int(10)
,`nome` varchar(15)
,`cognome` varchar(15)
,`sesso` enum('M','F')
,`nascita` date
,`Compagnia` varchar(30)
);
-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `viewTratte`
--
CREATE TABLE IF NOT EXISTS `viewTratte` (
`Tratta` int(11)
,`Partenza` varchar(40)
,`Arrivo` varchar(40)
);
-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `viewViaggiConScali`
--
CREATE TABLE IF NOT EXISTS `viewViaggiConScali` (
`idViaggio` int(11)
,`giorno` date
,`da` varchar(40)
,`a` varchar(40)
,`stato` enum('effettuato','previsto','soppresso')
,`prezzoPrima` int(11)
,`prezzoSeconda` int(11)
,`postiPrima` int(11)
,`postiSeconda` int(11)
,`admin` int(11)
);
-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `viewViaggiDiretti`
--
CREATE TABLE IF NOT EXISTS `viewViaggiDiretti` (
`idViaggio` int(11)
,`giorno` date
,`da` varchar(40)
,`a` varchar(40)
,`luogoP` varchar(40)
,`luogoA` varchar(40)
,`oraP` time
,`oraA` time
,`durata` time
,`stato` enum('effettuato','previsto','soppresso')
,`prezzoPrima` int(11)
,`prezzoSeconda` int(11)
,`postiPrima` int(11)
,`postiSeconda` int(11)
,`compagnia` varchar(30)
,`admin` int(11)
);
-- --------------------------------------------------------

--
-- Struttura stand-in per le viste `viewViceComandanti`
--
CREATE TABLE IF NOT EXISTS `viewViceComandanti` (
`matricola` int(10)
,`nome` varchar(15)
,`cognome` varchar(15)
,`sesso` enum('M','F')
,`nascita` date
,`Compagnia` varchar(30)
);
-- --------------------------------------------------------

--
-- Struttura della tabella `Voli`
--

CREATE TABLE IF NOT EXISTS `Voli` (
  `idVolo` varchar(7) NOT NULL,
  `oraP` time NOT NULL,
  `oraA` time NOT NULL,
  `idTratta` int(11) NOT NULL,
  `idCompagnia` int(11) NOT NULL,
  PRIMARY KEY (`idVolo`),
  KEY `idCompagnia` (`idCompagnia`),
  KEY `idTratta` (`idTratta`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `Voli`
--

INSERT INTO `Voli` (`idVolo`, `oraP`, `oraA`, `idTratta`, `idCompagnia`) VALUES
('AB56A3', '11:00:00', '12:00:00', 2, 7),
('AC21J7', '13:00:00', '14:50:00', 3, 1),
('AE30Y7', '08:00:00', '09:40:00', 1, 6);

-- --------------------------------------------------------

--
-- Struttura per la vista `viewAssistenti`
--
DROP TABLE IF EXISTS `viewAssistenti`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewAssistenti` AS select `d`.`matricola` AS `matricola`,`a`.`nome` AS `nome`,`a`.`cognome` AS `cognome`,`a`.`sesso` AS `sesso`,`a`.`nascita` AS `nascita`,`c`.`nome` AS `Compagnia` from ((`Dipendenti` `d` join `Anagrafiche` `a` on((`d`.`idAnag` = `a`.`idAnag`))) join `Compagnie` `c` on((`d`.`idCompagnia` = `c`.`idCompagnia`))) where (`d`.`grado` = 'assistente');

-- --------------------------------------------------------

--
-- Struttura per la vista `viewComandanti`
--
DROP TABLE IF EXISTS `viewComandanti`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewComandanti` AS select `d`.`matricola` AS `matricola`,`a`.`nome` AS `nome`,`a`.`cognome` AS `cognome`,`a`.`sesso` AS `sesso`,`a`.`nascita` AS `nascita`,`c`.`nome` AS `Compagnia` from ((`Dipendenti` `d` join `Anagrafiche` `a` on((`d`.`idAnag` = `a`.`idAnag`))) join `Compagnie` `c` on((`d`.`idCompagnia` = `c`.`idCompagnia`))) where (`d`.`grado` = 'comandante');

-- --------------------------------------------------------

--
-- Struttura per la vista `viewTratte`
--
DROP TABLE IF EXISTS `viewTratte`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewTratte` AS select `t`.`idTratta` AS `Tratta`,`a1`.`nome` AS `Partenza`,`a2`.`nome` AS `Arrivo` from ((`Tratte` `t` join `Aeroporti` `a1` on((`t`.`da` = `a1`.`idAeroporto`))) join `Aeroporti` `a2` on((`t`.`a` = `a2`.`idAeroporto`)));

-- --------------------------------------------------------

--
-- Struttura per la vista `viewViaggiConScali`
--
DROP TABLE IF EXISTS `viewViaggiConScali`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewViaggiConScali` AS select `v`.`idViaggio` AS `idViaggio`,`v`.`giorno` AS `giorno`,`vt`.`Partenza` AS `da`,`vt`.`Arrivo` AS `a`,`v`.`stato` AS `stato`,`v`.`prezzoPrima` AS `prezzoPrima`,`v`.`prezzoSeconda` AS `prezzoSeconda`,`v`.`postiPrima` AS `postiPrima`,`v`.`postiSeconda` AS `postiSeconda`,`v`.`inseritoDa` AS `admin` from ((`Viaggi` `v` join `ViaggiConScali` `vcs` on((`v`.`idViaggio` = `vcs`.`idViaggioConScali`))) join `viewTratte` `vt` on((`v`.`idTratta` = `vt`.`Tratta`)));

-- --------------------------------------------------------

--
-- Struttura per la vista `viewViaggiDiretti`
--
DROP TABLE IF EXISTS `viewViaggiDiretti`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewViaggiDiretti` AS select `v`.`idViaggio` AS `idViaggio`,`v`.`giorno` AS `giorno`,`vt`.`Partenza` AS `da`,`vt`.`Arrivo` AS `a`,`l1`.`nomecitta` AS `luogoP`,`l2`.`nomecitta` AS `luogoA`,`vo`.`oraP` AS `oraP`,`vo`.`oraA` AS `oraA`,timediff(`vo`.`oraA`,`vo`.`oraP`) AS `durata`,`v`.`stato` AS `stato`,`v`.`prezzoPrima` AS `prezzoPrima`,`v`.`prezzoSeconda` AS `prezzoSeconda`,`v`.`postiPrima` AS `postiPrima`,`v`.`postiSeconda` AS `postiSeconda`,`c`.`nome` AS `compagnia`,`v`.`inseritoDa` AS `admin` from (((((((`Viaggi` `v` join `ViaggiDiretti` `vd` on((`v`.`idViaggio` = `vd`.`idViaggioDiretto`))) join `viewTratte` `vt` on((`v`.`idTratta` = `vt`.`Tratta`))) join `Voli` `vo` on((`vd`.`idVolo` = `vo`.`idVolo`))) join `Compagnie` `c` on((`vd`.`idCompagniaEsec` = `c`.`idCompagnia`))) join `Tratte` `t` on((`vt`.`Tratta` = `t`.`idTratta`))) join `Luoghi` `l1` on((`t`.`da` = `l1`.`idLuogo`))) join `Luoghi` `l2` on((`t`.`a` = `l2`.`idLuogo`)));

-- --------------------------------------------------------

--
-- Struttura per la vista `viewViceComandanti`
--
DROP TABLE IF EXISTS `viewViceComandanti`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewViceComandanti` AS select `d`.`matricola` AS `matricola`,`a`.`nome` AS `nome`,`a`.`cognome` AS `cognome`,`a`.`sesso` AS `sesso`,`a`.`nascita` AS `nascita`,`c`.`nome` AS `Compagnia` from ((`Dipendenti` `d` join `Anagrafiche` `a` on((`d`.`idAnag` = `a`.`idAnag`))) join `Compagnie` `c` on((`d`.`idCompagnia` = `c`.`idCompagnia`))) where (`d`.`grado` = 'vice');

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `Aerei`
--
ALTER TABLE `Aerei`
  ADD CONSTRAINT `Aerei_ibfk_1` FOREIGN KEY (`idCompagnia`) REFERENCES `Compagnie` (`idCompagnia`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `Aeroporti`
--
ALTER TABLE `Aeroporti`
  ADD CONSTRAINT `Aeroporti_ibfk_1` FOREIGN KEY (`idLuogo`) REFERENCES `Luoghi` (`idLuogo`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `Assistenze`
--
ALTER TABLE `Assistenze`
  ADD CONSTRAINT `Assistenze_ibfk_1` FOREIGN KEY (`idViaggio`) REFERENCES `Viaggi` (`idViaggio`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Assistenze_ibfk_2` FOREIGN KEY (`matricola`) REFERENCES `Dipendenti` (`matricola`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `Dipendenti`
--
ALTER TABLE `Dipendenti`
  ADD CONSTRAINT `Dipendenti_ibfk_1` FOREIGN KEY (`idAnag`) REFERENCES `Anagrafiche` (`idAnag`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Dipendenti_ibfk_2` FOREIGN KEY (`idCompagnia`) REFERENCES `Compagnie` (`idCompagnia`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `Offerte`
--
ALTER TABLE `Offerte`
  ADD CONSTRAINT `Offerte_ibfk_1` FOREIGN KEY (`idViaggio`) REFERENCES `Viaggi` (`idViaggio`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `PostiPrimaClasse`
--
ALTER TABLE `PostiPrimaClasse`
  ADD CONSTRAINT `PostiPrimaClasse_ibfk_1` FOREIGN KEY (`aereo`) REFERENCES `Aerei` (`matricola`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `Prenotazioni`
--
ALTER TABLE `Prenotazioni`
  ADD CONSTRAINT `Prenotazioni_ibfk_1` FOREIGN KEY (`posto`) REFERENCES `PostiPrimaClasse` (`numero`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Prenotazioni_ibfk_2` FOREIGN KEY (`idViaggio`) REFERENCES `Viaggi` (`idViaggio`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Prenotazioni_ibfk_3` FOREIGN KEY (`acquirente`) REFERENCES `Utenti` (`idAnag`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Prenotazioni_ibfk_4` FOREIGN KEY (`passeggero`) REFERENCES `Anagrafiche` (`idAnag`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `Scali`
--
ALTER TABLE `Scali`
  ADD CONSTRAINT `Scali_ibfk_1` FOREIGN KEY (`idViaggioConScali`) REFERENCES `ViaggiConScali` (`idViaggioConScali`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Scali_ibfk_2` FOREIGN KEY (`idViaggioDiretto`) REFERENCES `ViaggiDiretti` (`idViaggioDiretto`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `TariffeBagagli`
--
ALTER TABLE `TariffeBagagli`
  ADD CONSTRAINT `TariffeBagagli_ibfk_1` FOREIGN KEY (`idBagaglio`) REFERENCES `Bagagli` (`idBagaglio`) ON UPDATE CASCADE,
  ADD CONSTRAINT `TariffeBagagli_ibfk_2` FOREIGN KEY (`idCompagnia`) REFERENCES `Compagnie` (`idCompagnia`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `Tratte`
--
ALTER TABLE `Tratte`
  ADD CONSTRAINT `Tratte_ibfk_1` FOREIGN KEY (`a`) REFERENCES `Aeroporti` (`idAeroporto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Tratte_ibfk_2` FOREIGN KEY (`da`) REFERENCES `Aeroporti` (`idAeroporto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `Utenti`
--
ALTER TABLE `Utenti`
  ADD CONSTRAINT `Utenti_ibfk_1` FOREIGN KEY (`idAnag`) REFERENCES `Anagrafiche` (`idAnag`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `Viaggi`
--
ALTER TABLE `Viaggi`
  ADD CONSTRAINT `Viaggi_ibfk_1` FOREIGN KEY (`inseritoDa`) REFERENCES `Utenti` (`idAnag`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Viaggi_ibfk_2` FOREIGN KEY (`idTratta`) REFERENCES `Tratte` (`idTratta`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `ViaggiConScali`
--
ALTER TABLE `ViaggiConScali`
  ADD CONSTRAINT `ViaggiConScali_ibfk_1` FOREIGN KEY (`idViaggioConScali`) REFERENCES `Viaggi` (`idViaggio`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `ViaggiDiretti`
--
ALTER TABLE `ViaggiDiretti`
  ADD CONSTRAINT `ViaggiDiretti_ibfk_1` FOREIGN KEY (`idViaggioDiretto`) REFERENCES `Viaggi` (`idViaggio`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ViaggiDiretti_ibfk_2` FOREIGN KEY (`idVolo`) REFERENCES `Voli` (`idVolo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ViaggiDiretti_ibfk_3` FOREIGN KEY (`aereo`) REFERENCES `Aerei` (`matricola`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ViaggiDiretti_ibfk_4` FOREIGN KEY (`comandante`) REFERENCES `Dipendenti` (`matricola`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ViaggiDiretti_ibfk_5` FOREIGN KEY (`vice`) REFERENCES `Dipendenti` (`matricola`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ViaggiDiretti_ibfk_6` FOREIGN KEY (`idCompagniaEsec`) REFERENCES `Compagnie` (`idCompagnia`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `Voli`
--
ALTER TABLE `Voli`
  ADD CONSTRAINT `Voli_ibfk_1` FOREIGN KEY (`idCompagnia`) REFERENCES `Compagnie` (`idCompagnia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Voli_ibfk_2` FOREIGN KEY (`idTratta`) REFERENCES `Tratte` (`idTratta`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
