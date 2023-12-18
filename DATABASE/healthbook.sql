-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 18, 2023 at 06:00 AM
-- Server version: 5.7.33
-- PHP Version: 8.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `healthbook`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` bigint(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `adminId` varchar(50) NOT NULL,
  `adminName` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `password`, `adminId`, `adminName`) VALUES
(1, 'admin', 'admin', 'Super Admin');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appId` int(11) NOT NULL,
  `patientId` int(11) NOT NULL,
  `appDate` date DEFAULT NULL,
  `appTime` time DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'pending',
  `doctorID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appId`, `patientId`, `appDate`, `appTime`, `status`, `doctorID`) VALUES
(1, 1, '2023-12-18', '10:00:00', 'pending', 1),
(2, 5, '2023-12-18', '13:00:00', 'done', 1),
(3, 4, '2023-12-18', '14:00:00', 'done', 1),
(4, 1, '2023-12-18', '15:00:00', 'pending', 1);

-- --------------------------------------------------------

--
-- Table structure for table `dependent`
--

CREATE TABLE `dependent` (
  `id_dependent` int(11) NOT NULL,
  `name_dependent` varchar(500) NOT NULL,
  `relationship` varchar(500) NOT NULL,
  `patientId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dependent`
--

INSERT INTO `dependent` (`id_dependent`, `name_dependent`, `relationship`, `patientId`) VALUES
(2, 'qeqweqeqeqweqwe', 'motherqweqe qweq e', 1),
(5, 'Alia', 'Daughter', 4);

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `id` bigint(20) NOT NULL,
  `doctorId` varchar(5) NOT NULL,
  `password` varchar(200) NOT NULL,
  `doctorName` varchar(255) NOT NULL,
  `doctorEmail` varchar(255) NOT NULL,
  `doctorPhone` varchar(15) NOT NULL,
  `specialization` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`id`, `doctorId`, `password`, `doctorName`, `doctorEmail`, `doctorPhone`, `specialization`) VALUES
(1, 'D001', '123', 'Dr. Kannan Raj', 'gotocodex@gmail.com', '1', 3),
(2, 'D002', '123', 'Doctor Cristiano', 'cr73@gmail.com', '534535', 1),
(3, 'D003', '123', 'Doctor John', 'jmatthew@gmail.com', '12', 1),
(4, 'D004', '123', 'Ramesh', 'ramesh@healthbook.com', '01233434', 3),
(5, 'D005', '213', 'Ramesh', 'rames2h@healthbook.com', '01233434', 2);

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `id` int(11) NOT NULL,
  `icPatient` bigint(20) NOT NULL,
  `patientName` varchar(500) NOT NULL,
  `patientEmail` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `patientPhone` varchar(15) NOT NULL,
  `patientAddress` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`id`, `icPatient`, `patientName`, `patientEmail`, `password`, `patientPhone`, `patientAddress`) VALUES
(1, 123, 'JOHN DOE', 'gotocodex@gmail.com', '123', '011123', 'r'),
(3, 1234, 'sad', 'SDsd', '123', '234', 'dsfsdf'),
(4, 980203012345, 'Test 123', 'test@gmail.com', 'test', '0101234567', 'Test'),
(5, 981112149878, 'Sarah', 'powergirl285@gmail.com', 'healthbook657fc62282001', '0171234567', 'Test');

-- --------------------------------------------------------

--
-- Table structure for table `specialization`
--

CREATE TABLE `specialization` (
  `id_specialization` int(11) NOT NULL,
  `name_specialization` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `specialization`
--

INSERT INTO `specialization` (`id_specialization`, `name_specialization`) VALUES
(1, 'Cardiology'),
(2, 'Dermatology'),
(3, 'Dentist');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `staffId` varchar(5) NOT NULL,
  `password` varchar(200) NOT NULL,
  `staffName` varchar(255) NOT NULL,
  `staffEmail` varchar(255) NOT NULL,
  `staffPhone` varchar(15) NOT NULL,
  `staffAddress` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `staffId`, `password`, `staffName`, `staffEmail`, `staffPhone`, `staffAddress`) VALUES
(1, 'S001', '123', 'vimala', 'vimala@com', '1', 'puchong here'),
(2, 'S002', '123', 'Mariah Carey', 'mcarey@gmail.com', '5656', 'MEXICOD');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appId`),
  ADD KEY `FK_pid` (`patientId`),
  ADD KEY `FK_did` (`doctorID`);

--
-- Indexes for table `dependent`
--
ALTER TABLE `dependent`
  ADD PRIMARY KEY (`id_dependent`),
  ADD KEY `FK_patientId` (`patientId`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_specialization` (`specialization`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `specialization`
--
ALTER TABLE `specialization`
  ADD PRIMARY KEY (`id_specialization`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dependent`
--
ALTER TABLE `dependent`
  MODIFY `id_dependent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `specialization`
--
ALTER TABLE `specialization`
  MODIFY `id_specialization` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `FK_did` FOREIGN KEY (`doctorID`) REFERENCES `doctor` (`id`),
  ADD CONSTRAINT `FK_pid` FOREIGN KEY (`patientId`) REFERENCES `patient` (`id`);

--
-- Constraints for table `dependent`
--
ALTER TABLE `dependent`
  ADD CONSTRAINT `FK_patientId` FOREIGN KEY (`patientId`) REFERENCES `patient` (`id`);

--
-- Constraints for table `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `FK_specialization` FOREIGN KEY (`specialization`) REFERENCES `specialization` (`id_specialization`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
