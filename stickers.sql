-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 05 Kwi 2018, 08:39
-- Wersja serwera: 10.1.30-MariaDB
-- Wersja PHP: 5.6.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `stickers`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `did` varchar(16) COLLATE utf8_polish_ci NOT NULL,
  `uid` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `content` varchar(2048) COLLATE utf8_polish_ci NOT NULL,
  `link` varchar(256) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `fields`
--

CREATE TABLE `fields` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `title` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `required` int(11) NOT NULL,
  `field_type` int(11) NOT NULL COMMENT '0 - input; 1 - select; 2 - textarea'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `fields`
--

INSERT INTO `fields` (`id`, `type_id`, `name`, `title`, `required`, `field_type`) VALUES
(1, 1, 'PCmodel', 'Model', 1, 1),
(2, 1, 'PCvncs', 'VNCS', 0, 0),
(3, 1, 'PCip', 'Adres IP', 0, 0),
(4, 1, 'PCmac', 'Adres MAC', 0, 0),
(5, 2, 'KAmodel', 'Model', 1, 1),
(6, 2, 'KAvncs', 'VNCS', 0, 0),
(7, 2, 'KAip', 'Adres IP', 0, 0),
(8, 2, 'KAmac', 'Adres MAC', 0, 0),
(9, 1, 'PCserial', 'Numer seryjny', 1, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `fieldselect`
--

CREATE TABLE `fieldselect` (
  `id` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `fieldselect`
--

INSERT INTO `fieldselect` (`id`, `fid`, `name`) VALUES
(1, 1, 'Dell E6400'),
(2, 1, 'Dell D630'),
(3, 5, 'Asus'),
(4, 5, 'Lenovo');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `fieldvalue`
--

CREATE TABLE `fieldvalue` (
  `id` int(11) NOT NULL,
  `name` varchar(16) COLLATE utf8_polish_ci NOT NULL,
  `fieldname` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `value` varchar(2048) COLLATE utf8_polish_ci NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `firewall`
--

CREATE TABLE `firewall` (
  `id` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `tag` varchar(8) COLLATE utf8_polish_ci NOT NULL,
  `ip` varchar(16) COLLATE utf8_polish_ci NOT NULL,
  `stand` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `firewall`
--

INSERT INTO `firewall` (`id`, `name`, `tag`, `ip`, `stand`) VALUES
(3, 'Radom Telemarketing', 'WRT', '79.110.193.7', 64),
(4, 'Radom Potwierdzenia', 'WRP', '79.110.193.138', 52),
(5, 'Lublin Telemarketing', 'LUT', '94.75.73.82', 51),
(6, 'Lublin Potwierdzenia', 'LUP', '46.170.22.50', 41),
(7, 'Kraśnik', 'LKR', '79.110.194.145', 30),
(8, 'Zamość', 'LZA', '78.11.105.74', 44),
(9, 'Chełm', 'LCH', '185.74.85.166', 40),
(10, 'Białystok', 'BIA', '109.231.56.233', 46),
(11, 'Ostrowiec', 'TOS', '77.252.217.170', 40),
(12, 'Skarżysko', 'TSK', '78.11.105.74', 41),
(13, 'Starachowice', 'TST', '91.227.66.4', 29),
(14, 'Łódź', 'LDZ', '91.205.72.122', 32),
(15, 'Radom DKJ', 'DKJ', '79.110.193.7', 36),
(16, 'Magazyn', 'MGN', '79.110.193.7', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `scan`
--

CREATE TABLE `scan` (
  `id` int(11) NOT NULL,
  `ip` varchar(16) COLLATE utf8_polish_ci NOT NULL,
  `name` varchar(11) COLLATE utf8_polish_ci NOT NULL,
  `date` datetime NOT NULL,
  `uid` int(11) NOT NULL,
  `department` varchar(8) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `name` varchar(16) COLLATE utf8_polish_ci NOT NULL,
  `status` int(11) NOT NULL COMMENT '1 - przy dodaniu; 2 - niesprawny',
  `uid` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `types`
--

CREATE TABLE `types` (
  `id` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `tag` varchar(2) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `types`
--

INSERT INTO `types` (`id`, `name`, `tag`) VALUES
(1, 'Komputer Konsultant', 'PC'),
(2, 'Komputer Kadra', 'KA'),
(3, 'Myszka', 'MY'),
(4, 'Słuchawki', 'SL'),
(5, 'Drukarka', 'DR'),
(6, 'Router', 'RO'),
(7, 'Switch', 'SW'),
(8, 'Monitor', 'MO'),
(9, 'Telewizor', 'TV'),
(10, 'Tablet', 'TB');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `password` varchar(48) COLLATE utf8_polish_ci NOT NULL,
  `name` varchar(32) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`) VALUES
(1, 'mateusz.zybura', 'Zybura', 'Mateusz Zybura'),
(2, 'natalia.skwarek', 'Skwarek', 'Natalia Skwarek'),
(3, 'mateusz.marek', 'Marek', 'Mateusz Marek'),
(4, 'mateusz.popiel', 'Popiel', 'Mateusz popiel'),
(5, 'szymon.duliasz', 'Duliasz', 'Szymon Duliasz'),
(6, 'agata.matysiak', 'Matysiak', 'Agata Matysiak'),
(7, 'pawel.zielinski', 'Zielinski', 'Paweł Zieliński');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fields`
--
ALTER TABLE `fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fieldselect`
--
ALTER TABLE `fieldselect`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fieldvalue`
--
ALTER TABLE `fieldvalue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `firewall`
--
ALTER TABLE `firewall`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scan`
--
ALTER TABLE `scan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `fields`
--
ALTER TABLE `fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `fieldselect`
--
ALTER TABLE `fieldselect`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT dla tabeli `fieldvalue`
--
ALTER TABLE `fieldvalue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `firewall`
--
ALTER TABLE `firewall`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT dla tabeli `scan`
--
ALTER TABLE `scan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `types`
--
ALTER TABLE `types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
