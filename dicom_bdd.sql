-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Jeu 04 Février 2016 à 11:13
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `dicom2`
--

-- --------------------------------------------------------

--
-- Structure de la table `anatomicorientation`
--

CREATE TABLE IF NOT EXISTS `anatomicorientation` (
  `anatomicOrientation_name` varchar(255) NOT NULL,
  PRIMARY KEY (`anatomicOrientation_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `anatomicorientation`
--

INSERT INTO `anatomicorientation` (`anatomicOrientation_name`) VALUES
('Allongé'),
('Debout');

-- --------------------------------------------------------

--
-- Structure de la table `bodypart`
--

CREATE TABLE IF NOT EXISTS `bodypart` (
  `bodyPart_anatomicRegionSequence` varchar(255) NOT NULL,
  `bodyPart_Examined` varchar(255) NOT NULL,
  PRIMARY KEY (`bodyPart_anatomicRegionSequence`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `bodypart`
--

INSERT INTO `bodypart` (`bodyPart_anatomicRegionSequence`, `bodyPart_Examined`) VALUES
('Bras', 'Bras'),
('Mollet', 'Mollet'),
('Ventre', 'Ventre');

-- --------------------------------------------------------

--
-- Structure de la table `console`
--

CREATE TABLE IF NOT EXISTS `console` (
  `console_SourceApplicationEntityTitle` varchar(255) NOT NULL,
  `console_StationName` varchar(255) NOT NULL,
  `console_DeviceSerialNumber` varchar(255) NOT NULL,
  `console_PerformedStationAETitle` varchar(255) NOT NULL,
  `console_PerformedStationName` varchar(255) NOT NULL,
  `console_col` varchar(45) DEFAULT NULL,
  `dicom_IP` varchar(255) NOT NULL,
  PRIMARY KEY (`console_SourceApplicationEntityTitle`),
  KEY `dicom_IP` (`dicom_IP`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `dicom`
--

CREATE TABLE IF NOT EXISTS `dicom` (
  `dicom_IP` varchar(255) NOT NULL,
  `dicom_port` varchar(255) NOT NULL,
  `dicom_transfertSyntaxeUID` varchar(255) NOT NULL,
  PRIMARY KEY (`dicom_IP`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Structure de la table `examen`
--

CREATE TABLE IF NOT EXISTS `examen` (
  `examen_accessionNumber` int(11) NOT NULL AUTO_INCREMENT,
  `examen_instanceCreationDateTime` datetime NOT NULL,
  `examen_procedureCodeSequence` varchar(255) NOT NULL,
  `examen_institutionalDepartementName` varchar(255) DEFAULT NULL,
  `examen_protocolName` varchar(255) NOT NULL,
  `examen_performedProcedureStepID` varchar(255) NOT NULL,
  `examen_performedProcedureStepDescription` varchar(255) DEFAULT NULL,
  `examen_contentDateTime` datetime NOT NULL,
  `examen_instanceCreatorUID` varchar(255) NOT NULL,
  `bodyPart_anatomicRegionSequence` varchar(255) NOT NULL,
  `anatomicOrientation_name` varchar(255) NOT NULL,
  `posture_name` varchar(255) NOT NULL,
  `operateur_name` varchar(255) NOT NULL,
  `realisateur_performingPhysicianName` varchar(255) NOT NULL,
  `prescripteur_referringPhysicianName` varchar(255) NOT NULL,
  `patient_insee` int(11) NOT NULL,
  `examen_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`examen_accessionNumber`),
  KEY `bodyPart_anatomicRegionSequence` (`bodyPart_anatomicRegionSequence`),
  KEY `anatomicOrientation_name` (`anatomicOrientation_name`),
  KEY `posture_name` (`posture_name`),
  KEY `operateur_name` (`operateur_name`),
  KEY `realisateur_performingPhysicianName` (`realisateur_performingPhysicianName`),
  KEY `prescripteur_referringPhysicianName` (`prescripteur_referringPhysicianName`),
  KEY `patient_insee` (`patient_insee`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Structure de la table `image`
--

CREATE TABLE IF NOT EXISTS `image` (
  `image_reference` int(11) NOT NULL,
  `image_samplesPerPixel` int(11) NOT NULL,
  `image_samplesPerPixelUsed` int(11) NOT NULL,
  `image_photometricInterpretation` varchar(255) NOT NULL,
  `image_rows` int(11) NOT NULL,
  `image_columns` int(11) NOT NULL,
  `image_bitsAllocated` int(11) NOT NULL,
  `image_bitsStored` int(11) NOT NULL,
  `image_pixelRepresentation` varchar(255) NOT NULL,
  `image_windowCenter` int(11) NOT NULL,
  `image_windowWidth` int(11) NOT NULL,
  `image_waveformDisplayBkgCIELabValue` varchar(255) NOT NULL,
  `image_channelRecommendDisplayCIELabValue` varchar(255) NOT NULL,
  `image_numericValue` int(11) NOT NULL,
  `image_imageFrameOrigin` int(11) NOT NULL,
  `image_annotationSequence` varchar(255) DEFAULT NULL,
  `image_unformattedTextValue` varchar(255) DEFAULT NULL,
  `image_graphicLayerDescription` varchar(255) DEFAULT NULL,
  `image_overlayRows` int(11) NOT NULL,
  `image_overlayColumns` int(11) NOT NULL,
  `image_overlayDescription` varchar(255) NOT NULL,
  `image_overlayType` varchar(1) NOT NULL,
  `image_overlayOrigin` varchar(3) NOT NULL,
  `image_overlayBitsAllocated` int(11) NOT NULL,
  `image_overlayBitPosition` int(11) NOT NULL,
  `image_overlayData` int(11) NOT NULL,
  `image_pixelData` int(11) NOT NULL,
  `image_referencedImageSequence` varchar(255) NOT NULL,
  `image_mediaStorageSOPInstanceUID` varchar(255) NOT NULL,
  `image_SOPInstanceUID` varchar(255) NOT NULL,
  `image_referencedSOPInstanceUID` varchar(255) NOT NULL,
  `image_link` varchar(255) NOT NULL,
  `serie_instanceNumber` int(11) NOT NULL,
  PRIMARY KEY (`image_reference`),
  KEY `serie_instanceNumber` (`serie_instanceNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Structure de la table `operateur`
--

CREATE TABLE IF NOT EXISTS `operateur` (
  `operateur_name` varchar(255) NOT NULL,
  PRIMARY KEY (`operateur_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `operateur`
--

INSERT INTO `operateur` (`operateur_name`) VALUES
('Dr. Frankenstein'),
('Dr. Strange'),
('Dr. Zoidberg');

-- --------------------------------------------------------

--
-- Structure de la table `parametres`
--

CREATE TABLE IF NOT EXISTS `parametres` (
  `id_param` int(11) NOT NULL,
  `delai_sup` int(11) NOT NULL DEFAULT '7' COMMENT 'Délai avant suppression des dossiers',
  PRIMARY KEY (`id_param`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `parametres`
--

INSERT INTO `parametres` (`id_param`, `delai_sup`) VALUES
(1, 12);

-- --------------------------------------------------------

--
-- Structure de la table `patient`
--

CREATE TABLE IF NOT EXISTS `patient` (
  `patient_insee` int(11) NOT NULL,
  `patient_firstName` varchar(255) NOT NULL,
  `patient_lastName` varchar(255) NOT NULL,
  `patient_dateOfBirth` date NOT NULL,
  `patient_sex` varchar(1) NOT NULL,
  `patient_size` int(11) NOT NULL,
  `patient_weight` int(11) NOT NULL,
  `patient_typeOfID` varchar(255) NOT NULL,
  `patient_adress` varchar(255) DEFAULT NULL,
  `patient_insurancePlanIdentification` varchar(255) DEFAULT NULL,
  `patient_countryOfResidence` varchar(255) NOT NULL,
  `patient_telephoneNumber` int(11) DEFAULT NULL,
  `patient_additionalHistory` text,
  PRIMARY KEY (`patient_insee`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Structure de la table `posture`
--

CREATE TABLE IF NOT EXISTS `posture` (
  `posture_name` varchar(255) NOT NULL,
  PRIMARY KEY (`posture_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `posture`
--

INSERT INTO `posture` (`posture_name`) VALUES
('Contraction'),
('Extension'),
('Repos');

-- --------------------------------------------------------

--
-- Structure de la table `prescripteur`
--

CREATE TABLE IF NOT EXISTS `prescripteur` (
  `prescripteur_referringPhysicianName` varchar(255) NOT NULL,
  PRIMARY KEY (`prescripteur_referringPhysicianName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `prescripteur`
--

INSERT INTO `prescripteur` (`prescripteur_referringPhysicianName`) VALUES
('Dr. Jekyll'),
('Dr. McCoy'),
('Dr. Watson');

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE IF NOT EXISTS `produit` (
  `produit_implementationVersionName` int(11) NOT NULL,
  `produit_privateInformation` varchar(255) DEFAULT NULL,
  `produit_specificCaractereSet` varchar(255) NOT NULL,
  `produit_imageType` varchar(255) NOT NULL,
  `produit_modality` text NOT NULL,
  `produit_manufacturer` varchar(255) NOT NULL,
  `produit_manufacturerModelName` varchar(255) NOT NULL,
  `produit_softwareVersion` text NOT NULL,
  `produit_lastCalibration` datetime NOT NULL,
  `produit_implementationClassUID` varchar(255) NOT NULL,
  PRIMARY KEY (`produit_implementationVersionName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `realisateur`
--

CREATE TABLE IF NOT EXISTS `realisateur` (
  `realisateur_performingPhysicianName` varchar(255) NOT NULL,
  PRIMARY KEY (`realisateur_performingPhysicianName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `realisateur`
--

INSERT INTO `realisateur` (`realisateur_performingPhysicianName`) VALUES
('Dr. Cooper'),
('Dr. House'),
('Dr. Lecter');

-- --------------------------------------------------------

--
-- Structure de la table `serie`
--

CREATE TABLE IF NOT EXISTS `serie` (
  `serie_seriesDescription` int(11) DEFAULT NULL,
  `serie_instanceNumber` int(11) NOT NULL AUTO_INCREMENT,
  `serie_imagesInAcquisition` int(11) NOT NULL,
  `serie_serieDatetime` datetime NOT NULL,
  `serie_acquisitionDatetime` datetime NOT NULL,
  `serie_SeriesInstanceUID` varchar(255) NOT NULL,
  `serie_frameOfReferenceUID` varchar(255) NOT NULL,
  `serie_mediaStorageSOPClassUID` varchar(255) NOT NULL,
  `serie_SOPClassUID` varchar(255) NOT NULL,
  `serie_referencedSOPClassUID` varchar(255) NOT NULL,
  `study_id` int(11) NOT NULL,
  PRIMARY KEY (`serie_instanceNumber`),
  KEY `study_id` (`study_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;


-- --------------------------------------------------------

--
-- Structure de la table `signal`
--

CREATE TABLE IF NOT EXISTS `signal` (
  `signal_id` int(11) NOT NULL AUTO_INCREMENT,
  `signal_waveformOriginality` varchar(255) NOT NULL,
  `signal_numberOfWaveformChannel` int(11) NOT NULL,
  `signal_numberOfWaveformSamples` int(11) NOT NULL,
  `signal_samplingFrequency` int(11) NOT NULL,
  `signal_channelDefinitionSequence` text NOT NULL,
  `signal_waveformChannelNumber` int(11) NOT NULL,
  `signal_channelLabel` varchar(255) NOT NULL,
  `signal_channelStatut` text NOT NULL,
  `signal_channelSourcesSequence` varchar(255) NOT NULL,
  `signal_channelSensitivityCorrection` int(11) DEFAULT NULL,
  `signal_channelBaseline` int(11) DEFAULT NULL,
  `signal_channelOfset` datetime NOT NULL,
  `signal_waveformBitStored` int(11) NOT NULL,
  `signal_notchFilterBandwitdh` int(11) DEFAULT NULL,
  `signal_measurementUnit` text NOT NULL,
  `signal_waveformSequence` varchar(255) NOT NULL,
  `serie_instanceNumber` int(11) NOT NULL,
  PRIMARY KEY (`signal_id`),
  KEY `serie_instanceNumber` (`serie_instanceNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `site`
--

CREATE TABLE IF NOT EXISTS `site` (
  `InstitutionName` varchar(255) NOT NULL,
  `InstitutionAdress` varchar(255) NOT NULL,
  PRIMARY KEY (`InstitutionName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `study`
--

CREATE TABLE IF NOT EXISTS `study` (
  `study_id` int(11) NOT NULL AUTO_INCREMENT,
  `study_studyInstanceUID` varchar(255) NOT NULL,
  `study_aquisitionsInStudy` int(11) NOT NULL,
  `study_datetime` datetime NOT NULL,
  `study_referencedStudySequence` varchar(255) NOT NULL,
  `patient_insee` int(11) NOT NULL,
  PRIMARY KEY (`study_id`),
  KEY `patient_insee` (`patient_insee`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;



--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `console`
--
ALTER TABLE `console`
  ADD CONSTRAINT `console_ibfk_1` FOREIGN KEY (`dicom_IP`) REFERENCES `dicom` (`dicom_IP`);

--
-- Contraintes pour la table `examen`
--
ALTER TABLE `examen`
  ADD CONSTRAINT `examen_ibfk_1` FOREIGN KEY (`bodyPart_anatomicRegionSequence`) REFERENCES `bodypart` (`bodyPart_anatomicRegionSequence`),
  ADD CONSTRAINT `examen_ibfk_2` FOREIGN KEY (`anatomicOrientation_name`) REFERENCES `anatomicorientation` (`anatomicOrientation_name`),
  ADD CONSTRAINT `examen_ibfk_3` FOREIGN KEY (`posture_name`) REFERENCES `posture` (`posture_name`),
  ADD CONSTRAINT `examen_ibfk_4` FOREIGN KEY (`operateur_name`) REFERENCES `operateur` (`operateur_name`),
  ADD CONSTRAINT `examen_ibfk_5` FOREIGN KEY (`realisateur_performingPhysicianName`) REFERENCES `realisateur` (`realisateur_performingPhysicianName`),
  ADD CONSTRAINT `examen_ibfk_6` FOREIGN KEY (`prescripteur_referringPhysicianName`) REFERENCES `prescripteur` (`prescripteur_referringPhysicianName`),
  ADD CONSTRAINT `examen_ibfk_7` FOREIGN KEY (`patient_insee`) REFERENCES `patient` (`patient_insee`);

--
-- Contraintes pour la table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `image_ibfk_1` FOREIGN KEY (`serie_instanceNumber`) REFERENCES `serie` (`serie_instanceNumber`);

--
-- Contraintes pour la table `serie`
--
ALTER TABLE `serie`
  ADD CONSTRAINT `serie_ibfk_1` FOREIGN KEY (`study_id`) REFERENCES `study` (`study_id`);

--
-- Contraintes pour la table `signal`
--
ALTER TABLE `signal`
  ADD CONSTRAINT `signal_ibfk_1` FOREIGN KEY (`serie_instanceNumber`) REFERENCES `serie` (`serie_instanceNumber`);

--
-- Contraintes pour la table `study`
--
ALTER TABLE `study`
  ADD CONSTRAINT `study_ibfk_1` FOREIGN KEY (`patient_insee`) REFERENCES `patient` (`patient_insee`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
