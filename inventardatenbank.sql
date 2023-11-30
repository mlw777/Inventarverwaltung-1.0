-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 25. Mai 2023 um 08:24
-- Server-Version: 10.4.27-MariaDB
-- PHP-Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `inventardatenbank`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ausleihe`
--

CREATE TABLE `ausleihe` (
  `ausleihid` int(11) NOT NULL,
  `rehanr` int(10) DEFAULT NULL,
  `ausleihe` datetime NOT NULL,
  `inventarid` varchar(20) NOT NULL,
  `zurueckgegeben` tinyint(1) NOT NULL DEFAULT 0,
  `mitarbeiterid` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Trigger `ausleihe`
--
DELIMITER $$
CREATE TRIGGER `Trueckgabehistory` AFTER DELETE ON `ausleihe` FOR EACH ROW BEGIN
-- Insert records into
INSERT INTO
rueckgabehistory
(
    ausleihid,rehanr,mitarbeiterid,ausleihe,inventarid
)
VALUES
(
    OLD.ausleihid,
    OLD.rehanr,  
    OLD.mitarbeiterid,
    OLD.ausleihe,
    OLD.inventarid
  
);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `inventar`
--

CREATE TABLE `inventar` (
  `inventarid` varchar(20) NOT NULL,
  `bezeichnung` varchar(100) NOT NULL,
  `seriennr` varchar(50) NOT NULL,
  `kategorie` varchar(100) NOT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'verfügbar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mitarbeiterinnen`
--

CREATE TABLE `mitarbeiterinnen` (
  `mitarbeiterid` varchar(10) NOT NULL,
  `vorname` varchar(100) NOT NULL,
  `nachname` varchar(100) NOT NULL,
  `abteilung` varchar(100) NOT NULL,
  `Benutzername` varchar(20) NOT NULL,
  `Passwort` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `mitarbeiterinnen`
--

INSERT INTO `mitarbeiterinnen` (`mitarbeiterid`, `vorname`, `nachname`, `abteilung`, `Benutzername`, `Passwort`) VALUES
('12345', 'Administrator', 'Administrator', 'IT Campus42', 'admin', 'Service1234');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rehabilitandinnen`
--

CREATE TABLE `rehabilitandinnen` (
  `rehanr` int(10) NOT NULL,
  `vorname` varchar(100) NOT NULL,
  `nachname` varchar(100) NOT NULL,
  `kurs` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rueckgabehistory`
--

CREATE TABLE `rueckgabehistory` (
  `rueckgabeid` int(11) NOT NULL,
  `inventarid` varchar(20) NOT NULL,
  `rehanr` int(10) NOT NULL,
  `mitarbeiterid` varchar(10) NOT NULL,
  `rueckgabe` timestamp NOT NULL DEFAULT current_timestamp(),
  `ausleihe` datetime NOT NULL,
  `ausleihid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `ausleihe`
--
ALTER TABLE `ausleihe`
  ADD PRIMARY KEY (`ausleihid`),
  ADD KEY `fk_inventarid` (`inventarid`),
  ADD KEY `fk_rehanr` (`rehanr`),
  ADD KEY `mitarbeiterid` (`mitarbeiterid`);

--
-- Indizes für die Tabelle `inventar`
--
ALTER TABLE `inventar`
  ADD PRIMARY KEY (`inventarid`);

--
-- Indizes für die Tabelle `mitarbeiterinnen`
--
ALTER TABLE `mitarbeiterinnen`
  ADD PRIMARY KEY (`mitarbeiterid`);

--
-- Indizes für die Tabelle `rehabilitandinnen`
--
ALTER TABLE `rehabilitandinnen`
  ADD PRIMARY KEY (`rehanr`);

--
-- Indizes für die Tabelle `rueckgabehistory`
--
ALTER TABLE `rueckgabehistory`
  ADD PRIMARY KEY (`rueckgabeid`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `ausleihe`
--
ALTER TABLE `ausleihe`
  MODIFY `ausleihid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT für Tabelle `rueckgabehistory`
--
ALTER TABLE `rueckgabehistory`
  MODIFY `rueckgabeid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `ausleihe`
--
ALTER TABLE `ausleihe`
  ADD CONSTRAINT `fk_inventarid` FOREIGN KEY (`inventarid`) REFERENCES `inventar` (`inventarid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rehanr` FOREIGN KEY (`rehanr`) REFERENCES `rehabilitandinnen` (`rehanr`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
