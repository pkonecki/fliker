-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mer 31 Août 2011 à 16:01
-- Version du serveur: 5.1.44
-- Version de PHP: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `fliker`
--

-- --------------------------------------------------------

--
-- Structure de la table `activite`
--

CREATE TABLE IF NOT EXISTS `activite` (
  `id` int(16) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `url` varchar(1024) NOT NULL,
  `id_sec` int(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_sec` (`id_sec`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `activite`
--

INSERT INTO `activite` (`id`, `nom`, `description`, `url`, `id_sec`) VALUES
(5, 'Kung Fu', 'Gigalol', 'http://kungfu.com', 3),
(6, 'Tai Chi', '.....', 'http://taichi.com', 3),
(7, 'Baby Foot', '', 'http://somurl.com', 4);

-- --------------------------------------------------------

--
-- Structure de la table `adherent`
--

CREATE TABLE IF NOT EXISTS `adherent` (
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
  PRIMARY KEY (`id`),
  KEY `id_statut` (`id_statut`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=43 ;

--
-- Contenu de la table `adherent`
--

INSERT INTO `adherent` (`id`, `privilege`, `numcarte`, `numayantdroit`, `categorie`, `id_statut`, `prenom`, `nom`, `naissance`, `photo`, `certmed`, `tel1`, `tel2`, `email`, `adresse1`, `adresse2`, `code_postal`, `adresse_pro`, `last_modif`, `charte`, `assurance`, `droit_image`, `date_creation`, `contact_urgence`, `contact_urgence_tel`, `active`, `activationkey`, `password`) VALUES
(28, 1, '', '2030322269', 'M', 13, 'Pierre-Olivier', 'Konecki', '1987-07-08', 1, '1', '0672536844', '979878968', 'pkonecki@gmail.com', '5 Place de la Muette', '', '78990', 'PROUT', '2011-08-29 10:37:55', 1, 1, 1, '2011-05-14 15:04:08', 'pmarty', '', 1, '', '5d41402abc4b2a76b9719d911017c592'),
(34, 0, '97987', '65465464', 'M', 5, 'Jackie', 'Chan', '1971-06-09', 1, '0', '3646456465', '6546546546', 'jackie.chan@kungfu.com', '2 rue du Soleil Levant', '', '91440', '', '2011-08-09 11:53:43', 1, 0, 0, '2011-06-05 21:00:19', 'Bruce Lee', '', 1, '', '5d41402abc4b2a76b9719d911017c592'),
(35, 0, '', '11', 'M', 3, 'Zinédine', 'Zidane', '1914-06-11', 1, '', '01238934', '9374934', 'zinedine@football.com', '3 rue du Ballon Rond', '', '75001', '', '2011-06-13 17:16:55', 1, 0, 0, '2011-06-06 09:17:16', 'Fabien Barthez', '', 1, '', '5d41402abc4b2a76b9719d911017c592'),
(36, 0, '', '97979878', 'F', 6, 'Aurore', 'Deberon', '1988-11-27', 1, '', '987979879', '98797979', 'aurore2127@gmail.com', '12 avenue du Belvédère', '', '91190', '', '2011-06-13 16:24:14', 1, 0, 0, '2011-06-10 11:42:50', 'kjdfdksjf', '', 1, '', '5d41402abc4b2a76b9719d911017c592'),
(37, 0, '', '09809809', 'M', 13, 'John', 'Doe', '1987-07-08', 1, '', '09808098', '0980989', 'john.doe@mail.com', 'Nulle Part', '', '99999', '', '2011-06-16 18:27:40', 1, 0, 0, '2011-06-16 18:24:25', 'L''homme invisible', '', 0, '', '5d41402abc4b2a76b9719d911017c592'),
(38, 0, '', '08098', 'M', 13, 'Man', 'Test', '2011-07-06', 1, '', '098098', '0980808', 'gros@bill.com', 'jhfsdk', '09098', '09808', '', '2011-07-07 21:15:29', 1, 0, 0, '2011-07-07 21:15:29', '098098', '', 1, '', '5d41402abc4b2a76b9719d911017c592'),
(41, 1, '', '12345', 'M', 13, 'philippe', 'marty', '1973-02-18', 1, '', '0678912345', '0123456789', 'pmarty@geocities.com', '12 allée des 3 quarts', '', '91234', '', '2011-07-27 10:41:41', 1, 0, 0, '2011-07-27 10:41:41', 'moi', '', 1, '', '5d41402abc4b2a76b9719d911017c592'),
(42, 0, '', '20303222', 'M', 13, 'LKDJGLSDFK', 'LDSKFJDSLK', '1986-06-26', 1, '1', '33672536844', '33672536844', 'meneo.tk@gmail.com', 'My Billing Address', 'Megaupload', '91440', '', '2011-08-26 08:52:42', 1, 0, 0, '2011-08-26 08:52:42', 'Maman 66666', '', 1, '', '5d41402abc4b2a76b9719d911017c592');

-- --------------------------------------------------------

--
-- Structure de la table `adhesion`
--

CREATE TABLE IF NOT EXISTS `adhesion` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `id_adh` int(16) NOT NULL,
  `id_cre` int(16) NOT NULL,
  `statut` int(4) NOT NULL,
  `promo` int(4) NOT NULL,
  `id_asso` int(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_adh` (`id_adh`),
  KEY `id_cre` (`id_cre`),
  KEY `id_asso` (`id_asso`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

--
-- Contenu de la table `adhesion`
--

INSERT INTO `adhesion` (`id`, `date`, `id_adh`, `id_cre`, `statut`, `promo`, `id_asso`) VALUES
(16, '2011-08-10 09:29:09', 28, 8, 0, 2011, 1),
(17, '2011-08-10 09:29:09', 28, 13, 1, 2011, 2),
(18, '2011-08-10 09:29:56', 34, 9, 0, 2011, 2),
(19, '2011-08-10 09:29:56', 34, 10, 0, 2011, 2),
(20, '2011-08-10 09:29:56', 34, 8, 0, 2011, 2),
(21, '2011-08-10 09:29:56', 34, 12, 0, 2011, 2),
(22, '2011-08-10 09:33:35', 35, 13, 0, 2011, 2),
(23, '2011-08-10 09:33:35', 35, 14, 0, 2011, 2),
(24, '2011-08-10 09:34:17', 36, 9, 0, 2011, 1),
(25, '2011-08-10 09:34:17', 36, 10, 0, 2011, 1),
(26, '2011-08-10 09:34:17', 36, 8, 0, 2011, 1),
(27, '2011-08-10 09:34:17', 36, 12, 0, 2011, 1),
(28, '2011-08-10 09:35:52', 37, 8, 0, 2011, 2),
(29, '2011-08-10 09:35:52', 37, 12, 0, 2011, 2),
(30, '2011-08-10 09:37:59', 38, 8, 1, 2011, 1),
(31, '2011-08-16 14:35:03', 28, 14, 0, 2011, 2),
(32, '2011-08-26 09:45:07', 28, 9, 0, 2011, 1);

-- --------------------------------------------------------

--
-- Structure de la table `association`
--

CREATE TABLE IF NOT EXISTS `association` (
  `id` int(16) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `association`
--

INSERT INTO `association` (`id`, `nom`, `description`, `url`) VALUES
(1, 'Asesco', 'Association des étudiants u-psud', 'http://www.asesco.u-psud.fr/'),
(2, 'PSUC', 'Association du personnel', 'PSUC');

-- --------------------------------------------------------

--
-- Structure de la table `asso_section`
--

CREATE TABLE IF NOT EXISTS `asso_section` (
  `id_asso` int(16) NOT NULL,
  `id_sec` int(16) NOT NULL,
  KEY `id_asso` (`id_asso`,`id_sec`),
  KEY `id_sec` (`id_sec`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `asso_section`
--

INSERT INTO `asso_section` (`id_asso`, `id_sec`) VALUES
(1, 3),
(2, 3),
(2, 4);

-- --------------------------------------------------------

--
-- Structure de la table `champs_adherent`
--

CREATE TABLE IF NOT EXISTS `champs_adherent` (
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

--
-- Contenu de la table `champs_adherent`
--

INSERT INTO `champs_adherent` (`nom`, `type`, `description`, `inscription`, `user_editable`, `user_viewable`, `search_simple`, `search_trombi`, `format`, `ordre`, `required`) VALUES
('adresse1', 'varchar', 'Adresse', 1, 1, 1, 0, 0, 'def', 9, 1),
('adresse2', 'varchar', 'Complément Adresse', 1, 1, 1, 0, 0, 'def', 10, 0),
('adresse_pro', 'varchar', 'Adresse Professionnelle', 1, 1, 1, 0, 0, 'def', 25, 0),
('assurance', 'tinyint', 'J''ai pris connaissance des conditions d''<a href="http://www.asesco.u-psud.fr/wiki/index.php?title=ASESCO:Charte" target="_blank" >assurance</a>', 1, 0, 1, 0, 0, 'def', 49, 1),
('categorie', 'varchar', 'Catégorie', 1, 1, 1, 0, 0, 'categorie', 1, 1),
('certmed', 'file', 'Certificat médical', 1, 1, 1, 0, 0, 'def', 60, 0),
('charte', 'tinyint', 'J''accepte la <a href="http://www.asesco.u-psud.fr/wiki/index.php?title=ASESCO:Charte" target=_blank>charte</a>', 1, 0, 1, 0, 0, 'def', 50, 1),
('code_postal', 'varchar', 'Code postal', 1, 1, 1, 0, 0, 'number', 11, 1),
('contact_urgence', 'varchar', 'Contact d''urgence (nom)', 1, 1, 1, 0, 0, 'def', 12, 1),
('contact_urgence_tel', 'varchar', 'Contact d''urgence (tel)', 1, 1, 1, 0, 0, 'number', 20, 1),
('droit_image', 'tinyint', 'Je cède mon droit à l''image', 1, 1, 1, 0, 0, 'def', 46, 0),
('email', 'varchar', 'Adresse email', 1, 0, 1, 1, 0, 'email', 8, 1),
('id', 'int', '', 0, 0, 0, 0, 0, 'def', 0, 0),
('last_modif', 'datetime', 'Dernière modification', 0, 0, 1, 0, 0, 'def', 0, 0),
('naissance', 'date', 'Date de naissance', 1, 1, 1, 0, 0, 'date', 4, 1),
('nom', 'varchar', 'Nom', 1, 1, 1, 1, 1, 'def', 2, 1),
('numayantdroit', 'varchar', 'N° d''ayant droit (n° étudiant/n° agent)', 1, 1, 1, 0, 0, 'def', 5, 0),
('numcarte', 'varchar', 'Numéro de carte Asesco', 0, 0, 1, 1, 1, '', 0, 0),
('photo', 'file', 'Photo', 1, 1, 1, 1, 1, 'def', 55, 0),
('prenom', 'varchar', 'Prénom', 1, 1, 1, 1, 1, 'def', 3, 1),
('pre_inscription', 'datetime', '', 0, 0, 0, 0, 0, 'def', 0, 0),
('privilege', 'int', '', 0, 0, 0, 0, 0, 'def', 0, 0),
('statut', 'select', 'Votre statut', 1, 0, 1, 0, 0, 'def', 3, 1),
('tel1', 'varchar', 'Télephone portable', 1, 1, 1, 1, 0, 'number', 6, 1),
('tel2', 'varchar', 'Téléphone fixe', 1, 1, 1, 0, 0, 'number', 7, 0);

-- --------------------------------------------------------

--
-- Structure de la table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `id` varchar(255) NOT NULL,
  `valeur` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `config`
--

INSERT INTO `config` (`id`, `valeur`) VALUES
('admin_email', 'webmaster-asesco.asso@u-psud.fr'),
('contact_email', 'bureau-asesco.asso@u-psud.fr'),
('currency', '€'),
('promo', '2011'),
('url_resiliation', 'http://www.asesco.u-psud.fr/wiki/index.php?title=ASESCO:Contacts'),
('url_site', 'http://fliker.dyndns.org/');

-- --------------------------------------------------------

--
-- Structure de la table `creneau`
--

CREATE TABLE IF NOT EXISTS `creneau` (
  `id` int(16) NOT NULL,
  `jour` varchar(255) NOT NULL,
  `debut` time NOT NULL,
  `fin` time NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `id_act` int(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_act` (`id_act`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `creneau`
--

INSERT INTO `creneau` (`id`, `jour`, `debut`, `fin`, `lieu`, `id_act`) VALUES
(8, 'Lundi', '15:00:00', '16:00:00', 'Pagode de la nuit enchanté du matin', 6),
(9, 'Jeudi', '17:00:00', '20:00:00', 'Pagode du soleil levant', 5),
(10, 'Vendredi', '19:00:00', '20:00:00', 'Pagode du soleil levant', 5),
(12, 'Mercredi', '19:00:00', '20:00:00', 'Temple Du Destin', 6),
(13, 'Vendredi', '19:00:00', '20:00:00', 'L''Yvette', 7),
(14, 'Jeudi', '19:00:00', '20:00:00', 'L''Yvette', 7);

-- --------------------------------------------------------

--
-- Structure de la table `entite`
--

CREATE TABLE IF NOT EXISTS `entite` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Contenu de la table `entite`
--

INSERT INTO `entite` (`id`) VALUES
(1),
(2),
(3),
(4),
(5),
(6),
(7),
(8),
(9),
(10),
(11),
(12),
(13),
(14),
(15),
(16),
(20),
(21),
(23),
(24);

-- --------------------------------------------------------

--
-- Structure de la table `paiement`
--

CREATE TABLE IF NOT EXISTS `paiement` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Contenu de la table `paiement`
--

INSERT INTO `paiement` (`id`, `date`, `date_t`, `id_adh`, `type`, `num`, `remarque`, `promo`, `recorded_by`) VALUES
(1, '2011-01-03 19:53:02', '0000-00-00', 36, 'Cheque', '54643065503', 'Cheque en bois', 2011, 'Jean Robert'),
(9, '2011-08-09 22:43:08', '0000-00-00', 36, 'Cheque', '9998', 'Cheque du 20 Aout', 2011, 'Daniel Auteuil'),
(10, '2011-08-17 10:39:53', '0000-00-00', 36, 'Cheque', '98797889', 'Blahbalhba', 2011, 'Mac Gyver'),
(12, '2011-08-23 17:11:11', '0000-00-00', 36, 'Cheque', '9879878', 'wesh', 2011, 'Johnny Cash'),
(13, '2011-08-23 17:11:56', '0000-00-00', 35, 'Cheque', '9798', 'IOhidsh', 2011, 'Clark Kent'),
(16, '2011-08-24 20:29:50', '2011-08-09', 38, 'Cheque', '0897', 'Blahbalhba', 2011, 'Konecki Pierre-Olivier');

-- --------------------------------------------------------

--
-- Structure de la table `paiement_sup`
--

CREATE TABLE IF NOT EXISTS `paiement_sup` (
  `id_paiement` int(16) NOT NULL,
  `id_sup` int(16) NOT NULL,
  `valeur` double NOT NULL,
  KEY `id_paiment` (`id_paiement`,`id_sup`),
  KEY `id_adhesion` (`id_sup`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `paiement_sup`
--

INSERT INTO `paiement_sup` (`id_paiement`, `id_sup`, `valeur`) VALUES
(1, 42, 50),
(1, 46, 2),
(9, 44, 100),
(10, 47, 10),
(10, 42, 0),
(10, 38, 5),
(10, 41, 7),
(10, 39, 5),
(10, 35, 7),
(12, 47, 14),
(12, 42, 50),
(12, 38, 5),
(12, 41, 6),
(12, 39, 0),
(12, 35, 0),
(13, 49, 12),
(13, 45, 100),
(16, 36, 1000),
(16, 42, 100),
(16, 41, 13),
(16, 35, 7);

-- --------------------------------------------------------

--
-- Structure de la table `presence`
--

CREATE TABLE IF NOT EXISTS `presence` (
  `id_adh` int(16) NOT NULL,
  `id_cre` int(16) NOT NULL,
  `week` int(2) NOT NULL,
  `promo` int(4) NOT NULL,
  KEY `id_adh` (`id_adh`),
  KEY `id_cre` (`id_cre`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `presence`
--

INSERT INTO `presence` (`id_adh`, `id_cre`, `week`, `promo`) VALUES
(28, 12, 23, 2011),
(34, 10, 33, 2011),
(36, 8, 33, 2011),
(34, 9, 32, 2011),
(36, 10, 32, 2011),
(34, 8, 32, 2011),
(36, 9, 33, 2011),
(36, 9, 10, 2011),
(34, 9, 17, 2011),
(34, 8, 16, 2011),
(36, 8, 11, 2011),
(34, 9, 13, 2011),
(34, 9, 8, 2011),
(34, 10, 10, 2011),
(28, 13, 10, 2011),
(34, 9, 38, 2011),
(36, 9, 44, 2011),
(34, 9, 48, 2011),
(34, 10, 47, 2011),
(36, 10, 42, 2011),
(34, 10, 40, 2011),
(36, 10, 3, 2011),
(34, 10, 5, 2011),
(36, 8, 45, 2011),
(34, 8, 45, 2011),
(37, 8, 45, 2011),
(28, 8, 45, 2011),
(38, 8, 45, 2011),
(36, 8, 1, 2011),
(28, 8, 9, 2011),
(37, 8, 19, 2011),
(28, 8, 22, 2011),
(36, 8, 23, 2011),
(34, 9, 45, 2011),
(36, 9, 47, 2011),
(36, 8, 52, 2011),
(37, 8, 52, 2011),
(37, 8, 1, 2011),
(34, 8, 50, 2011),
(38, 8, 52, 2011),
(28, 8, 5, 2011),
(36, 8, 6, 2011),
(34, 8, 4, 2011),
(37, 8, 49, 2011),
(28, 8, 49, 2011),
(34, 9, 43, 2011),
(36, 9, 50, 2011),
(36, 9, 2, 2011),
(36, 9, 5, 2011),
(34, 9, 5, 2011),
(36, 9, 41, 2011),
(36, 10, 49, 2011),
(36, 10, 52, 2011),
(36, 10, 7, 2011),
(34, 9, 1, 2011),
(36, 9, 52, 2011),
(34, 10, 46, 2011),
(36, 10, 45, 2011),
(34, 10, 2, 2011),
(36, 10, 12, 2011),
(34, 10, 26, 2011),
(36, 10, 23, 2011),
(34, 10, 21, 2011),
(34, 10, 20, 2011),
(36, 10, 20, 2011),
(34, 10, 19, 2011),
(36, 10, 19, 2011),
(34, 10, 18, 2011),
(34, 10, 16, 2011),
(34, 10, 14, 2011),
(34, 10, 50, 2011),
(34, 12, 51, 2011),
(36, 12, 47, 2011),
(28, 9, 3, 2011),
(28, 9, 6, 2011),
(28, 9, 7, 2011),
(28, 9, 8, 2011),
(28, 9, 9, 2011),
(28, 9, 10, 2011),
(28, 9, 11, 2011),
(28, 9, 12, 2011),
(28, 9, 13, 2011);

-- --------------------------------------------------------

--
-- Structure de la table `resp_act`
--

CREATE TABLE IF NOT EXISTS `resp_act` (
  `id_act` int(16) NOT NULL,
  `id_adh` int(16) NOT NULL,
  PRIMARY KEY (`id_act`,`id_adh`),
  KEY `id_adh` (`id_adh`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `resp_act`
--

INSERT INTO `resp_act` (`id_act`, `id_adh`) VALUES
(6, 37),
(6, 38);

-- --------------------------------------------------------

--
-- Structure de la table `resp_asso`
--

CREATE TABLE IF NOT EXISTS `resp_asso` (
  `id_asso` int(16) NOT NULL,
  `id_adh` int(16) NOT NULL,
  PRIMARY KEY (`id_asso`,`id_adh`),
  KEY `id_adh` (`id_adh`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `resp_asso`
--

INSERT INTO `resp_asso` (`id_asso`, `id_adh`) VALUES
(1, 38),
(2, 38);

-- --------------------------------------------------------

--
-- Structure de la table `resp_cren`
--

CREATE TABLE IF NOT EXISTS `resp_cren` (
  `id_cre` int(16) NOT NULL,
  `id_adh` int(16) NOT NULL,
  PRIMARY KEY (`id_cre`,`id_adh`),
  KEY `id_adh` (`id_adh`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `resp_cren`
--

INSERT INTO `resp_cren` (`id_cre`, `id_adh`) VALUES
(13, 35),
(8, 38);

-- --------------------------------------------------------

--
-- Structure de la table `resp_section`
--

CREATE TABLE IF NOT EXISTS `resp_section` (
  `id_sec` int(16) NOT NULL,
  `id_adh` int(16) NOT NULL,
  PRIMARY KEY (`id_sec`,`id_adh`),
  KEY `id_adh` (`id_adh`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `resp_section`
--

INSERT INTO `resp_section` (`id_sec`, `id_adh`) VALUES
(3, 34);

-- --------------------------------------------------------

--
-- Structure de la table `section`
--

CREATE TABLE IF NOT EXISTS `section` (
  `id` int(16) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `url` varchar(1024) NOT NULL,
  `description` longtext NOT NULL,
  `logo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `section`
--

INSERT INTO `section` (`id`, `nom`, `url`, `description`, `logo`) VALUES
(3, 'Arts Martiaux', 'http://am.com', 'Blablablalalba', ''),
(4, 'Sports Collectifs', 'http://somurl.com', 'Foot, Basket toussa', '');

-- --------------------------------------------------------

--
-- Structure de la table `statut`
--

CREATE TABLE IF NOT EXISTS `statut` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Contenu de la table `statut`
--

INSERT INTO `statut` (`id`, `nom`) VALUES
(1, 'étudiant IUT Cachan'),
(2, 'étudiant IUT Orsay'),
(3, 'étudiant IUT Sceaux'),
(4, 'étudiant Jean Monnet'),
(5, 'étudiant Médecine'),
(6, 'étudiant Pharmacie'),
(7, 'étudiant Polytech PSud'),
(8, 'étudiant Polytechnique'),
(9, 'étudiant Sciences'),
(10, 'étudiant STAPS'),
(11, 'étudiant SupElec'),
(12, 'étudiant SupOptique'),
(13, 'étudiant autre'),
(14, 'personnel CEA'),
(15, 'personnel CNRS'),
(16, 'personnel INRA'),
(17, 'personnel INRIA'),
(18, 'personnel INSERM'),
(19, 'personnel Polytechnique'),
(20, 'personnel Soleil'),
(21, 'personnel SupElec'),
(22, 'personnel SupOptique'),
(23, 'personnel Univ PSud'),
(24, 'extérieur');

-- --------------------------------------------------------

--
-- Structure de la table `sup`
--

CREATE TABLE IF NOT EXISTS `sup` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=66 ;

--
-- Contenu de la table `sup`
--

INSERT INTO `sup` (`id`, `promo`, `type`, `valeur`, `id_statut`, `id_asso_adh`, `id_asso_paie`) VALUES
(35, 2011, 'Sup Cre', 7, NULL, 1, 1),
(36, 2011, 'Cotisation', 1000, 13, NULL, 1),
(38, 2011, 'TEst', 10, NULL, 1, 1),
(39, 2011, 'Test', 1, NULL, 1, 1),
(40, 2011, 'Cotisation', 10, 13, NULL, 2),
(41, 2011, 'Sup Act', 13, NULL, 1, 1),
(42, 2011, 'Sup Sec', 100, NULL, 1, 1),
(43, 2011, 'Cotisation', 23, 1, NULL, 1),
(44, 2011, 'Sup Sec 2', 102, NULL, 2, 2),
(45, 2011, 'Sup Psuc', 100, NULL, 2, 2),
(46, 2011, 'Test', 2, NULL, 2, 2),
(47, 2011, 'Cotisation', 24, 6, NULL, 1),
(48, 2011, 'Cotisation', 2, 5, NULL, 2),
(49, 2011, 'Cotisation', 12, 3, NULL, 2),
(50, 2011, 'Cotisation', 12, 3, NULL, 1),
(51, 2011, 'Cotisation', 63, 11, NULL, 2),
(52, 2010, 'Cotisation', 100, 7, NULL, 1),
(53, 2010, 'Cotisation', 93, 12, NULL, 1),
(56, 2011, 'Cotisation', 100, 7, NULL, 1),
(60, 2010, 'dsfdsfkl', 987987, NULL, 1, 1),
(61, 2010, '97838', 9802, NULL, 1, 2),
(64, 2010, '798798', 97979, NULL, 1, 2),
(65, 2010, '97897', 9798798, NULL, 1, 2);

-- --------------------------------------------------------

--
-- Structure de la table `sup_fk`
--

CREATE TABLE IF NOT EXISTS `sup_fk` (
  `id_ent` int(16) NOT NULL,
  `id_sup` int(16) NOT NULL,
  PRIMARY KEY (`id_ent`,`id_sup`),
  KEY `id` (`id_ent`),
  KEY `id_sup` (`id_sup`),
  KEY `id_2` (`id_ent`,`id_sup`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `sup_fk`
--

INSERT INTO `sup_fk` (`id_ent`, `id_sup`) VALUES
(1, 36),
(1, 43),
(1, 47),
(1, 50),
(1, 52),
(1, 53),
(1, 56),
(2, 40),
(2, 48),
(2, 49),
(2, 51),
(3, 42),
(3, 44),
(3, 60),
(3, 61),
(4, 45),
(5, 38),
(5, 64),
(6, 41),
(8, 35),
(9, 39),
(9, 46),
(9, 65);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `activite`
--
ALTER TABLE `activite`
  ADD CONSTRAINT `activite_ibfk_2` FOREIGN KEY (`id`) REFERENCES `entite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activite_ibfk_3` FOREIGN KEY (`id_sec`) REFERENCES `section` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `adherent`
--
ALTER TABLE `adherent`
  ADD CONSTRAINT `adherent_ibfk_1` FOREIGN KEY (`id_statut`) REFERENCES `statut` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `adhesion`
--
ALTER TABLE `adhesion`
  ADD CONSTRAINT `adhesion_ibfk_3` FOREIGN KEY (`id_adh`) REFERENCES `adherent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adhesion_ibfk_4` FOREIGN KEY (`id_cre`) REFERENCES `creneau` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adhesion_ibfk_5` FOREIGN KEY (`id_asso`) REFERENCES `association` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `association`
--
ALTER TABLE `association`
  ADD CONSTRAINT `association_ibfk_1` FOREIGN KEY (`id`) REFERENCES `entite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `asso_section`
--
ALTER TABLE `asso_section`
  ADD CONSTRAINT `asso_section_ibfk_1` FOREIGN KEY (`id_asso`) REFERENCES `association` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `asso_section_ibfk_2` FOREIGN KEY (`id_sec`) REFERENCES `section` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `creneau`
--
ALTER TABLE `creneau`
  ADD CONSTRAINT `creneau_ibfk_2` FOREIGN KEY (`id`) REFERENCES `entite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `creneau_ibfk_3` FOREIGN KEY (`id_act`) REFERENCES `activite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `paiement`
--
ALTER TABLE `paiement`
  ADD CONSTRAINT `paiement_ibfk_1` FOREIGN KEY (`id_adh`) REFERENCES `adherent` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `paiement_sup`
--
ALTER TABLE `paiement_sup`
  ADD CONSTRAINT `paiement_sup_ibfk_1` FOREIGN KEY (`id_paiement`) REFERENCES `paiement` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `paiement_sup_ibfk_2` FOREIGN KEY (`id_sup`) REFERENCES `sup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `presence`
--
ALTER TABLE `presence`
  ADD CONSTRAINT `presence_ibfk_1` FOREIGN KEY (`id_adh`) REFERENCES `adherent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `presence_ibfk_2` FOREIGN KEY (`id_cre`) REFERENCES `creneau` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `resp_act`
--
ALTER TABLE `resp_act`
  ADD CONSTRAINT `resp_act_ibfk_1` FOREIGN KEY (`id_act`) REFERENCES `activite` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `resp_act_ibfk_2` FOREIGN KEY (`id_adh`) REFERENCES `adherent` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `resp_asso`
--
ALTER TABLE `resp_asso`
  ADD CONSTRAINT `resp_asso_ibfk_1` FOREIGN KEY (`id_asso`) REFERENCES `association` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `resp_asso_ibfk_2` FOREIGN KEY (`id_adh`) REFERENCES `adherent` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `resp_cren`
--
ALTER TABLE `resp_cren`
  ADD CONSTRAINT `resp_cren_ibfk_1` FOREIGN KEY (`id_cre`) REFERENCES `creneau` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `resp_cren_ibfk_2` FOREIGN KEY (`id_adh`) REFERENCES `adherent` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `resp_section`
--
ALTER TABLE `resp_section`
  ADD CONSTRAINT `resp_section_ibfk_1` FOREIGN KEY (`id_sec`) REFERENCES `section` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `resp_section_ibfk_2` FOREIGN KEY (`id_adh`) REFERENCES `adherent` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `section`
--
ALTER TABLE `section`
  ADD CONSTRAINT `section_ibfk_1` FOREIGN KEY (`id`) REFERENCES `entite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sup`
--
ALTER TABLE `sup`
  ADD CONSTRAINT `sup_ibfk_1` FOREIGN KEY (`id_statut`) REFERENCES `statut` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sup_ibfk_2` FOREIGN KEY (`id_asso_adh`) REFERENCES `association` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sup_ibfk_3` FOREIGN KEY (`id_asso_paie`) REFERENCES `association` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sup_fk`
--
ALTER TABLE `sup_fk`
  ADD CONSTRAINT `sup_fk_ibfk_1` FOREIGN KEY (`id_sup`) REFERENCES `sup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sup_fk_ibfk_2` FOREIGN KEY (`id_ent`) REFERENCES `entite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
