-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 19, 2019 at 04:44 PM
-- Server version: 5.5.62-0+deb8u1
-- PHP Version: 5.6.39-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tripMoney`
--

-- --------------------------------------------------------

--
-- Table structure for table `payer`
--

CREATE TABLE IF NOT EXISTS `payer` (
  `pay_record` int(11) NOT NULL,
  `payer` int(11) NOT NULL,
  `amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `pay_for`
--

CREATE TABLE IF NOT EXISTS `pay_for` (
  `pay_record` int(11) NOT NULL,
  `payee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `pay_record`
--

CREATE TABLE IF NOT EXISTS `pay_record` (
`id` int(11) NOT NULL,
  `trip` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `currency` varchar(3) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `waived` tinyint(1) NOT NULL DEFAULT '0',
  `IP` text COLLATE utf8_bin NOT NULL,
  `UserAgent` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `people`
--

CREATE TABLE IF NOT EXISTS `people` (
`person_id` int(11) NOT NULL,
  `name` varchar(16) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `trip`
--

CREATE TABLE IF NOT EXISTS `trip` (
`trip_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `name` varchar(16) COLLATE utf8_bin NOT NULL,
  `password` text COLLATE utf8_bin
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `trip_participant`
--

CREATE TABLE IF NOT EXISTS `trip_participant` (
  `trip_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payer`
--
ALTER TABLE `payer`
 ADD PRIMARY KEY (`pay_record`,`payer`);

--
-- Indexes for table `pay_for`
--
ALTER TABLE `pay_for`
 ADD PRIMARY KEY (`pay_record`,`payee`);

--
-- Indexes for table `pay_record`
--
ALTER TABLE `pay_record`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `people`
--
ALTER TABLE `people`
 ADD PRIMARY KEY (`person_id`);

--
-- Indexes for table `trip`
--
ALTER TABLE `trip`
 ADD PRIMARY KEY (`trip_id`);

--
-- Indexes for table `trip_participant`
--
ALTER TABLE `trip_participant`
 ADD PRIMARY KEY (`trip_id`,`person_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pay_record`
--
ALTER TABLE `pay_record`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `people`
--
ALTER TABLE `people`
MODIFY `person_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `trip`
--
ALTER TABLE `trip`
MODIFY `trip_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
