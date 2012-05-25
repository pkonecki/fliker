-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Ven 25 Mai 2012 à 14:42
-- Version du serveur: 5.5.8
-- Version de PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `asesco`
--

-- --------------------------------------------------------

--
-- Structure de la table `fliker_activite`
--

CREATE TABLE IF NOT EXISTS `fliker_activite` (
  `id` int(16) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `url` varchar(1024) NOT NULL,
  `id_sec` int(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_sec` (`id_sec`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_adherent`
--

CREATE TABLE IF NOT EXISTS `fliker_adherent` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `privilege` int(1) NOT NULL DEFAULT '0',
  `numcarte` varchar(255) NOT NULL,
  `numayantdroit` varchar(255) NOT NULL,
  `categorie` varchar(255) NOT NULL,
  `id_statut` int(16) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `naissance` date NOT NULL,
  `photo` tinyint(4) NOT NULL,
  `certmed` varchar(255) NOT NULL,
  `tel1` varchar(255) NOT NULL,
  `tel2` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `adresse1` varchar(255) NOT NULL,
  `adresse2` varchar(255) NOT NULL,
  `code_postal` varchar(255) NOT NULL,
  `adresse_pro` varchar(255) NOT NULL,
  `last_modif` datetime NOT NULL,
  `charte` tinyint(1) NOT NULL,
  `assurance` tinyint(4) NOT NULL,
  `droit_image` tinyint(4) NOT NULL,
  `date_creation` datetime NOT NULL,
  `contact_urgence` varchar(255) NOT NULL,
  `contact_urgence_tel` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `activationkey` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `add_mail_temp` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_statut` (`id_statut`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=369 ;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_adhesion`
--

CREATE TABLE IF NOT EXISTS `fliker_adhesion` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `id_adh` int(16) NOT NULL,
  `id_cre` int(16) NOT NULL,
  `statut` int(4) NOT NULL,
  `promo` int(4) NOT NULL,
  `id_asso` int(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_adh` (`id_adh`),
  KEY `id_cre` (`id_cre`),
  KEY `id_asso` (`id_asso`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=615 ;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_association`
--

CREATE TABLE IF NOT EXISTS `fliker_association` (
  `id` int(16) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_asso_section`
--

CREATE TABLE IF NOT EXISTS `fliker_asso_section` (
  `id_asso` int(16) NOT NULL,
  `id_sec` int(16) NOT NULL,
  KEY `id_asso` (`id_asso`,`id_sec`),
  KEY `id_sec` (`id_sec`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_champs_adherent`
--

CREATE TABLE IF NOT EXISTS `fliker_champs_adherent` (
  `nom` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `inscription` tinyint(1) NOT NULL,
  `user_editable` tinyint(1) NOT NULL,
  `user_viewable` tinyint(4) NOT NULL DEFAULT '0',
  `search_simple` tinyint(4) NOT NULL,
  `search_trombi` tinyint(4) NOT NULL,
  `format` varchar(255) NOT NULL,
  `ordre` int(16) NOT NULL,
  `required` tinyint(1) NOT NULL,
  PRIMARY KEY (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_config`
--

CREATE TABLE IF NOT EXISTS `fliker_config` (
  `id` varchar(255) NOT NULL,
  `valeur` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_creneau`
--

CREATE TABLE IF NOT EXISTS `fliker_creneau` (
  `id` int(16) NOT NULL,
  `jour` varchar(255) NOT NULL,
  `debut` time NOT NULL,
  `fin` time NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `id_act` int(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_act` (`id_act`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_entite`
--

CREATE TABLE IF NOT EXISTS `fliker_entite` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=128 ;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_paiement`
--

CREATE TABLE IF NOT EXISTS `fliker_paiement` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `date_t` date NOT NULL,
  `id_adh` int(16) NOT NULL,
  `type` varchar(255) NOT NULL,
  `num` varchar(255) NOT NULL,
  `remarque` varchar(255) NOT NULL,
  `promo` int(4) NOT NULL,
  `recorded_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_adh_2` (`id_adh`),
  KEY `recorded_by` (`recorded_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=273 ;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_paiement_sup`
--

CREATE TABLE IF NOT EXISTS `fliker_paiement_sup` (
  `id_paiement` int(16) NOT NULL,
  `id_sup` int(16) NOT NULL,
  `valeur` double NOT NULL,
  KEY `id_paiment` (`id_paiement`,`id_sup`),
  KEY `id_adhesion` (`id_sup`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_presence`
--

CREATE TABLE IF NOT EXISTS `fliker_presence` (
  `id_adh` int(16) NOT NULL,
  `id_cre` int(16) NOT NULL,
  `week` int(2) NOT NULL,
  `promo` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_resp_act`
--

CREATE TABLE IF NOT EXISTS `fliker_resp_act` (
  `id_act` int(16) NOT NULL,
  `id_adh` int(16) NOT NULL,
  PRIMARY KEY (`id_act`,`id_adh`),
  KEY `id_adh` (`id_adh`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_resp_asso`
--

CREATE TABLE IF NOT EXISTS `fliker_resp_asso` (
  `id_asso` int(16) NOT NULL,
  `id_adh` int(16) NOT NULL,
  PRIMARY KEY (`id_asso`,`id_adh`),
  KEY `id_adh` (`id_adh`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_resp_cren`
--

CREATE TABLE IF NOT EXISTS `fliker_resp_cren` (
  `id_cre` int(16) NOT NULL,
  `id_adh` int(16) NOT NULL,
  PRIMARY KEY (`id_cre`,`id_adh`),
  KEY `id_adh` (`id_adh`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_resp_section`
--

CREATE TABLE IF NOT EXISTS `fliker_resp_section` (
  `id_sec` int(16) NOT NULL,
  `id_adh` int(16) NOT NULL,
  PRIMARY KEY (`id_sec`,`id_adh`),
  KEY `id_adh` (`id_adh`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_section`
--

CREATE TABLE IF NOT EXISTS `fliker_section` (
  `id` int(16) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `url` varchar(1024) NOT NULL,
  `description` longtext NOT NULL,
  `logo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_statut`
--

CREATE TABLE IF NOT EXISTS `fliker_statut` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_sup`
--

CREATE TABLE IF NOT EXISTS `fliker_sup` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `promo` int(4) NOT NULL,
  `type` varchar(255) NOT NULL,
  `valeur` double NOT NULL,
  `id_statut` int(16) DEFAULT NULL,
  `id_asso_adh` int(16) DEFAULT NULL,
  `id_asso_paie` int(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_statut` (`id_statut`),
  KEY `id_asso_adh` (`id_asso_adh`),
  KEY `id_asso_paie` (`id_asso_paie`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=228 ;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_sup_fk`
--

CREATE TABLE IF NOT EXISTS `fliker_sup_fk` (
  `id_ent` int(16) NOT NULL,
  `id_sup` int(16) NOT NULL,
  PRIMARY KEY (`id_ent`,`id_sup`),
  KEY `id` (`id_ent`),
  KEY `id_sup` (`id_sup`),
  KEY `id_2` (`id_ent`,`id_sup`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `fliker_activite`
--
ALTER TABLE `fliker_activite`
  ADD CONSTRAINT `activite_ibfk_2` FOREIGN KEY (`id`) REFERENCES `fliker_entite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activite_ibfk_3` FOREIGN KEY (`id_sec`) REFERENCES `fliker_section` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_adherent`
--
ALTER TABLE `fliker_adherent`
  ADD CONSTRAINT `adherent_ibfk_1` FOREIGN KEY (`id_statut`) REFERENCES `fliker_statut` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_adhesion`
--
ALTER TABLE `fliker_adhesion`
  ADD CONSTRAINT `adhesion_ibfk_3` FOREIGN KEY (`id_adh`) REFERENCES `fliker_adherent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adhesion_ibfk_4` FOREIGN KEY (`id_cre`) REFERENCES `fliker_creneau` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adhesion_ibfk_5` FOREIGN KEY (`id_asso`) REFERENCES `fliker_association` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_association`
--
ALTER TABLE `fliker_association`
  ADD CONSTRAINT `association_ibfk_1` FOREIGN KEY (`id`) REFERENCES `fliker_entite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_asso_section`
--
ALTER TABLE `fliker_asso_section`
  ADD CONSTRAINT `asso_section_ibfk_1` FOREIGN KEY (`id_asso`) REFERENCES `fliker_association` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `asso_section_ibfk_2` FOREIGN KEY (`id_sec`) REFERENCES `fliker_section` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_creneau`
--
ALTER TABLE `fliker_creneau`
  ADD CONSTRAINT `creneau_ibfk_2` FOREIGN KEY (`id`) REFERENCES `fliker_entite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `creneau_ibfk_3` FOREIGN KEY (`id_act`) REFERENCES `fliker_activite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_paiement`
--
ALTER TABLE `fliker_paiement`
  ADD CONSTRAINT `paiement_ibfk_1` FOREIGN KEY (`id_adh`) REFERENCES `fliker_adherent` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_paiement_sup`
--
ALTER TABLE `fliker_paiement_sup`
  ADD CONSTRAINT `paiement_sup_ibfk_1` FOREIGN KEY (`id_paiement`) REFERENCES `fliker_paiement` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `paiement_sup_ibfk_2` FOREIGN KEY (`id_sup`) REFERENCES `fliker_sup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_resp_act`
--
ALTER TABLE `fliker_resp_act`
  ADD CONSTRAINT `resp_act_ibfk_1` FOREIGN KEY (`id_act`) REFERENCES `fliker_activite` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `resp_act_ibfk_2` FOREIGN KEY (`id_adh`) REFERENCES `fliker_adherent` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_resp_asso`
--
ALTER TABLE `fliker_resp_asso`
  ADD CONSTRAINT `resp_asso_ibfk_1` FOREIGN KEY (`id_asso`) REFERENCES `fliker_association` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `resp_asso_ibfk_2` FOREIGN KEY (`id_adh`) REFERENCES `fliker_adherent` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_resp_cren`
--
ALTER TABLE `fliker_resp_cren`
  ADD CONSTRAINT `resp_cren_ibfk_1` FOREIGN KEY (`id_cre`) REFERENCES `fliker_creneau` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `resp_cren_ibfk_2` FOREIGN KEY (`id_adh`) REFERENCES `fliker_adherent` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_resp_section`
--
ALTER TABLE `fliker_resp_section`
  ADD CONSTRAINT `resp_section_ibfk_1` FOREIGN KEY (`id_sec`) REFERENCES `fliker_section` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `resp_section_ibfk_2` FOREIGN KEY (`id_adh`) REFERENCES `fliker_adherent` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_section`
--
ALTER TABLE `fliker_section`
  ADD CONSTRAINT `section_ibfk_1` FOREIGN KEY (`id`) REFERENCES `fliker_entite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_sup`
--
ALTER TABLE `fliker_sup`
  ADD CONSTRAINT `sup_ibfk_1` FOREIGN KEY (`id_statut`) REFERENCES `fliker_statut` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sup_ibfk_2` FOREIGN KEY (`id_asso_adh`) REFERENCES `fliker_association` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sup_ibfk_3` FOREIGN KEY (`id_asso_paie`) REFERENCES `fliker_association` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_sup_fk`
--
ALTER TABLE `fliker_sup_fk`
  ADD CONSTRAINT `sup_fk_ibfk_1` FOREIGN KEY (`id_sup`) REFERENCES `fliker_sup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sup_fk_ibfk_2` FOREIGN KEY (`id_ent`) REFERENCES `fliker_entite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
