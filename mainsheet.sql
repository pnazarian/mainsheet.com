-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 09, 2014 at 01:07 AM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mainsheet`
--
CREATE DATABASE IF NOT EXISTS `mainsheet` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `mainsheet`;

-- --------------------------------------------------------

--
-- Table structure for table `article_authors`
--

CREATE TABLE IF NOT EXISTS `article_authors` (
  `articleID` int(10) unsigned NOT NULL,
  `authorID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`articleID`,`authorID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `article_images`
--

CREATE TABLE IF NOT EXISTS `article_images` (
  `articleID` int(10) unsigned NOT NULL,
  `imageID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`articleID`,`imageID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `articleID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sectionID` int(10) unsigned NOT NULL,
  `volumeID` int(10) unsigned DEFAULT NULL,
  `issueNumber` int(10) unsigned DEFAULT NULL,
  `pageNumber` int(10) unsigned DEFAULT NULL,
  `articleTitle` char(100) NOT NULL,
  `articleText` text NOT NULL,
  `date` date NOT NULL,
  `keywords` char(250) DEFAULT NULL,
  `promotion` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`articleID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=56 ;

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE IF NOT EXISTS `authors` (
  `authorID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `authorFirstName` char(25) NOT NULL,
  `authorLastName` char(25) NOT NULL,
  PRIMARY KEY (`authorID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `imageID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `caption` char(200) DEFAULT NULL,
  `credit` char(100) DEFAULT NULL,
  PRIMARY KEY (`imageID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE IF NOT EXISTS `issues` (
  `volumeID` int(10) unsigned NOT NULL,
  `issueNumber` int(10) unsigned NOT NULL,
  `googleFolderID` char(50) NOT NULL,
  `googlePageFolderID` char(50) NOT NULL,
  `googleImageFolderID` char(50) NOT NULL,
  `issueDate` date DEFAULT NULL,
  PRIMARY KEY (`volumeID`,`issueNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `pageID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `volumeID` int(10) unsigned NOT NULL,
  `issueNumber` int(10) unsigned NOT NULL,
  `pageNumber` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pageID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE IF NOT EXISTS `sections` (
  `sectionID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sectionName` char(35) DEFAULT NULL,
  PRIMARY KEY (`sectionID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
