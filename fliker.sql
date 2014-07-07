-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Ven 28 Septembre 2012 à 13:18
-- Version du serveur: 5.1.63
-- Version de PHP: 5.3.16

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `fliker`
--

-- CREATE DATABASE `fliker` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci;
-- USE `fliker`;

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
  `privilege` tinyint(1) NOT NULL DEFAULT '0',
  `date_creation` datetime NOT NULL,
  `last_modif` datetime NOT NULL,
  `last_modif_droit_image` datetime NOT NULL,
  `categorie` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `id_statut` int(16) NOT NULL,
  `numayantdroit` varchar(255) NOT NULL,
  `naissance` date NOT NULL,
  `tel1` varchar(255) NOT NULL,
  `tel2` varchar(255) NOT NULL,
  `adresse1` varchar(255) NOT NULL,
  `adresse2` varchar(255) NOT NULL,
  `code_postal` varchar(255) NOT NULL,
  `adresse_pro` varchar(255) NOT NULL,
  `contact_urgence` varchar(255) NOT NULL,
  `contact_urgence_tel` varchar(255) NOT NULL,
  `photo` tinyint(4) NOT NULL,
  `certmed` varchar(255) NOT NULL,
  `charte` tinyint(1) NOT NULL,
  `assurance` tinyint(4) NOT NULL,
  `droit_image` tinyint(4) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `activationkey` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `add_mail_temp` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_statut` (`id_statut`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
  `exemple` varchar(255) NOT NULL,
  `ordre` int(16) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `inscription` tinyint(1) NOT NULL,
  `user_viewable` tinyint(1) NOT NULL DEFAULT '0',
  `user_editable` tinyint(1) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `search_simple` tinyint(1) NOT NULL,
  `search_trombi` tinyint(1) NOT NULL,
  `format` varchar(255) NOT NULL,
  PRIMARY KEY (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `fliker_champs_adherent`
--

INSERT INTO `fliker_champs_adherent` (`nom`, `type`, `description`, `exemple`, `ordre`, `required`, `inscription`, `user_viewable`, `user_editable`, `admin`, `search_simple`, `search_trombi`, `format`) VALUES
('activationkey', 'varchar', 'Utilisé pour l''assignation de password, changements de mots de passe et changements d''emails', '', 0, 0, 0, 0, 0, 0, 0, 0, 'def'),
('active', 'tinyint', 'Etat du compte', '', 22, 0, 0, 0, 0, 1, 0, 0, 'active'),
('add_mail_temp', 'varchar', 'Utilisé pour stocker la nouvelle adresse lors d''un changement d''email', '', 0, 0, 0, 0, 0, 0, 0, 0, 'def'),
('adresse1', 'varchar', 'Adresse postale', '', 11, 1, 1, 1, 1, 1, 0, 0, 'def'),
('adresse2', 'varchar', 'Ville', '', 12, 1, 1, 1, 1, 1, 0, 0, 'def'),
('adresse_pro', 'varchar', 'Adresse professionnelle', '', 10, 0, 1, 1, 1, 1, 0, 0, 'def'),
('assurance', 'tinyint', 'J''ai pris connaissance des conditions d''<a href="http://www.asso.fr/wiki/index.php?title=Assurance" target="_blank" >assurance</a>', '', 17, 1, 1, 1, 0, 0, 0, 0, 'def'),
('categorie', 'varchar', 'Catégorie', '', 1, 1, 1, 1, 1, 1, 0, 0, 'categorie'),
('certmed', 'file', 'Certificat médical', '', 20, 1, 0, 1, 1, 1, 0, 0, 'def'),
('charte', 'tinyint', 'J''accepte les <a href="http://www.asso.fr/wiki/index.php?title=Charte" target=_blank>statuts et règlements</a>', '', 18, 1, 1, 1, 0, 0, 0, 0, 'def'),
('code_postal', 'varchar', 'Code postal', '', 13, 1, 1, 1, 1, 1, 0, 0, 'number'),
('contact_urgence', 'varchar', 'Contact d''urgence (nom)', '', 14, 1, 1, 1, 1, 1, 0, 0, 'def'),
('contact_urgence_tel', 'varchar', 'Contact d''urgence (tel)', '', 15, 1, 1, 1, 1, 1, 0, 0, 'number'),
('date_creation', 'datetime', 'Création de la fiche', '', 0, 0, 0, 0, 0, 0, 0, 0, 'def'),
('droit_image', 'tinyint', 'Je cède mon droit à l''image', '', 16, 0, 1, 1, 1, 0, 0, 0, 'def'),
('email', 'varchar', 'Adresse email <u>valide</u> (confirmation requise !)', '', 9, 1, 1, 1, 0, 1, 1, 0, 'email'),
('id', 'int', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 'def'),
('last_modif', 'datetime', 'Dernière modification de la fiche', '', 0, 0, 0, 0, 0, 0, 0, 0, 'def'),
('last_modif_droit_image', 'datetime', 'Dernière modification de l''autorisation des droits d''image ou pas.', '', 0, 0, 0, 0, 0, 0, 0, 0, 'def'),
('naissance', 'date', 'Date de naissance', '', 4, 1, 1, 1, 1, 1, 0, 0, 'date'),
('nom', 'varchar', 'Nom', '', 2, 1, 1, 1, 1, 1, 1, 1, 'def'),
('numayantdroit', 'varchar', 'N° d''ayant droit', '', 6, 0, 1, 1, 1, 1, 0, 0, 'def'),
('password', 'varchar', 'Mot de passe utilisateur', '', 0, 0, 0, 0, 0, 0, 0, 0, 'def'),
('photo', 'file', 'Photo', '', 19, 1, 0, 1, 1, 1, 1, 1, 'def'),
('prenom', 'varchar', 'Prénom', '', 3, 1, 1, 1, 1, 1, 1, 1, 'def'),
('privilege', 'tinyint', 'Privilège administrateur', '', 21, 0, 0, 0, 0, 0, 0, 0, 'def'),
('statut', 'select', 'Statut', '', 5, 1, 1, 1, 0, 1, 0, 0, 'def'),
('tel1', 'varchar', 'Téléphone portable', '', 7, 1, 1, 1, 1, 1, 1, 0, 'number'),
('tel2', 'varchar', 'Téléphone fixe', '', 8, 0, 1, 1, 1, 1, 0, 0, 'number');

-- --------------------------------------------------------

--
-- Structure de la table `fliker_config`
--

CREATE TABLE IF NOT EXISTS `fliker_config` (
  `id` varchar(255) NOT NULL COMMENT 'conf=configuration globale,notif=notification à envoyer,txt=message texte intégré au site',
  `valeur` varchar(1024) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `fliker_config`
--

INSERT INTO `fliker_config` (`id`, `valeur`, `description`) VALUES
('account_out.notif', 'now', 'Notification lorsque qu''un compte est désactivé'),
('action_continuer.txt', 'OK (Vous pouvez continuer à naviguer)', 'Message indiquant à l''utilisateur que l''envoi d''emails est terminé'),
('action_patienter.txt', 'Envoi des emails (merci de patienter) : ', 'Message indiquant à l''utilisateur de patienter pendant l''envoi d''emails'),
('admin_email.conf', 'webmaster@asso.fr', 'Adresse email à partir de laquelle seront envoyés les emails (email)'),
('allow_mail.conf', 'true', 'Autoriser l''envoi des emails'),
('change_email.notif', 'now', 'Notification lorsque qu''un changement d''email est effectué'),
('contact_email.conf', 'contact@asso.fr', 'Adresse email utilisée pour la réponse aux emails lorsque c''est nécessaire (email)'),
('currency.conf', '€', 'Monnaie actuelle de Fliker (symbole monétaire)'),
('date_debut_promo.conf', '09/01', 'Date indiquant le début de la promo actuelle (date : MM/DD)'),
('date_fin_promo.conf', '06/30', 'Date indiquant la fin de la promo actuelle (date : MM/DD)'),
('dest_redirect.conf', './index.php', 'URL relatif utilisé pour rediriger l''utilisateur après une connexion réussie (lien relatif)'),
('is_wiki.conf', 'false', 'Définit si une base de données wiki est activé (true ou false)'),
('modif_rights.notif', 'now', 'Notification lors d''un ajout ou retrait de droits'),
('msgError_email.txt', 'L''<b>email</b> que vous avez entré n''existe pas !', 'Message d''erreur utilisé lors de la connexion en cas d''une erreur dans l''adresse email'),
('msgError_mdp.txt', 'Le <b>mot de passe</b> que vous avez entré est erroné ! Ou vous avez oublié d''activer votre compte ? (consultez votre boîte email !)', 'Message d''erreur utilisé lors de la connexion en cas d''une erreur du mot de passe'),
('new_adhesion.notif', 'now', 'Notification lorsqu''une adhésion devient possible'),
('new_adhesionAdh.notif', 'now', 'Notification lors d''une nouvelle adhésion (invitation à prendre contact)'),
('new_impossible.notif', 'now', 'Notification lorsque nouvelle adhésion impossible'),
('no_adhesion.notif', 'monthly', 'Notification lorsqu''un adhérent n''a pas d''adhésion cette année'),
('no_adhesion_resp.notif', 'weekly', 'Notification lorsqu''un responsable n''est pas adhérent de sa section'),
('paiment_noCorresp.notif', 'now', 'Alerte lors d''un paiement qui ne correspond plus à une facture'),
('promo.conf', '2013', 'Année de la promo actuelle (année)'),
('section_off.notif', 'now', 'Alerte lorsque section désactivée'),
('solde_neg.notif', 'daily', 'Notification lorsqu''un solde n''est pas à jour'),
('stat_db.notif', 'weekly', 'Notification sur les stats de taille de la base sql'),
('stop_adhesions.conf', 'false', 'Booléen permettant d''empêcher les nouvelles adhésions (true ou false)'),
('text_activite.txt', 'Fiche d''Activité', 'Texte affiché en haut d''une fiche d''activité '),
('text_adherent.txt', '<h2>Choisissez vos sports en cliquant sur "Adhésions" puis sur "Nouvelle" !</h2><h3>Pensez à uploader votre photo + certificat éventuel (cliquer sur "Modifier") !</h3>', 'Texte affiché sur la fiche adhérent'),
('text_asso.txt', 'Fiche d''Asso', 'Texte affiché en haut d''une fiche d''association'),
('text_creneau.txt', 'Fiche de Créneau', 'Texte affiché en haut d''une fiche d''un créneau'),
('text_presence.txt', 'Feuilles d''Appel', 'Texte affiché en haut des feuilles d''appels '),
('text_search.txt', 'Formulaire de Recherche', 'Texte affiché en haut du système de recherche'),
('text_section.txt', 'Fiche de Section', 'Texte affiché en haut d''une fiche de section'),
('text_select_asso.txt', '<H2>Veuillez SVP sélectionner votre association pour chacun des créneaux choisis :</H2><H4><i>si vous lisez "impossible", cela signifie que l''activité n''est pas encore ouverte à votre statut<br>(par exemple, si vous avez choisi une activité réservée aux "étudiants" alors que vous faîtes partie des "personnels") ;<br>mais n''hésitez pas à "Valider" quand même, car plus il y aura de demandes, plus les chances augmentent que le créneau soit prochainement ouvert à votre statut ...</i></H4>', 'Texte affiché lors de la sélection de ses adhésions permettant de choisir les paramètres d''un créneau'),
('text_top.txt', 'FLIKER', 'Titre général en haut à gauche, logo (string ou lien)'),
('timezone.conf', 'Europe/Paris', 'Timezone utilisé pour les dates du site'),
('url_resiliation.conf', 'http://www.asso.fr/wiki/index.php?title=Contacts', 'URL utilisé pour rediriger les adhérents souhaitant se désinscrire à un sport (lien absolu)'),
('url_site.conf', 'http://www.asso.fr/fliker/', 'URL du site en production, la racine du site (lien absolu)'),
('validate_account.txt', 'Votre compte est presque activé. Votre "identifiant" sera votre adresse email.</p><p>Veuillez SVP définir le mot de passe qui sera associé à votre identifiant :', 'Message utilisé pour la validation d''un compte, demandant le mot de passe'),
('validate_redirect.txt', 'Votre compte est à présent activé ! Vous pouvez dès à présent vous connecter en cliquant sur "Connexion".', 'Message de redirection affiché après une activation d''un compte '),
('without_pic.notif', 'weekly', 'Notification lorsque pas de photo ou pas de certif');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_famille`
--

CREATE TABLE IF NOT EXISTS `fliker_famille` (
  `id` int(16) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `url` varchar(1024) NOT NULL,
  `description` longtext NOT NULL,
  `logo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------


--
-- Structure de la table `fliker_finances`
--

CREATE TABLE IF NOT EXISTS `fliker_finances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `type_transaction` varchar(255) NOT NULL,
  `num_transaction` varchar(255) NOT NULL,
  `section` varchar(255) NOT NULL,
  `fournisseur` varchar(255) NOT NULL,
  `montant` decimal(38,2) NOT NULL,
  `date_transaction` datetime NOT NULL,
  `signataire` int(11) NOT NULL,
  `enregistreur` int(11) NOT NULL,
  `date_bancaire` datetime NOT NULL,
  `autorisation` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=en attente,1=autorisé,2=refusé',
  `confirmation` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=en attente,1=autorisé,2=refusé',
  `description` varchar(255) NOT NULL,
  `id_obj_inventaire` int(11) NOT NULL DEFAULT '0',
  `date_enregistrement` datetime NOT NULL,
  `confirmed_by` int(11) NOT NULL,
  `confirmed_date` datetime NOT NULL,
  `authorized_by` int(11) NOT NULL,
  `authorized_date` datetime NOT NULL,
  `promo` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_inventaire`
--

CREATE TABLE IF NOT EXISTS `fliker_inventaire` (
  `id_entite` int(11) NOT NULL,
  `id_objet` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `dates_verification` datetime NOT NULL,
  `date_enregistrement` datetime NOT NULL,
  `amortissement` int(11) NOT NULL,
  `promo` int(11) NOT NULL,
  `reservable` tinyint(1) NOT NULL,
  UNIQUE KEY `id_objet` (`id_objet`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_inv_hist`
--

CREATE TABLE IF NOT EXISTS `fliker_inv_hist` (
  `id_obj` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `date_modif` datetime NOT NULL,
  `commentaire` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `fliker_numcarte_fk`
--

CREATE TABLE IF NOT EXISTS `fliker_numcarte_fk` (
  `id_adh` int(16) NOT NULL DEFAULT '0',
  `numcarte` int(11) NOT NULL,
  `promo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `confirmed_by` varchar(255) NOT NULL,
  `confirmed_date` datetime NOT NULL,
  `date_bordereau` datetime DEFAULT NULL COMMENT 'date=confirmé,0=annulé,null=en attente d''action',
  PRIMARY KEY (`id`),
  KEY `id_adh_2` (`id_adh`),
  KEY `recorded_by` (`recorded_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
  `promo` int(4) NOT NULL,
  KEY `id_adh` (`id_adh`),
  KEY `id_cre` (`id_cre`)
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
  `id_famille` int(16) NOT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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

-- --------------------------------------------------------

--
-- Structure de la table `fliker_type_dep`
--

CREATE TABLE IF NOT EXISTS `fliker_type_dep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Contenu de la table `fliker_type_dep`
--

INSERT INTO `fliker_type_dep` (`id`, `nom`) VALUES
(1, 'Fourniture consommable'),
(2, 'Matériel inventorié'),
(3, 'Gratification'),
(4, 'Subvention'),
(5, 'Remboursement adhésion'),
(6, 'Formation'),
(7, 'Déplacement'),
(8, 'Evénement'),
(9, 'Don'),
(10, 'Maintenance');

-- --------------------------------------------------------

--
-- Structure de la table `fliker_type_supl`
--

CREATE TABLE IF NOT EXISTS `fliker_type_supl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `fliker_type_supl`
--

INSERT INTO `fliker_type_supl` (`id`, `nom`) VALUES
(1, 'Cotisation'),
(2, 'Supplément section'),
(3, 'Supplément cours'),
(4, 'Location'),
(5, 'Caution'),
(6, 'Licence internationale'),
(7, 'Passeport fédéral'),
(8, 'Licence fédérale');

-- --------------------------------------------------------

--
-- Structure de la table `fliker_type_transa`
--

CREATE TABLE IF NOT EXISTS `fliker_type_transa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `fliker_type_transa`
--

INSERT INTO `fliker_type_transa` (`id`, `nom`) VALUES
(1, 'Dispense'),
(2, 'Espèces'),
(3, 'Chèque'),
(4, 'Paypal'),
(5, 'Virement'),
(6, 'CB');

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `fliker_activite`
--
ALTER TABLE `fliker_activite`
  ADD CONSTRAINT `activite_ibfk_1` FOREIGN KEY (`id`) REFERENCES `fliker_entite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activite_ibfk_2` FOREIGN KEY (`id_sec`) REFERENCES `fliker_section` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_adherent`
--
ALTER TABLE `fliker_adherent`
  ADD CONSTRAINT `adherent_ibfk_1` FOREIGN KEY (`id_statut`) REFERENCES `fliker_statut` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `fliker_adhesion`
--
ALTER TABLE `fliker_adhesion`
  ADD CONSTRAINT `adhesion_ibfk_1` FOREIGN KEY (`id_adh`) REFERENCES `fliker_adherent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adhesion_ibfk_2` FOREIGN KEY (`id_cre`) REFERENCES `fliker_creneau` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adhesion_ibfk_3` FOREIGN KEY (`id_asso`) REFERENCES `fliker_association` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `creneau_ibfk_1` FOREIGN KEY (`id`) REFERENCES `fliker_entite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `creneau_ibfk_2` FOREIGN KEY (`id_act`) REFERENCES `fliker_activite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Contraintes pour la table `fliker_presence`
--
ALTER TABLE `fliker_presence`
  ADD CONSTRAINT `presence_ibfk_1` FOREIGN KEY (`id_adh`) REFERENCES `fliker_adherent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `presence_ibfk_2` FOREIGN KEY (`id_cre`) REFERENCES `fliker_creneau` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
