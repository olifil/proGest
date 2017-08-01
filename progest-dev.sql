-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 06 Mai 2016 à 10:46
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `progest-dev`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fournisseur_id` int(11) NOT NULL,
  `reference` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `descriptif` longtext COLLATE utf8_unicode_ci,
  `prix_achat` decimal(6,2) NOT NULL,
  `date` datetime NOT NULL,
  `livre` int(11) DEFAULT '0',
  `vendu` int(11) DEFAULT '0',
  `stock` int(11) DEFAULT '0',
  `isActive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `IDX_23A0E66670C757F` (`fournisseur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `boutique`
--

CREATE TABLE IF NOT EXISTS `boutique` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `codepostal` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `ville` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `telephone` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `marge` int(11) NOT NULL,
  `isactive` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `fournisseur`
--

CREATE TABLE IF NOT EXISTS `fournisseur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cp` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ville` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tel` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cheque` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `prix_multiple`
--

CREATE TABLE IF NOT EXISTS `prix_multiple` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) DEFAULT NULL,
  `quantite` int(11) NOT NULL,
  `prix` decimal(6,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7E8864767294869C` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `variation`
--

CREATE TABLE IF NOT EXISTS `variation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) DEFAULT NULL,
  `vente_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `quantite` int(11) NOT NULL,
  `prixVente` decimal(6,2) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `IDX_629B33EA7294869C` (`article_id`),
  KEY `IDX_629B33EA7DC7170A` (`vente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `vente`
--

CREATE TABLE IF NOT EXISTS `vente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomClient` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prenomClient` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresseClient` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cpClient` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `communeClient` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nbrArticles` int(11) DEFAULT NULL,
  `totalVente` double DEFAULT NULL,
  `paymentMode` varchar(9) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateVente` datetime NOT NULL,
  `isValidate` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `FK_23A0E66670C757F` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseur` (`id`);

--
-- Contraintes pour la table `prix_multiple`
--
ALTER TABLE `prix_multiple`
  ADD CONSTRAINT `FK_7E8864767294869C` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`);

--
-- Contraintes pour la table `variation`
--
ALTER TABLE `variation`
  ADD CONSTRAINT `FK_629B33EA7DC7170A` FOREIGN KEY (`vente_id`) REFERENCES `vente` (`id`),
  ADD CONSTRAINT `FK_629B33EA7294869C` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
