-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 28, 2023 at 06:08 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `healthbook_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` bigint NOT NULL,
  `password` varchar(20) NOT NULL,
  `adminId` varchar(50) NOT NULL,
  `adminName` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `appId` int NOT NULL,
  `patientId` int NOT NULL,
  `dependentID` int DEFAULT NULL,
  `appDate` date DEFAULT NULL,
  `appTime` time DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'pending',
  `doctorID` bigint NOT NULL,
  `receipt` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appId`, `patientId`, `dependentID`, `appDate`, `appTime`, `status`, `doctorID`, `receipt`) VALUES
(10, 1, 6, '2023-12-19', '09:00:00', 'done', 1, NULL),
(17, 1, NULL, '2023-12-20', '14:00:00', 'approved', 4, NULL),
(20, 5, NULL, '2023-12-19', '13:00:00', 'done', 2, NULL),
(21, 5, NULL, '2023-12-21', '09:00:00', 'approved', 3, NULL),
(34, 1, NULL, '2023-12-22', '12:00:00', 'done', 2, '../patient/receipt/kew.at-3.pdf'),
(35, 5, NULL, '2023-12-24', '09:00:00', 'done', 1, NULL),
(36, 5, 8, '2023-12-26', '15:00:00', 'done', 1, '../patient/receipt/2. Surat rasmi.pdf'),
(39, 5, 8, '2023-12-24', '16:00:00', 'done', 1, NULL),
(41, 1, NULL, '2023-12-25', '13:00:00', 'approved', 1, NULL),
(44, 1, 8, '2023-12-25', '15:00:00', 'approved', 1, NULL),
(45, 5, 8, '2023-12-26', '17:00:00', 'approved', 1, NULL),
(46, 5, 8, '2023-12-26', '16:00:00', 'done', 1, NULL),
(47, 5, 8, '2023-12-26', '12:00:00', 'approved', 1, NULL),
(59, 5, NULL, '2023-12-27', '10:00:00', 'approved', 1, NULL),
(60, 1, 6, '2023-12-27', '11:00:00', 'approved', 1, NULL),
(62, 5, NULL, '2023-12-27', '11:00:00', 'approved', 2, NULL),
(67, 5, 8, '2023-12-27', '09:00:00', 'pending', 1, NULL),
(68, 5, 10, '2023-12-28', '12:00:00', 'pending', 1, NULL),
(69, 5, 8, '2023-12-27', '13:00:00', 'pending', 1, NULL),
(70, 5, 8, '2023-12-28', '10:00:00', 'pending', 1, NULL),
(71, 5, NULL, '2023-12-27', '09:00:00', 'approved', 6, NULL),
(75, 5, NULL, '2023-12-28', '11:00:00', 'pending', 2, NULL),
(76, 6, NULL, '2023-12-27', '11:00:00', 'done', 6, NULL),
(89, 1, NULL, '2023-12-29', '09:00:00', 'pending', 2, NULL),
(99, 1, NULL, '2023-12-29', '12:00:00', 'pending', 6, NULL),
(100, 1, 6, '2023-12-29', '14:00:00', 'done', 1, NULL),
(102, 1, NULL, '2023-12-31', '09:00:00', 'approved', 2, NULL),
(103, 1, NULL, '2023-12-29', '16:00:00', 'pending', 2, NULL),
(104, 1, 6, '2023-12-29', '17:00:00', 'pending', 1, NULL),
(105, 13, 12, '2023-12-29', '09:00:00', 'done', 1, '../patient/receipt/TEST 1.pdf'),
(106, 13, NULL, '2023-12-29', '10:00:00', 'approved', 2, NULL),
(108, 13, 12, '2023-12-29', '15:00:00', 'approved', 3, NULL),
(109, 13, NULL, '2023-12-30', '10:00:00', 'approved', 4, NULL),
(110, 13, NULL, '2023-12-29', '13:00:00', 'done', 1, NULL),
(111, 13, NULL, '2023-12-29', '15:00:00', 'pending', 1, NULL),
(112, 13, 12, '2023-12-29', '16:00:00', 'pending', 1, NULL),
(113, 13, NULL, '2023-12-30', '09:00:00', 'pending', 1, NULL),
(114, 13, 12, '2023-12-29', '17:00:00', 'approved', 6, NULL),
(117, 13, NULL, '2023-12-30', '14:00:00', 'pending', 1, NULL),
(118, 13, NULL, '2023-12-31', '11:00:00', 'pending', 1, NULL),
(120, 13, 12, '2023-12-31', '09:00:00', 'pending', 1, NULL),
(123, 13, NULL, '2023-12-29', '12:00:00', 'approved', 4, NULL),
(128, 13, 12, '2023-12-31', '13:00:00', 'pending', 4, NULL),
(129, 13, 12, '2023-12-30', '17:00:00', 'pending', 1, NULL),
(131, 13, NULL, '2023-12-30', '12:00:00', 'pending', 1, NULL),
(132, 1, NULL, '2023-12-30', '13:00:00', 'pending', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `assessment_data`
--

CREATE TABLE `assessment_data` (
  `id_assesment` int NOT NULL,
  `symptoms` varchar(255) NOT NULL,
  `type_of_symptoms` varchar(1000) DEFAULT NULL,
  `contact` varchar(3) DEFAULT NULL,
  `travel` varchar(3) DEFAULT NULL,
  `exposure` varchar(3) DEFAULT NULL,
  `hygiene` varchar(3) DEFAULT NULL,
  `symptom_duration` varchar(20) DEFAULT NULL,
  `assessmentResult` varchar(1000) NOT NULL,
  `patientID` int NOT NULL,
  `date_assessment` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `assessment_data`
--

INSERT INTO `assessment_data` (`id_assesment`, `symptoms`, `type_of_symptoms`, `contact`, `travel`, `exposure`, `hygiene`, `symptom_duration`, `assessmentResult`, `patientID`, `date_assessment`) VALUES
(22, 'yes', 'fever', 'yes', 'no', 'no', 'no', '1-3 days', 'Based on your responses, it is recommended to consult with a healthcare professional and consider getting tested for COVID-19.', 1, '2023-12-26 05:38:02'),
(23, 'no', '', 'yes', 'no', 'no', 'yes', 'Not Applicable', 'Based on your responses, it is recommended to consult with a healthcare professional and consider getting tested for COVID-19.', 1, '2023-12-26 05:40:09'),
(24, 'yes', 'fever, cough', 'no', 'no', 'no', 'yes', 'Not Applicable', 'Based on your responses, it is recommended to consult with a healthcare professional and consider getting tested for COVID-19.', 1, '2023-12-26 05:41:55'),
(25, 'no', '', 'no', 'yes', 'yes', 'yes', 'Not Applicable', 'Based on your responses, you have traveled to a high-risk area and have been exposed to crowded places. It is recommended to monitor your health and consider getting tested.', 1, '2023-12-26 05:42:18'),
(26, 'yes', 'fever', 'yes', 'no', 'yes', 'yes', '1-3 days', 'Based on your responses, it is recommended to consult with a healthcare professional and consider getting tested for COVID-19.', 5, '2023-12-26 05:47:09'),
(27, 'no', '', 'no', 'no', 'no', 'yes', 'Not Applicable', 'Based on your responses, you are not exhibiting significant symptoms or risk factors. Continue to practice good hygiene and monitor your health.', 5, '2023-12-26 05:47:47'),
(28, 'no', '', 'yes', 'no', 'yes', 'yes', '1-3 days', 'Based on your responses, it is recommended to consult with a healthcare professional and consider getting tested for COVID-19.', 5, '2023-12-26 11:31:00'),
(29, 'yes', 'fever, cough', 'yes', 'yes', 'yes', 'no', 'Not Applicable', 'Based on your responses, it is recommended to consult with a healthcare professional and consider getting tested for COVID-19.', 1, '2023-12-26 16:30:08'),
(30, 'yes', 'fever', 'no', 'no', 'yes', 'yes', '1-3 days', 'Based on your responses, it is recommended to consult with a healthcare professional and consider getting tested for COVID-19.', 13, '2023-12-28 15:57:52'),
(31, 'no', '', 'no', 'no', 'no', 'yes', 'Not Applicable', 'Based on your responses, you are not exhibiting significant symptoms or risk factors. Continue to practice good hygiene and monitor your health.', 13, '2023-12-28 15:58:10');

-- --------------------------------------------------------

--
-- Table structure for table `dependent`
--

CREATE TABLE `dependent` (
  `id_dependent` int NOT NULL,
  `name_dependent` varchar(500) NOT NULL,
  `relationship` varchar(500) NOT NULL,
  `patientId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dependent`
--

INSERT INTO `dependent` (`id_dependent`, `name_dependent`, `relationship`, `patientId`) VALUES
(5, 'Alia', 'Daughter', 4),
(6, 'LIONEL MESSI', 'BROTHER', 1),
(8, 'Ali', 'Son', 5),
(9, 'Aliah', 'Daughter', 5),
(10, 'Arif', 'Son', 5),
(12, 'Nurul A', 'Daughter', 13);

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `id` bigint NOT NULL,
  `doctorId` varchar(5) NOT NULL,
  `password` varchar(200) NOT NULL,
  `doctorName` varchar(255) NOT NULL,
  `doctorEmail` varchar(255) NOT NULL,
  `doctorPhone` varchar(15) NOT NULL,
  `specialization` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`id`, `doctorId`, `password`, `doctorName`, `doctorEmail`, `doctorPhone`, `specialization`) VALUES
(1, 'D001', '123', 'Dr. Kannan Raj', 'test@com', '22', 3),
(2, 'D002', '123', 'Doctor Cristiano', 'cr73@gmail.com', '534535', 1),
(3, 'D003', '123', 'Doctor John', 'jmatthew@gmail.com', '12', 1),
(4, 'D004', '123', 'Doctor Ramesh', 'ramesh@healthbook.com', '01233434', 3),
(6, 'D005', '123', 'Doctor Dewi', 'test1@gmail.com', '1234', 2),
(7, 'D006', '123', 'test', 'test2@gmail.com', '345', 1);

-- --------------------------------------------------------

--
-- Table structure for table `educational`
--

CREATE TABLE `educational` (
  `id_educational` int NOT NULL,
  `title` varchar(500) NOT NULL,
  `document` varchar(1000) NOT NULL,
  `doctorID` bigint NOT NULL,
  `tags` varchar(500) NOT NULL,
  `date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `educational`
--

INSERT INTO `educational` (`id_educational`, `title`, `document`, `doctorID`, `tags`, `date_update`) VALUES
(4, 'TEst Title', '../educational-resources/kew.at-3.pdf', 3, '[{\"value\":\"Flu\"}]', '2023-12-19 00:48:41'),
(6, 'test', '../educational-resources/LAYOUT PLAQ PENGHARGAAN.pdf', 1, '[{\"value\":\"Diabetes\"},{\"value\":\"Flu\"},{\"value\":\"Fever\"}]', '2023-12-21 00:48:41'),
(7, 'asdffd', '../educational-resources/LAYOUT PLAQ PENGHARGAAN.pdf', 1, '[{\"value\":\"Diabetes\"},{\"value\":\"Flu\"}]', '2023-12-23 00:48:41'),
(8, 'yrdt', '../educational-resources/LAYOUT PLAQ PENGHARGAAN.pdf', 1, '[{\"value\":\"Diabetes\"},{\"value\":\"Flu\"}]', '2023-12-24 00:48:41'),
(9, 'sdf', '../educational-resources/LAYOUT PLAQ PENGHARGAAN.pdf', 1, '[{\"value\":\"Heart Attack\"},{\"value\":\"Flu\"},{\"value\":\"Diabetes\"},{\"value\":\"Fever\"}]', '2023-12-26 00:48:41'),
(10, 'testt', '../educational-resources/DP Lab 3 Factory - new.pdf', 1, '[{\"value\":\"Cough\"}]', '2023-12-26 06:18:40'),
(11, 'Test', '../educational-resources/TEST 1.pdf', 7, '[{\"value\":\"High Blood Pressure\"},{\"value\":\"Headache\"}]', '2023-12-28 16:05:52'),
(13, 'Test 3', '../educational-resources/TEST 1.pdf', 7, '[{\"value\":\"fever\"},{\"value\":\"Nausea\"},{\"value\":\"Stomach Ache\"}]', '2023-12-28 16:06:49');

-- --------------------------------------------------------

--
-- Table structure for table `health_metrics`
--

CREATE TABLE `health_metrics` (
  `id_health` int NOT NULL,
  `height` varchar(500) NOT NULL,
  `weight` varchar(500) NOT NULL,
  `medical_issues` varchar(1000) NOT NULL,
  `allergies` varchar(1000) NOT NULL,
  `current_medication` varchar(1000) NOT NULL,
  `patientId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `health_metrics`
--

INSERT INTO `health_metrics` (`id_health`, `height`, `weight`, `medical_issues`, `allergies`, `current_medication`, `patientId`) VALUES
(2, '167', '65.3', 'You have flu, that we tested and also diabetes.', 'seafood', 'yes', 1),
(6, '155', '51', 'Heart Attack, High Blood Pressure flu', 'No', 'No', 5),
(8, '160', '56.5', 'Headache, High Blood Pressure, Fever', 'No', 'No', 13);

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `id` int NOT NULL,
  `icPatient` bigint NOT NULL,
  `patientName` varchar(500) NOT NULL,
  `patientEmail` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `patientPhone` varchar(15) NOT NULL,
  `patientGender` varchar(50) NOT NULL,
  `patientRace` varchar(50) NOT NULL,
  `patientAddress` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`id`, `icPatient`, `patientName`, `patientEmail`, `password`, `patientPhone`, `patientGender`, `patientRace`, `patientAddress`) VALUES
(1, 123, 'JOHN DOE', 'gotocodex@gmail.com', '123', '011123', 'Male', 'Malay', 'LOT 125, KAMPUNG KONOHA'),
(4, 980203012345, 'Test 123', 'test@gmail.com', 'test', '0101234567', 'Female', 'Chinese', 'Test'),
(5, 981112149878, 'Sarah', 'powergirl285@gmail.com', '123', '0171234567', 'Female', 'Malay', 'Test'),
(6, 971212011234, 'Test', 'test123@gmail.com', '123', '1234567890', 'Female', 'Malay', 'test'),
(11, 7654321, 'Diana', 'asd', '123', '34567', 'Female', 'Malay', 'test'),
(12, 567890, 'Liyana', '123', '123', '786543', 'Female', 'Malay', 'test'),
(13, 980323103456, 'Tasha', 'canva101workshop@gmail.com', '123', '0192345678', 'Female', 'Malay', 'Test');

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `id_record` int NOT NULL,
  `appId` int NOT NULL,
  `diagnosis` varchar(1000) NOT NULL,
  `clarification` varchar(1000) DEFAULT NULL,
  `clinical_progress` varchar(1000) DEFAULT NULL,
  `care_plan` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`id_record`, `appId`, `diagnosis`, `clarification`, `clinical_progress`, `care_plan`) VALUES
(9, 10, 'test sakit', NULL, NULL, NULL),
(10, 20, '', NULL, NULL, NULL),
(11, 34, 'testttttt fff', NULL, NULL, NULL),
(12, 35, 'Sakit perut', 'testr', 'test', 'addd'),
(13, 36, 'Pening Kepala', 'test', 'test', 'test'),
(14, 39, 'cvxbvcb', '', '', ''),
(15, 76, 'flu fever sakit2', '', '', ''),
(16, 110, 'Flu, Fever', '', '', ''),
(17, 105, 'High Blood Pressure', '', '', ''),
(18, 100, 'Fever, Cough', '', '', ''),
(19, 46, 'testt', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id_review` int NOT NULL,
  `appID` int NOT NULL,
  `post` varchar(1000) NOT NULL,
  `rating` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id_review`, `appID`, `post`, `rating`) VALUES
(1, 10, 'test', 3),
(2, 10, 'tyerf', 2),
(3, 10, 'very good', 5),
(4, 20, 'test', 3),
(5, 20, 'test', 4),
(6, 20, 'Test', 4),
(7, 35, 'bad', 1),
(8, 35, 'Good', 5),
(9, 35, 'Nice', 4),
(10, 105, 'Good Doctor', 5);

-- --------------------------------------------------------

--
-- Table structure for table `specialization`
--

CREATE TABLE `specialization` (
  `id_specialization` int NOT NULL,
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
  `id` int NOT NULL,
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
(1, 'S001', '123', 'vimalas', 'vimala@com', '55', 'Puchong'),
(2, 'S002', '123', 'Mariah Carey', 'mcarey@gmail.com', '5656', 'MEXICO'),
(6, 'S003', '123', 'test', 'test3@gmail.com', '123', '1');

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
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `assessment_data`
--
ALTER TABLE `assessment_data`
  MODIFY `id_assesment` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `dependent`
--
ALTER TABLE `dependent`
  MODIFY `id_dependent` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `educational`
--
ALTER TABLE `educational`
  MODIFY `id_educational` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `health_metrics`
--
ALTER TABLE `health_metrics`
  MODIFY `id_health` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `records`
--
ALTER TABLE `records`
  MODIFY `id_record` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id_review` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `specialization`
--
ALTER TABLE `specialization`
  MODIFY `id_specialization` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
