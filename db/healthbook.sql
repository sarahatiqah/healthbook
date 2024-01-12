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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `password`, `adminId`, `adminName`) VALUES
(1, '123', 'admin', 'Super Admin');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appId` int(11) NOT NULL,
  `patientId` int(11) NOT NULL,
  `dependentID` int(11) DEFAULT NULL,
  `appDate` date DEFAULT NULL,
  `appTime` time DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'pending',
  `doctorID` bigint(20) NOT NULL,
  `receipt` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_data`
--

CREATE TABLE `assessment_data` (
  `id_assesment` int(11) NOT NULL,
  `symptoms` varchar(255) NOT NULL,
  `type_of_symptoms` varchar(1000) DEFAULT NULL,
  `contact` varchar(3) DEFAULT NULL,
  `travel` varchar(3) DEFAULT NULL,
  `exposure` varchar(3) DEFAULT NULL,
  `hygiene` varchar(3) DEFAULT NULL,
  `symptom_duration` varchar(20) DEFAULT NULL,
  `assessmentResult` varchar(1000) NOT NULL,
  `patientID` int(11) NOT NULL,
  `date_assessment` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dependent`
--

CREATE TABLE `dependent` (
  `id_dependent` int(11) NOT NULL,
  `name_dependent` varchar(500) NOT NULL,
  `relationship` varchar(500) NOT NULL,
  `patientId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`id`, `doctorId`, `password`, `doctorName`, `doctorEmail`, `doctorPhone`, `specialization`) VALUES
(1, 'D001', '123', 'Doctor Adam', 'adam@healthbook.com', '0192348976', 1);

-- --------------------------------------------------------

--
-- Table structure for table `educational`
--

CREATE TABLE `educational` (
  `id_educational` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `document` varchar(1000) NOT NULL,
  `doctorID` bigint(20) NOT NULL,
  `tags` varchar(500) NOT NULL,
  `date_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `educational`
--

INSERT INTO `educational` (`id_educational`, `title`, `document`, `doctorID`, `tags`, `date_update`) VALUES
(1, 'Decoding Respiratory Illnesses: Recognizing Symptoms and Seeking Treatment', '../educational-resources/Testing.pdf', 1, '[{\"value\":\"Fever\"},{\"value\":\"Cough\"},{\"value\":\"Flu\"}]', '2023-12-29 16:28:04'),
(2, 'Demystifying Diabetes: A Comprehensive Guide to Blood Sugar Control', '../educational-resources/Testing.pdf', 1, '[{\"value\":\"Diabetes\"}]', '2023-12-29 16:28:57'),
(3, 'Understanding Hypertension: Navigating High Blood Pressure', '../educational-resources/Testing.pdf', 1, '[{\"value\":\"Hypertension\"},{\"value\":\"High Blood Pressure\"}]', '2023-12-29 16:29:36'),
(4, 'Headaches Unveiled: Types, Triggers, and Strategies for Relief', '../educational-resources/Testing.pdf', 1, '[{\"value\":\"Headaches\"},{\"value\":\"Migraines\"}]', '2023-12-29 16:30:08'),
(5, 'Breathing Easy: Recognizing and Treating Asthma Symptoms', '../educational-resources/Testing.pdf', 1, '[{\"value\":\"Asthma\"}]', '2023-12-29 16:30:38');

-- --------------------------------------------------------

--
-- Table structure for table `health_metrics`
--

CREATE TABLE `health_metrics` (
  `id_health` int(11) NOT NULL,
  `height` varchar(500) NOT NULL,
  `weight` varchar(500) NOT NULL,
  `medical_issues` varchar(1000) NOT NULL,
  `allergies` varchar(1000) NOT NULL,
  `current_medication` varchar(1000) NOT NULL,
  `patientId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `patientGender` varchar(50) NOT NULL,
  `patientRace` varchar(50) NOT NULL,
  `patientAddress` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `id_record` int(11) NOT NULL,
  `appId` int(11) NOT NULL,
  `diagnosis` varchar(1000) NOT NULL,
  `clarification` varchar(1000) DEFAULT NULL,
  `clinical_progress` varchar(1000) DEFAULT NULL,
  `care_plan` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id_review` int(11) NOT NULL,
  `appID` int(11) NOT NULL,
  `post` varchar(1000) NOT NULL,
  `rating` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `specialization`
--

CREATE TABLE `specialization` (
  `id_specialization` int(11) NOT NULL,
  `name_specialization` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `specialization`
--

INSERT INTO `specialization` (`id_specialization`, `name_specialization`) VALUES
(1, 'General Medicine'),
(2, 'Internal Medicine'),
(3, 'Pediatrics'),
(4, 'Obstetrics/Gynecology'),
(5, 'Dermatology');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `staffId`, `password`, `staffName`, `staffEmail`, `staffPhone`, `staffAddress`) VALUES
(1, 'S001', '123', 'Aleeya', 'aleeya@healthbook.com', '0129872345', 'Selangor');

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
  ADD KEY `FK_did` (`doctorID`),
  ADD KEY `FK_dependent` (`dependentID`);

--
-- Indexes for table `assessment_data`
--
ALTER TABLE `assessment_data`
  ADD PRIMARY KEY (`id_assesment`),
  ADD KEY `FK_patient_assessment` (`patientID`);

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
-- Indexes for table `educational`
--
ALTER TABLE `educational`
  ADD PRIMARY KEY (`id_educational`),
  ADD KEY `FK_edu_DID` (`doctorID`);

--
-- Indexes for table `health_metrics`
--
ALTER TABLE `health_metrics`
  ADD PRIMARY KEY (`id_health`),
  ADD KEY `FK_patientH` (`patientId`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `records`
--
ALTER TABLE `records`
  ADD PRIMARY KEY (`id_record`),
  ADD KEY `FK_appID` (`appId`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `FK_review` (`appID`);

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assessment_data`
--
ALTER TABLE `assessment_data`
  MODIFY `id_assesment` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dependent`
--
ALTER TABLE `dependent`
  MODIFY `id_dependent` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `educational`
--
ALTER TABLE `educational`
  MODIFY `id_educational` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `health_metrics`
--
ALTER TABLE `health_metrics`
  MODIFY `id_health` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `records`
--
ALTER TABLE `records`
  MODIFY `id_record` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `specialization`
--
ALTER TABLE `specialization`
  MODIFY `id_specialization` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `FK_dependent` FOREIGN KEY (`dependentID`) REFERENCES `dependent` (`id_dependent`),
  ADD CONSTRAINT `FK_did` FOREIGN KEY (`doctorID`) REFERENCES `doctor` (`id`),
  ADD CONSTRAINT `FK_pid` FOREIGN KEY (`patientId`) REFERENCES `patient` (`id`);

--
-- Constraints for table `assessment_data`
--
ALTER TABLE `assessment_data`
  ADD CONSTRAINT `FK_patient_assessment` FOREIGN KEY (`patientID`) REFERENCES `patient` (`id`);

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

--
-- Constraints for table `educational`
--
ALTER TABLE `educational`
  ADD CONSTRAINT `FK_edu_DID` FOREIGN KEY (`doctorID`) REFERENCES `doctor` (`id`);

--
-- Constraints for table `health_metrics`
--
ALTER TABLE `health_metrics`
  ADD CONSTRAINT `FK_patientH` FOREIGN KEY (`patientId`) REFERENCES `patient` (`id`);

--
-- Constraints for table `records`
--
ALTER TABLE `records`
  ADD CONSTRAINT `FK_appID` FOREIGN KEY (`appId`) REFERENCES `appointment` (`appId`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `FK_review` FOREIGN KEY (`appID`) REFERENCES `appointment` (`appId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
