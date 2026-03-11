SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `takenlijst`
--
DROP DATABASE IF EXISTS `takenlijst`;
CREATE DATABASE IF NOT EXISTS `takenlijst` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `takenlijst`;

--
-- Tabel: `taken`
--

DROP TABLE IF EXISTS `taken`;
CREATE TABLE IF NOT EXISTS `taken` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(255) NOT NULL,
  `beschrijving` text NOT NULL,
  `afdeling` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'todo',
  `deadline` date DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(255) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`naam`, `username`, `password`) VALUES ('Testgebruiker 1', 'user1', '1234');
INSERT INTO `users` (`naam`, `username`, `password`) VALUES ('Testgebruiker 2', 'user2', '12345');
INSERT INTO `users` (`naam`, `username`, `password`) VALUES ('Testgebruiker 3', 'user3', '123456');