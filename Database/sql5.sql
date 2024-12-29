-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for ista_project
CREATE DATABASE IF NOT EXISTS `ista_project` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `ista_project`;

-- Dumping structure for table ista_project.admin
CREATE TABLE IF NOT EXISTS `admin` (
  `username` varchar(40) DEFAULT NULL,
  `PASSWORD` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table ista_project.admin: ~1 rows (approximately)
DELETE FROM `admin`;
INSERT INTO `admin` (`username`, `PASSWORD`) VALUES
	('Aziza', 'admin');

-- Dumping structure for table ista_project.classes
CREATE TABLE IF NOT EXISTS `classes` (
  `code_class` varchar(50) NOT NULL,
  `cin` varchar(40) NOT NULL,
  `s_fname` varchar(100) DEFAULT NULL,
  `s_lname` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  PRIMARY KEY (`cin`,`code_class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table ista_project.classes: ~6 rows (approximately)
DELETE FROM `classes`;
INSERT INTO `classes` (`code_class`, `cin`, `s_fname`, `s_lname`, `age`) VALUES
	('DD102', 'da1', 'salm', 'saidi', 19),
	('DD102', 'da2', 'salma', 'ouhoud', 20),
	('DD102', 'da3', 'ilyas', 'elalaoui', 21),
	('DD201', 'db1', 'aboubakr', 'taouil', 20),
	('DD201', 'db2', 'omar', 'bouazzaoui', 21),
	('DD201', 'db3', 'walid', 'elkherak', 19);

-- Dumping structure for table ista_project.student
CREATE TABLE IF NOT EXISTS `student` (
  `cin` varchar(40) NOT NULL,
  `id_card_img` longblob DEFAULT NULL,
  `bac_img` longblob DEFAULT NULL,
  `birth_img` longblob DEFAULT NULL,
  `code_class` varchar(50) NOT NULL,
  PRIMARY KEY (`cin`,`code_class`),
  CONSTRAINT `student_ibfk_1` FOREIGN KEY (`cin`, `code_class`) REFERENCES `classes` (`cin`, `code_class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table ista_project.student: ~0 rows (approximately)
DELETE FROM `student`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
