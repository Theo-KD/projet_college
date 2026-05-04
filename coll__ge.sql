-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 04 mai 2026 à 14:21
-- Version du serveur : 9.1.0
-- Version de PHP : 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `collège`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$12$bDXO6rOuQGwiFoNpOT2ateAap4rpHX04RXM14tGCJfOC42mRKRFb.', '2026-01-15 20:41:35');

-- --------------------------------------------------------

--
-- Structure de la table `carousel`
--

DROP TABLE IF EXISTS `carousel`;
CREATE TABLE IF NOT EXISTS `carousel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `text` text,
  `position` int DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `carousel`
--

INSERT INTO `carousel` (`id`, `image`, `title`, `subtitle`, `text`, `position`, `active`) VALUES
(1, 'img1.png', 'Accompagnement des élèves', 'salle A12', 'en ce mardi 13 janvier la salle informatique sera ouverte afin d\'accompagner les élèves dans leurs démarche de validation pix', 1, 1),
(2, 'Néo.png', 'Télécharger néo', 'Pour une meilleur interaction avec votre établissement ', 'Et les membres qui le constitut.', 2, 1),
(3, 'img3.jpg', 'Rappel', 'Maintenance', 'Une maintenance aura lieu mardi prochains', 3, 1);

-- --------------------------------------------------------

--
-- Structure de la table `contenu`
--

DROP TABLE IF EXISTS `contenu`;
CREATE TABLE IF NOT EXISTS `contenu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `content_file` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `contenu`
--

INSERT INTO `contenu` (`id`, `titre`, `content_file`, `created_at`, `updated_at`) VALUES
(8, 'anniversaire de mr Leblanc', 'col6/documents/doc_69724d66bbd31.docx', '2026-01-22 12:16:38', NULL),
(7, 'xaxaxaxa', 'col6/documents/doc_696a4433a319c.docx', '2026-01-16 09:59:15', '2026-01-22 12:11:20');

-- --------------------------------------------------------

--
-- Structure de la table `contenu_type`
--

DROP TABLE IF EXISTS `contenu_type`;
CREATE TABLE IF NOT EXISTS `contenu_type` (
  `contenu_id` int NOT NULL,
  `type_id` int NOT NULL,
  PRIMARY KEY (`contenu_id`,`type_id`),
  KEY `idx_ct_type` (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `contenu_type`
--

INSERT INTO `contenu_type` (`contenu_id`, `type_id`) VALUES
(7, 12),
(7, 15),
(7, 16),
(7, 23),
(7, 26),
(8, 6),
(8, 8),
(8, 10),
(8, 25);

-- --------------------------------------------------------

--
-- Structure de la table `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE IF NOT EXISTS `image` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contenu_id` int NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `position` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_image_contenu` (`contenu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `image`
--

INSERT INTO `image` (`id`, `contenu_id`, `image_path`, `position`) VALUES
(7, 8, 'col6/img/contenu/img_69724d66bf869.jpeg', 1),
(6, 7, 'col6/img/contenu/img_696a4433aa60e.png', 1);

-- --------------------------------------------------------

--
-- Structure de la table `infos_etablissement`
--

DROP TABLE IF EXISTS `infos_etablissement`;
CREATE TABLE IF NOT EXISTS `infos_etablissement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nb_eleves` int NOT NULL,
  `chef_etablissement` varchar(255) NOT NULL,
  `adjoint` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `infos_etablissement`
--

INSERT INTO `infos_etablissement` (`id`, `nb_eleves`, `chef_etablissement`, `adjoint`) VALUES
(1, 653, 'M.ADONAI TONY', 'MME.MAUGENNE BETTY');

-- --------------------------------------------------------

--
-- Structure de la table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `content_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `news`
--

INSERT INTO `news` (`id`, `title`, `image`, `created_at`, `updated_at`, `content_file`) VALUES
(34, 'chien chien', '[\"col6/img/news/img_697208a9264bc.jpeg\"]', '2026-01-22 07:23:21', NULL, 'col6/documents/doc_697208a925141.docx'),
(33, 'abcd', '[\"col6/img/news/img_696e1a4f15fba.png\"]', '2026-01-19 07:49:35', NULL, 'col6/documents/doc_696e1a4f1501e.docx');

-- --------------------------------------------------------

--
-- Structure de la table `type`
--

DROP TABLE IF EXISTS `type`;
CREATE TABLE IF NOT EXISTS `type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `type`
--

INSERT INTO `type` (`id`, `slug`, `nom`) VALUES
(4, 'projet-etablissement', 'Projet d’établissement'),
(2, 'presentation', 'Présentation'),
(1, 'services-organigramme', 'beta-test'),
(5, 'conseil-ecoles-college', 'Conseil écoles collège'),
(6, 'restauration', 'Restauration'),
(7, 'contact', 'Contact'),
(8, 'examen', 'Examens / formations'),
(9, 'information', 'Informations de la direction'),
(10, 'parent', 'Parents d’élèves'),
(11, 'numerique-emi', 'Numérique EMI & innovation pédagogique'),
(12, 'atelier-jeudi', 'Atelier du jeudi'),
(13, 'peac', 'Parcours d’éducation culturelle et artistique'),
(14, 'parcours-sante-citoyennete', 'Parcours santé & citoyenneté'),
(15, 'assistante', 'Assistante sociale'),
(16, 'association', 'Association sportive'),
(17, 'cesc', 'CESC'),
(18, 'cvc', 'CVC Conseil de vie collégienne'),
(19, 'infirmieres', 'Infirmières'),
(20, 'cordees-reussite', 'Cordées de la réussite'),
(21, 'liens-utiles', 'Liens utiles'),
(22, 'parcours-avenir-sites', 'Parcours Avenir – sites indispensables'),
(23, 'eleve-top', 'Élèves'),
(24, 'parent-top', 'Parents'),
(25, 'enseignant-top', 'Enseignants'),
(26, 'autre-top', 'Autres');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
