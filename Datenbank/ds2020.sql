-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 19. Okt 2017 um 16:03
-- Server-Version: 10.1.13-MariaDB
-- PHP-Version: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `ds2020`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `absenzen_absenzen`
--

CREATE TABLE `absenzen_absenzen` (
  `absenzID` int(11) NOT NULL,
  `absenzSchuelerAsvID` varchar(50) NOT NULL,
  `absenzDatum` date NOT NULL,
  `absenzDatumEnde` date NOT NULL,
  `absenzQuelle` enum('TELEFON','WEBPORTAL','LEHRER','PERSOENLICH','FAX') NOT NULL,
  `absenzBemerkung` mediumtext NOT NULL,
  `absenzErfasstTime` int(11) NOT NULL,
  `absenzErfasstUserID` int(11) NOT NULL,
  `absenzBefreiungID` int(11) NOT NULL DEFAULT '0',
  `absenzBeurlaubungID` int(11) NOT NULL DEFAULT '0',
  `absenzStunden` mediumtext NOT NULL,
  `absenzisEntschuldigt` tinyint(1) NOT NULL,
  `absenzIsSchriftlichEntschuldigt` tinyint(1) NOT NULL,
  `absenzKommtSpaeter` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Absenzen der Schüler';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `absenzen_absenzen_stunden`
--

CREATE TABLE `absenzen_absenzen_stunden` (
  `absenzID` int(11) NOT NULL,
  `absenzStunde` int(11) NOT NULL,
  `absenzStundeEntschuldigt` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Abwesende Stunden';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `absenzen_attestpflicht`
--

CREATE TABLE `absenzen_attestpflicht` (
  `attestpflichtID` int(11) NOT NULL,
  `schuelerAsvID` varchar(100) NOT NULL,
  `attestpflichtStart` date NOT NULL,
  `attestpflichtEnde` date NOT NULL,
  `attestpflichtUserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `absenzen_befreiungen`
--

CREATE TABLE `absenzen_befreiungen` (
  `befreiungID` int(11) NOT NULL,
  `befreiungUhrzeit` varchar(100) NOT NULL,
  `befreiungLehrer` varchar(100) NOT NULL,
  `befreiungPrinted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `absenzen_beurlaubungen`
--

CREATE TABLE `absenzen_beurlaubungen` (
  `beurlaubungID` int(11) NOT NULL,
  `beurlaubungCreatorID` int(11) NOT NULL,
  `beurlaubungPrinted` tinyint(1) NOT NULL DEFAULT '0',
  `beurlaubungIsInternAbwesend` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `absenzen_beurlaubung_antrag`
--

CREATE TABLE `absenzen_beurlaubung_antrag` (
  `antragID` int(11) NOT NULL,
  `antragUserID` int(11) NOT NULL,
  `antragSchuelerAsvID` varchar(100) NOT NULL,
  `antragDatumStart` date NOT NULL,
  `antragDatumEnde` date NOT NULL,
  `antragBegruendung` longtext NOT NULL,
  `antragTime` int(11) NOT NULL,
  `antragKLGenehmigt` tinyint(1) NOT NULL DEFAULT '-1',
  `antragKLGenehmigtDate` date DEFAULT NULL,
  `antragSLgenehmigt` tinyint(1) NOT NULL DEFAULT '-1',
  `antragSLgenehmigtDate` date DEFAULT NULL,
  `antragIsVerarbeitet` tinyint(1) NOT NULL DEFAULT '0',
  `antragKLKommentar` longtext NOT NULL,
  `antragSLKommentar` longtext NOT NULL,
  `antragStunden` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `absenzen_comments`
--

CREATE TABLE `absenzen_comments` (
  `schuelerAsvID` varchar(100) NOT NULL,
  `commentText` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `absenzen_krankmeldungen`
--

CREATE TABLE `absenzen_krankmeldungen` (
  `krankmeldungID` int(11) NOT NULL,
  `krankmeldungSchuelerASVID` varchar(50) NOT NULL,
  `krankmeldungDate` date NOT NULL,
  `krankmeldungUntilDate` date NOT NULL,
  `krankmeldungElternID` int(11) NOT NULL,
  `krankmeldungDurch` enum('m','v','s','schueleru18','schuelerue18') NOT NULL,
  `krankmeldungKommentar` mediumtext NOT NULL,
  `krankmeldungAbsenzID` tinyint(1) NOT NULL DEFAULT '0',
  `krankmeldungTime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `absenzen_meldung`
--

CREATE TABLE `absenzen_meldung` (
  `meldungDatum` date NOT NULL,
  `meldungKlasse` varchar(100) NOT NULL,
  `meldungUserID` int(11) NOT NULL,
  `meldungTime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `absenzen_merker`
--

CREATE TABLE `absenzen_merker` (
  `merkerID` int(11) NOT NULL,
  `merkerSchuelerAsvID` varchar(100) NOT NULL,
  `merkerDate` date NOT NULL,
  `merkerText` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `absenzen_sanizimmer`
--

CREATE TABLE `absenzen_sanizimmer` (
  `sanizimmerID` int(11) NOT NULL,
  `sanizimmerSchuelerAsvID` varchar(20) NOT NULL,
  `sanizimmerTimeStart` int(11) NOT NULL DEFAULT '0',
  `sanizimmerTimeEnde` int(11) NOT NULL DEFAULT '0',
  `sanizimmerErfasserUserID` int(11) NOT NULL,
  `sanizimmerResult` enum('ZURUECK','BEFREIUNG','RETTUNGSDIENST') NOT NULL,
  `sanizimmerGrund` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `absenzen_verspaetungen`
--

CREATE TABLE `absenzen_verspaetungen` (
  `verspaetungID` int(11) NOT NULL,
  `verspaetungSchuelerAsvID` varchar(20) NOT NULL,
  `verspaetungDate` date NOT NULL,
  `verspaetungMinuten` int(11) NOT NULL,
  `verspaetungKommentar` mediumtext NOT NULL,
  `verspaetungStunde` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `beurlaubungsantraege`
--

CREATE TABLE `beurlaubungsantraege` (
  `baID` int(11) NOT NULL,
  `baUserID` int(11) NOT NULL,
  `baSchuelerAsvID` varchar(200) NOT NULL,
  `baSchuelerText` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cron_execution`
--

CREATE TABLE `cron_execution` (
  `cronRunID` int(11) NOT NULL,
  `cronName` varchar(255) NOT NULL,
  `cronStartTime` int(11) NOT NULL,
  `cronEndTime` int(11) NOT NULL,
  `cronSuccess` tinyint(1) NOT NULL,
  `cronResult` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cron_status`
--

CREATE TABLE `cron_status` (
  `cronIsRunning` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `eltern_adressen`
--

CREATE TABLE `eltern_adressen` (
  `adresseID` int(11) NOT NULL,
  `adresseSchuelerAsvID` varchar(100) NOT NULL,
  `adresseWessen` enum('eb','web','s','w') NOT NULL COMMENT 'eb=Erziehungsberechtiger, web=weiterer Erziehungsberechtigter; s=Schüler; w=weitere Anschrift',
  `adresseIsAuskunftsberechtigt` tinyint(1) NOT NULL,
  `adresseIsHauptansprechpartner` tinyint(1) NOT NULL,
  `adresseStrasse` mediumtext NOT NULL,
  `adresseNummer` mediumtext NOT NULL,
  `adresseOrt` mediumtext NOT NULL,
  `adressePostleitzahl` varchar(10) NOT NULL,
  `adresseFamilienname` mediumtext NOT NULL,
  `adresseVorname` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `eltern_email`
--

CREATE TABLE `eltern_email` (
  `elternEMail` varchar(255) NOT NULL,
  `elternSchuelerAsvID` varchar(20) NOT NULL,
  `elternAdresseID` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `eltern_telefon`
--

CREATE TABLE `eltern_telefon` (
  `telefonNummer` varchar(255) NOT NULL,
  `schuelerAsvID` varchar(50) NOT NULL,
  `telefonTyp` enum('telefon','mobiltelefon','fax') NOT NULL DEFAULT 'telefon',
  `adresseID` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `eltern_to_schueler`
--

CREATE TABLE `eltern_to_schueler` (
  `elternUserID` int(11) NOT NULL,
  `schuelerUserID` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fachbetreuer`
--

CREATE TABLE `fachbetreuer` (
  `fachID` int(11) NOT NULL,
  `lehrerAsvID` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `faecher`
--

CREATE TABLE `faecher` (
  `fachID` int(11) NOT NULL COMMENT 'Aus XML File',
  `fachKurzform` mediumtext NOT NULL,
  `fachLangform` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `klassenleitung`
--

CREATE TABLE `klassenleitung` (
  `klasseName` varchar(200) NOT NULL,
  `lehrerID` int(11) NOT NULL,
  `klassenleitungArt` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lehrer`
--

CREATE TABLE `lehrer` (
  `lehrerID` int(11) NOT NULL,
  `lehrerAsvID` varchar(100) NOT NULL,
  `lehrerKuerzel` varchar(100) NOT NULL,
  `lehrerName` mediumtext NOT NULL,
  `lehrerRufname` mediumtext NOT NULL,
  `lehrerGeschlecht` enum('w','m') NOT NULL,
  `lehrerAmtsbezeichnung` varchar(200) NOT NULL,
  `lehrerIsSchulleitung` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mail_send`
--

CREATE TABLE `mail_send` (
  `mailID` int(11) NOT NULL,
  `mailRecipient` mediumtext NOT NULL,
  `mailSubject` mediumtext NOT NULL,
  `mailText` mediumtext NOT NULL,
  `mailSent` int(11) NOT NULL DEFAULT '0',
  `mailCrawler` int(11) NOT NULL DEFAULT '1',
  `replyTo` varchar(255) DEFAULT NULL,
  `mailCC` varchar(255) DEFAULT NULL,
  `mailLesebestaetigung` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `modul_admin_notes`
--

CREATE TABLE `modul_admin_notes` (
  `noteID` int(11) NOT NULL,
  `noteModuleName` varchar(255) NOT NULL,
  `noteText` text NOT NULL,
  `noteUserID` int(11) NOT NULL,
  `noteTime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `respizienz`
--

CREATE TABLE `respizienz` (
  `respizienzID` int(11) NOT NULL,
  `respizienzFile` int(11) NOT NULL DEFAULT '0',
  `respizientFSLFile` int(11) NOT NULL DEFAULT '0',
  `respizientFSLLehrer` varchar(50) DEFAULT NULL,
  `respizientSLFile` int(11) NOT NULL DEFAULT '0',
  `respizientSLLehrer` varchar(50) DEFAULT NULL,
  `respizienzIsAnalog` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `schueler`
--

CREATE TABLE `schueler` (
  `schuelerAsvID` varchar(200) NOT NULL,
  `schuelerName` text NOT NULL,
  `schuelerVornamen` text NOT NULL,
  `schuelerRufname` text NOT NULL,
  `schuelerGeschlecht` enum('m','w') NOT NULL,
  `schuelerGeburtsdatum` date NOT NULL,
  `schuelerKlasse` varchar(200) NOT NULL,
  `schuelerJahrgangsstufe` varchar(10) NOT NULL,
  `schuelerAustrittDatum` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sessions`
--

CREATE TABLE `sessions` (
  `sessionID` varchar(255) NOT NULL,
  `sessionUserID` int(11) NOT NULL,
  `sessionType` enum('NORMAL','SAVED') NOT NULL,
  `sessionIP` varchar(100) NOT NULL,
  `sessionLastActivity` int(11) NOT NULL,
  `sessionBrowser` varchar(255) NOT NULL,
  `sessionDevice` enum('ANDROIDAPP','IOSAPP','WINDOWSPHONEAPP','NORMAL','SINGLESIGNON') NOT NULL DEFAULT 'NORMAL'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings`
--

CREATE TABLE `settings` (
  `settingName` varchar(100) NOT NULL,
  `settingValue` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `site_activation`
--

CREATE TABLE `site_activation` (
  `siteName` varchar(200) NOT NULL,
  `siteIsActive` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `templates`
--

CREATE TABLE `templates` (
  `templateName` varchar(200) NOT NULL,
  `templateCompiledContents` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `uploads`
--

CREATE TABLE `uploads` (
  `uploadID` int(11) NOT NULL,
  `uploadFileName` text NOT NULL,
  `uploadFileExtension` varchar(50) NOT NULL,
  `uploadFileMimeType` varchar(200) NOT NULL,
  `uploadTime` int(11) NOT NULL,
  `uploaderUserID` int(11) NOT NULL,
  `fileAccessCode` varchar(222) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `userName` varchar(255) NOT NULL,
  `userFirstName` mediumtext NOT NULL,
  `userLastName` mediumtext NOT NULL,
  `userIsAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `userIsLehrer` tinyint(1) NOT NULL DEFAULT '0',
  `userIsEltern` tinyint(1) NOT NULL DEFAULT '0',
  `userIsSchueler` tinyint(1) NOT NULL DEFAULT '0',
  `userIsSekretariat` tinyint(1) NOT NULL DEFAULT '0',
  `userSchuelerAsvID` varchar(100) NOT NULL,
  `userLehrerAsvID` varchar(100) NOT NULL,
  `userElternSchuelerAsvIDs` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Enthält alle Benutzer, die SchuleIntern benutzen können';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users_groups`
--

CREATE TABLE `users_groups` (
  `userID` int(11) NOT NULL,
  `groupName` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `absenzen_absenzen`
--
ALTER TABLE `absenzen_absenzen`
  ADD PRIMARY KEY (`absenzID`);

--
-- Indizes für die Tabelle `absenzen_absenzen_stunden`
--
ALTER TABLE `absenzen_absenzen_stunden`
  ADD PRIMARY KEY (`absenzID`,`absenzStunde`);

--
-- Indizes für die Tabelle `absenzen_attestpflicht`
--
ALTER TABLE `absenzen_attestpflicht`
  ADD PRIMARY KEY (`attestpflichtID`);

--
-- Indizes für die Tabelle `absenzen_befreiungen`
--
ALTER TABLE `absenzen_befreiungen`
  ADD PRIMARY KEY (`befreiungID`);

--
-- Indizes für die Tabelle `absenzen_beurlaubungen`
--
ALTER TABLE `absenzen_beurlaubungen`
  ADD PRIMARY KEY (`beurlaubungID`);

--
-- Indizes für die Tabelle `absenzen_beurlaubung_antrag`
--
ALTER TABLE `absenzen_beurlaubung_antrag`
  ADD PRIMARY KEY (`antragID`);

--
-- Indizes für die Tabelle `absenzen_comments`
--
ALTER TABLE `absenzen_comments`
  ADD PRIMARY KEY (`schuelerAsvID`);

--
-- Indizes für die Tabelle `absenzen_krankmeldungen`
--
ALTER TABLE `absenzen_krankmeldungen`
  ADD PRIMARY KEY (`krankmeldungID`);

--
-- Indizes für die Tabelle `absenzen_meldung`
--
ALTER TABLE `absenzen_meldung`
  ADD PRIMARY KEY (`meldungDatum`,`meldungKlasse`);

--
-- Indizes für die Tabelle `absenzen_merker`
--
ALTER TABLE `absenzen_merker`
  ADD PRIMARY KEY (`merkerID`);

--
-- Indizes für die Tabelle `absenzen_sanizimmer`
--
ALTER TABLE `absenzen_sanizimmer`
  ADD PRIMARY KEY (`sanizimmerID`);

--
-- Indizes für die Tabelle `absenzen_verspaetungen`
--
ALTER TABLE `absenzen_verspaetungen`
  ADD PRIMARY KEY (`verspaetungID`);

--
-- Indizes für die Tabelle `beurlaubungsantraege`
--
ALTER TABLE `beurlaubungsantraege`
  ADD PRIMARY KEY (`baID`);

--
-- Indizes für die Tabelle `cron_execution`
--
ALTER TABLE `cron_execution`
  ADD PRIMARY KEY (`cronRunID`);

--
-- Indizes für die Tabelle `eltern_adressen`
--
ALTER TABLE `eltern_adressen`
  ADD PRIMARY KEY (`adresseID`);

--
-- Indizes für die Tabelle `eltern_email`
--
ALTER TABLE `eltern_email`
  ADD PRIMARY KEY (`elternEMail`,`elternSchuelerAsvID`);

--
-- Indizes für die Tabelle `eltern_telefon`
--
ALTER TABLE `eltern_telefon`
  ADD PRIMARY KEY (`telefonNummer`,`schuelerAsvID`,`adresseID`),
  ADD KEY `telefonNummer` (`telefonNummer`,`schuelerAsvID`,`telefonTyp`) USING BTREE,
  ADD KEY `telefonNummer_2` (`telefonNummer`,`schuelerAsvID`) USING BTREE;

--
-- Indizes für die Tabelle `eltern_to_schueler`
--
ALTER TABLE `eltern_to_schueler`
  ADD PRIMARY KEY (`elternUserID`,`schuelerUserID`);

--
-- Indizes für die Tabelle `faecher`
--
ALTER TABLE `faecher`
  ADD PRIMARY KEY (`fachID`);

--
-- Indizes für die Tabelle `klassenleitung`
--
ALTER TABLE `klassenleitung`
  ADD PRIMARY KEY (`klasseName`,`lehrerID`);

--
-- Indizes für die Tabelle `lehrer`
--
ALTER TABLE `lehrer`
  ADD PRIMARY KEY (`lehrerID`);

--
-- Indizes für die Tabelle `mail_send`
--
ALTER TABLE `mail_send`
  ADD PRIMARY KEY (`mailID`);

--
-- Indizes für die Tabelle `modul_admin_notes`
--
ALTER TABLE `modul_admin_notes`
  ADD PRIMARY KEY (`noteID`);

--
-- Indizes für die Tabelle `respizienz`
--
ALTER TABLE `respizienz`
  ADD PRIMARY KEY (`respizienzID`);

--
-- Indizes für die Tabelle `schueler`
--
ALTER TABLE `schueler`
  ADD PRIMARY KEY (`schuelerAsvID`);

--
-- Indizes für die Tabelle `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`sessionID`);

--
-- Indizes für die Tabelle `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`settingName`);

--
-- Indizes für die Tabelle `site_activation`
--
ALTER TABLE `site_activation`
  ADD PRIMARY KEY (`siteName`);

--
-- Indizes für die Tabelle `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`templateName`);

--
-- Indizes für die Tabelle `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`uploadID`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- Indizes für die Tabelle `users_groups`
--
ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`userID`,`groupName`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `absenzen_absenzen`
--
ALTER TABLE `absenzen_absenzen`
  MODIFY `absenzID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `absenzen_attestpflicht`
--
ALTER TABLE `absenzen_attestpflicht`
  MODIFY `attestpflichtID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `absenzen_befreiungen`
--
ALTER TABLE `absenzen_befreiungen`
  MODIFY `befreiungID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `absenzen_beurlaubungen`
--
ALTER TABLE `absenzen_beurlaubungen`
  MODIFY `beurlaubungID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `absenzen_beurlaubung_antrag`
--
ALTER TABLE `absenzen_beurlaubung_antrag`
  MODIFY `antragID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `absenzen_krankmeldungen`
--
ALTER TABLE `absenzen_krankmeldungen`
  MODIFY `krankmeldungID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `absenzen_merker`
--
ALTER TABLE `absenzen_merker`
  MODIFY `merkerID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `absenzen_sanizimmer`
--
ALTER TABLE `absenzen_sanizimmer`
  MODIFY `sanizimmerID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `absenzen_verspaetungen`
--
ALTER TABLE `absenzen_verspaetungen`
  MODIFY `verspaetungID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `beurlaubungsantraege`
--
ALTER TABLE `beurlaubungsantraege`
  MODIFY `baID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `cron_execution`
--
ALTER TABLE `cron_execution`
  MODIFY `cronRunID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `eltern_adressen`
--
ALTER TABLE `eltern_adressen`
  MODIFY `adresseID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `lehrer`
--
ALTER TABLE `lehrer`
  MODIFY `lehrerID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `mail_send`
--
ALTER TABLE `mail_send`
  MODIFY `mailID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `modul_admin_notes`
--
ALTER TABLE `modul_admin_notes`
  MODIFY `noteID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `uploads`
--
ALTER TABLE `uploads`
  MODIFY `uploadID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
