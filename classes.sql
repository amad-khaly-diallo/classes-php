-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Oct 02, 2025 at 12:00 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `classes`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `email`, `firstname`, `lastname`) VALUES
(2, 'amad', '$2y$10$utla2lZTTTAFTVz/dvw0lObNA1xgx6Up4c.Dd8ArCaX8oRvgR5mD2', 'amad@mail.com', 'amad', 'diallo'),
(11, 'jdoe', '$2y$10$mdOyL.6KGgNyWqEEqODzHOgL10ycOp.cqOHV2oSoKL6HhR/BwvmvO', 'jdoe@mail.com', 'John', 'Doe'),
(12, 'john.doe', '$2y$10$qUsWUjBTKKLS1zYhrkhyqO7WUo8HYFwiQ8rC5fiz5c8.xuFaPj6zO', 'john@mail.com', 'John', 'Doe'),
(13, 'khaly', '$2y$10$lWDqU.WA4/.AskSZPecu/u8VSseZ.VWpwTwjiRx.EUikClK13bphG', 'john@mail.com', 'John', 'Doe'),
(20, 'jane.doe', '$2y$10$9uQfWsMbXsoRAc0Y0hTxLuBobIdUQawPZ3AVc5IC8z6JAf2QKqp3e', 'jane.doe@mail.com', 'Jane', 'Doe'),
(31, 'john', '$2y$10$c94NMM7wLLipl314sVJKWunV.Idj1Ur/.4hWWhvFMC2zm6O5GdlG6', 'janedoe@mail.com', 'Jane', 'Doe');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
