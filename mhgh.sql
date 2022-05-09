-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 09, 2022 alle 22:49
-- Versione del server: 10.4.22-MariaDB
-- Versione PHP: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mhgh`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `credenziali_utenti`
--

CREATE TABLE `credenziali_utenti` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_salt` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `preferences`
--

CREATE TABLE `preferences` (
  `ID` int(11) NOT NULL,
  `arma_preferita` int(11) DEFAULT NULL,
  `preferenze_di_caccia` int(11) DEFAULT NULL,
  `orario_libero_inizio` time DEFAULT NULL,
  `orario_libero_fine` time DEFAULT NULL,
  `HR` int(11) DEFAULT NULL,
  `piattaforma` varchar(100) DEFAULT NULL,
  `related_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `id` int(11) NOT NULL,
  `ultimo_accesso` datetime DEFAULT NULL,
  `bio_personale` varchar(255) DEFAULT NULL,
  `link_propic` varchar(255) DEFAULT NULL,
  `discord_data` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `sesso` varchar(100) DEFAULT NULL,
  `related_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti_preferiti`
--

CREATE TABLE `utenti_preferiti` (
  `id` int(11) NOT NULL,
  `user_saved` int(11) DEFAULT NULL,
  `related_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `credenziali_utenti`
--
ALTER TABLE `credenziali_utenti`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `preferences`
--
ALTER TABLE `preferences`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `related_user_id` (`related_user_id`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `related_user_id` (`related_user_id`);

--
-- Indici per le tabelle `utenti_preferiti`
--
ALTER TABLE `utenti_preferiti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `related_user_id` (`related_user_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `preferences`
--
ALTER TABLE `preferences`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `utente`
--
ALTER TABLE `utente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `utenti_preferiti`
--
ALTER TABLE `utenti_preferiti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `preferences`
--
ALTER TABLE `preferences`
  ADD CONSTRAINT `preferences_ibfk_1` FOREIGN KEY (`related_user_id`) REFERENCES `utente` (`id`);

--
-- Limiti per la tabella `utente`
--
ALTER TABLE `utente`
  ADD CONSTRAINT `utente_ibfk_1` FOREIGN KEY (`related_user_id`) REFERENCES `credenziali_utenti` (`id`);

--
-- Limiti per la tabella `utenti_preferiti`
--
ALTER TABLE `utenti_preferiti`
  ADD CONSTRAINT `utenti_preferiti_ibfk_1` FOREIGN KEY (`related_user_id`) REFERENCES `utente` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
