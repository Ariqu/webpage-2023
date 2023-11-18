-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 18 Lis 2023, 20:29
-- Wersja serwera: 10.4.27-MariaDB
-- Wersja PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `dariadb`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `dane_klienci`
--

CREATE TABLE `dane_klienci` (
  `id` int(11) NOT NULL,
  `imie` varchar(255) NOT NULL,
  `nazwisko` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `wiadomosc` text NOT NULL,
  `highlighted` tinyint(1) DEFAULT 0,
  `data_dodania` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `telefon` varchar(20) DEFAULT NULL,
  `statystyki_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `dane_klienci`
--

INSERT INTO `dane_klienci` (`id`, `imie`, `nazwisko`, `email`, `wiadomosc`, `highlighted`, `data_dodania`, `telefon`, `statystyki_id`) VALUES
(41, 'Jan', 'Kowalski', 'jan@kowalski.pl', 'Witam, z tej strony Jan Kowalski. Chciałbym umówić się na wizytę.', 0, '2023-11-18 19:28:10', '123 456 789', NULL);

--
-- Wyzwalacze `dane_klienci`
--
DELIMITER $$
CREATE TRIGGER `after_insert_dane_klienci` AFTER INSERT ON `dane_klienci` FOR EACH ROW BEGIN
    -- Zaktualizuj dzienną liczbę użytkowników w tabeli statystyki
    UPDATE statystyki
    SET dzienna_ilosc_uzytkownikow = dzienna_ilosc_uzytkownikow + 1
    WHERE data = CURRENT_DATE;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `statystyki`
--

CREATE TABLE `statystyki` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL,
  `dzienna_ilosc_uzytkownikow` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `statystyki`
--

INSERT INTO `statystyki` (`id`, `data`, `dzienna_ilosc_uzytkownikow`) VALUES
(0, '2023-11-13', 0);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `dane_klienci`
--
ALTER TABLE `dane_klienci`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_statystyki_id` (`statystyki_id`);

--
-- Indeksy dla tabeli `statystyki`
--
ALTER TABLE `statystyki`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `dane_klienci`
--
ALTER TABLE `dane_klienci`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `dane_klienci`
--
ALTER TABLE `dane_klienci`
  ADD CONSTRAINT `fk_statystyki_id` FOREIGN KEY (`statystyki_id`) REFERENCES `statystyki` (`id`);

DELIMITER $$
--
-- Zdarzenia
--
CREATE DEFINER=`root`@`localhost` EVENT `reset_statystyki_event` ON SCHEDULE EVERY 1 DAY STARTS '2023-11-14 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
  -- Aktualizuj dane w tabeli `statystyki`
  UPDATE `statystyki` SET `dzienna_ilosc_uzytkownikow` = 0 WHERE 1;
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
