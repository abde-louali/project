-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 15 jan. 2025 à 00:12
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ista_project`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `username` varchar(40) DEFAULT NULL,
  `PASSWORD` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`username`, `PASSWORD`) VALUES
('aziza', 'Aziza123!');

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

CREATE TABLE `classes` (
  `code_class` varchar(50) NOT NULL,
  `filier_name` varchar(40) NOT NULL,
  `cin` varchar(40) NOT NULL,
  `s_fname` varchar(100) DEFAULT NULL,
  `s_lname` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `classes`
--

INSERT INTO `classes` (`code_class`, `filier_name`, `cin`, `s_fname`, `s_lname`, `age`) VALUES
('DD201', 'DEVELOPMENT DIGITAL', 'AA901234', 'Samira', 'El Hajji', 22),
('DD201', 'DEVELOPMENT DIGITAL', 'AAA56789', 'Abd samad', 'louli', 20),
('GS202', 'GESTION ENTREPRISE', 'AAA56789', 'Jamal', 'El Bouhaddi', 20),
('DD101', 'DEVELOPMENT DIGITAL', 'AB123456', 'Ahmed', 'Benali', 20),
('DD201', 'DEVELOPMENT DIGITAL', 'BB567890', 'Rachid', 'Tazi', 23),
('DD101', 'DEVELOPMENT DIGITAL', 'BBB12345', 'Khadija', 'El Moutawakil', 21),
('DD202', 'DEVELOPMENT DIGITAL', 'CC123456', 'Noura', 'Boukhari', 20),
('DD102', 'DEVELOPMENT DIGITAL', 'CCC67890', 'Yassir', 'El Fassi', 22),
('DD101', 'DEVELOPMENT DIGITAL', 'CD789012', 'Fatima', 'Zahra', 21),
('DD201', 'DEVELOPMENT DIGITAL', 'DB30143', 'nisrine', 'el atmani', 19),
('DD202', 'DEVELOPMENT DIGITAL', 'DD789012', 'Adil', 'El Ouazzani', 21),
('DD201', 'DEVELOPMENT DIGITAL', 'DDD12345', 'Said', 'El Ouafi', 23),
('DD202', 'DEVELOPMENT DIGITAL', 'EE345678', 'Hafsa', 'Bennani', 22),
('DD202', 'DEVELOPMENT DIGITAL', 'EEE67890', 'Nadia', 'El Kadi', 19),
('DD101', 'DEVELOPMENT DIGITAL', 'EF345678', 'Youssef', 'El Mansouri', 22),
('DD202', 'DEVELOPMENT DIGITAL', 'FF901234', 'Yassine', 'El Khatib', 23),
('GS101', 'GESTION ENTREPRISE', 'FFF12345', 'Hassan', 'El Moussaoui', 20),
('DD202', 'DEVELOPMENT DIGITAL', 'GG567890', 'Asmae', 'El Fahsi', 19),
('GS102', 'GESTION ENTREPRISE', 'GGG67890', 'Samir', 'El Boukili', 21),
('DD101', 'DEVELOPMENT DIGITAL', 'GH901234', 'Amina', 'Toumi', 19),
('GS101', 'GESTION ENTREPRISE', 'HH123456', 'Mohamed', 'El Amrani', 20),
('GS201', 'GESTION ENTREPRISE', 'HHH12345', 'Noureddine', 'El Khatib', 22),
('GS101', 'GESTION ENTREPRISE', 'II789012', 'Zahra', 'El Idrissi', 21),
('GS202', 'GESTION ENTREPRISE', 'III67890', 'Latifa', 'El Harrak', 23),
('DD101', 'DEVELOPMENT DIGITAL', 'IJ567890', 'Karim', 'Bouhaddi', 20),
('GS101', 'GESTION ENTREPRISE', 'JJ345678', 'Othman', 'Boujema', 22),
('DD101', 'DEVELOPMENT DIGITAL', 'JJJ12345', 'Mohamed', 'El Amraoui', 20),
('GS101', 'GESTION ENTREPRISE', 'KK901234', 'Souad', 'El Moussaoui', 19),
('DD102', 'DEVELOPMENT DIGITAL', 'KKK67890', 'Zakaria', 'El Fahsi', 21),
('DD102', 'DEVELOPMENT DIGITAL', 'KL123456', 'Leila', 'Chraibi', 21),
('GS101', 'GESTION ENTREPRISE', 'LL567890', 'Anas', 'El Kharraz', 20),
('GS102', 'GESTION ENTREPRISE', 'MM123456', 'Houda', 'El Fassi', 21),
('DD102', 'DEVELOPMENT DIGITAL', 'MN789012', 'Omar', 'Khalidi', 20),
('GS102', 'GESTION ENTREPRISE', 'NN789012', 'Rachida', 'Bouhlal', 22),
('GS102', 'GESTION ENTREPRISE', 'OO345678', 'Hamza', 'El Ghazi', 23),
('DD102', 'DEVELOPMENT DIGITAL', 'OP345678', 'Nadia', 'El Fassi', 22),
('GS102', 'GESTION ENTREPRISE', 'PP901234', 'Imane', 'El Ouafi', 19),
('GS102', 'GESTION ENTREPRISE', 'QQ567890', 'Younes', 'El Haddad', 20),
('DD102', 'DEVELOPMENT DIGITAL', 'QR901234', 'Hassan', 'Rami', 23),
('GS201', 'GESTION ENTREPRISE', 'RR123456', 'Kawtar', 'El Mernissi', 21),
('GS201', 'GESTION ENTREPRISE', 'SS789012', 'Abdelilah', 'El Hassani', 22),
('DD102', 'DEVELOPMENT DIGITAL', 'ST567890', 'Sanaa', 'Bennani', 19),
('GS201', 'GESTION ENTREPRISE', 'TT345678', 'Nabila', 'El Kacemi', 23),
('GS201', 'GESTION ENTREPRISE', 'UU901234', 'Reda', 'El Boukili', 19),
('DD201', 'DEVELOPMENT DIGITAL', 'UV123456', 'Mehdi', 'El Kadi', 24),
('GS201', 'GESTION ENTREPRISE', 'VV567890', 'Safae', 'El Moustaghfir', 20),
('GS202', 'GESTION ENTREPRISE', 'WW123456', 'Hicham', 'El Guerrouj', 21),
('DD201', 'DEVELOPMENT DIGITAL', 'WX789012', 'Zineb', 'El Amrani', 20),
('GS202', 'GESTION ENTREPRISE', 'XX789012', 'Naima', 'El Khayat', 22),
('GS202', 'GESTION ENTREPRISE', 'YY345678', 'Fouad', 'El Amraoui', 23),
('DD201', 'DEVELOPMENT DIGITAL', 'YZ345678', 'Khalid', 'Bouzidi', 21),
('GS202', 'GESTION ENTREPRISE', 'ZZ901234', 'Hayat', 'El Harrak', 19);

-- --------------------------------------------------------

--
-- Structure de la table `student`
--

CREATE TABLE `student` (
  `cin` varchar(40) NOT NULL,
  `s_fname` varchar(100) NOT NULL,
  `s_lname` varchar(100) NOT NULL,
  `id_card_img` longblob DEFAULT NULL,
  `bac_img` longblob DEFAULT NULL,
  `birth_img` longblob DEFAULT NULL,
  `code_class` varchar(50) NOT NULL,
  `filier_name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`cin`,`code_class`,`filier_name`);

--
-- Index pour la table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`cin`,`code_class`,`filier_name`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`cin`,`code_class`,`filier_name`) REFERENCES `classes` (`cin`, `code_class`, `filier_name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
