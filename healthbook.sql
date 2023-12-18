-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 16, 2023 at 02:27 PM
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
-- Database: `healthbook`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `icAdmin` bigint NOT NULL,
  `password` varchar(20) NOT NULL,
  `adminId` varchar(50) NOT NULL,
  `adminFirstName` varchar(50) NOT NULL,
  `adminLastName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`icAdmin`, `password`, `adminId`, `adminFirstName`, `adminLastName`) VALUES
(123456789, 'admin', 'admin', 'Super', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appId` int NOT NULL,
  `patientIc` bigint NOT NULL,
  `scheduleId` int NOT NULL,
  `appSymptom` varchar(100) NOT NULL,
  `appComment` varchar(100) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'process'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

CREATE TABLE `article` (
  `id_article` int NOT NULL,
  `title` text NOT NULL,
  `article` varchar(5000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`id_article`, `title`, `article`) VALUES
(1, 'Coronavirus disease (COVID-19) is an infectious disease caused by the SARS-CoV-2 virus', 'Most people infected with the virus will experience mild to moderate respiratory illness and recover without requiring special treatment. However, some will become seriously ill and require medical attention. Older people and those with underlying medical conditions like cardiovascular disease, diabetes, chronic respiratory disease, or cancer are more likely to develop serious illness. Anyone can get sick with COVID-19 and become seriously ill or die at any age.<br><br>\r\nThe best way to prevent and slow down transmission is to be well informed about the disease and how the virus spreads. Protect yourself and others from infection by staying at least 1 metre apart from others, wearing a properly fitted mask, and washing your hands or using an alcohol-based rub frequently. Get vaccinated when it\'s your turn and follow local guidance.<br><br>\r\n\r\nThe virus can spread from an infected person\'s mouth or nose in small liquid particles when they cough, sneeze, speak, sing or breathe. These particles range from larger respiratory droplets to smaller aerosols. It is important to practice respiratory etiquette, for example by coughing into a flexed elbow, and to stay home and self-isolate until you recover if you fell unwell.');

-- --------------------------------------------------------

--
-- Table structure for table `contactus`
--

CREATE TABLE `contactus` (
  `id_contact` int NOT NULL,
  `fname_contact` varchar(255) NOT NULL,
  `lname_contact` varchar(255) NOT NULL,
  `email_contact` varchar(100) NOT NULL,
  `enquiry` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contactus`
--

INSERT INTO `contactus` (`id_contact`, `fname_contact`, `lname_contact`, `email_contact`, `enquiry`) VALUES
(2, 'asdh', 'asdn', 'asd@gmail.com', 'asdasda nice hahaha'),
(3, 'vin', 'dinesh', 'f@com', 'how to register');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `id` bigint NOT NULL,
  `doctorId` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(20) NOT NULL,
  `doctorName` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `doctorEmail` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `doctorPhone` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `specialization` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`id`, `doctorId`, `password`, `doctorName`, `doctorEmail`, `doctorPhone`, `specialization`) VALUES
(1, 'D001', '123', 'Kannan Raj', 'doctor@com', '012', 2),
(2, 'D002', '123', 'Doctor Cristiano', 'cr73@gmail.com', '534535', 1),
(3, 'D003', '123', 'Doctor John', 'jmatthew@gmail.com', '12', 1),
(4, 'D004', '123', 'Ramesh', 'ramesh@healthbook.com', '01233434', 3),
(5, 'D005', '213', 'Ramesh', 'rames2h@healthbook.com', '01233434', 2);

-- --------------------------------------------------------

--
-- Table structure for table `doctorschedule`
--

CREATE TABLE `doctorschedule` (
  `scheduleId` int NOT NULL,
  `scheduleDate` date NOT NULL,
  `scheduleDay` varchar(15) NOT NULL,
  `scheduleTime` varchar(50) NOT NULL,
  `startTime` time DEFAULT NULL,
  `endTime` time DEFAULT NULL,
  `bookAvail` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id_feedback` int NOT NULL,
  `comment` varchar(500) NOT NULL,
  `rating` int NOT NULL,
  `ic_patient` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id_feedback`, `comment`, `rating`, `ic_patient`) VALUES
(1, 'nice', 5, ''),
(2, 'good', 5, ''),
(3, 'redasd', 4, '123'),
(4, 'sdfsdf', 4, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `management`
--

CREATE TABLE `management` (
  `icManage` bigint NOT NULL,
  `password` varchar(20) NOT NULL,
  `manageId` varchar(5) NOT NULL,
  `manageFirstName` varchar(255) NOT NULL,
  `manageLastName` varchar(255) NOT NULL,
  `manageAddress` varchar(255) NOT NULL,
  `managePhone` varchar(15) NOT NULL,
  `manageEmail` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `management`
--

INSERT INTO `management` (`icManage`, `password`, `manageId`, `manageFirstName`, `manageLastName`, `manageAddress`, `managePhone`, `manageEmail`) VALUES
(12, '123', 'M002', 'don', 'kiran', 'malim', '0239', 'don@com'),
(123, '123', 'M001', 'John', 'Cena', 'California', '3434', 'jc@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `medicine`
--

CREATE TABLE `medicine` (
  `id_medicine` int NOT NULL,
  `name_medicine` varchar(255) NOT NULL,
  `stock` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `medicine`
--

INSERT INTO `medicine` (`id_medicine`, `name_medicine`, `stock`) VALUES
(6, 'PANADOL', 35);

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `icPatient` bigint NOT NULL,
  `patientName` varchar(500) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `patientEmail` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(20) NOT NULL,
  `patientPhone` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `patientAddress` varchar(500) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`icPatient`, `patientName`, `patientEmail`, `password`, `patientPhone`, `patientAddress`) VALUES
(123, 'JOHN DOE', 'johndoe@gmail.com', '123', '011123', 'asdasd');

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

CREATE TABLE `prescription` (
  `id_prescription` int NOT NULL,
  `desc_prescription` varchar(255) NOT NULL,
  `price` varchar(11) DEFAULT NULL,
  `id_schedule` int NOT NULL,
  `id_appointment` int NOT NULL,
  `id_medicine` int NOT NULL,
  `id_room` int DEFAULT NULL,
  `warded` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `prescription`
--

INSERT INTO `prescription` (`id_prescription`, `desc_prescription`, `price`, `id_schedule`, `id_appointment`, `id_medicine`, `id_room`, `warded`) VALUES
(2, 'need to break', NULL, 87, 5, 6, 1, 'yes'),
(3, 'no need warded', NULL, 88, 6, 6, NULL, 'no'),
(4, 'need to exercies', NULL, 96, 10, 6, 2, 'yes'),
(5, 'need to exercies', NULL, 96, 10, 6, 2, 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id_room` int NOT NULL,
  `name_room` varchar(255) NOT NULL,
  `level_room` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id_room`, `name_room`, `level_room`) VALUES
(0, 'None', 0),
(1, 'KENANGA', 1),
(2, 'DELUXE', 5);

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
  `staffId` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(20) NOT NULL,
  `staffName` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `staffEmail` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `staffPhone` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `staffAddress` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `staffId`, `password`, `staffName`, `staffEmail`, `staffPhone`, `staffAddress`) VALUES
(1, 'N002', '123', 'vimala', 'vimala@com', '012', 'puchong'),
(2, 'N001', '123', 'Mariah', 'mcarey@gmail.com', '5656', 'MEXICO');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`icAdmin`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appId`),
  ADD KEY `patientIc` (`patientIc`);

--
-- Indexes for table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id_article`);

--
-- Indexes for table `contactus`
--
ALTER TABLE `contactus`
  ADD PRIMARY KEY (`id_contact`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_specialization` (`specialization`);

--
-- Indexes for table `doctorschedule`
--
ALTER TABLE `doctorschedule`
  ADD PRIMARY KEY (`scheduleId`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id_feedback`);

--
-- Indexes for table `management`
--
ALTER TABLE `management`
  ADD PRIMARY KEY (`icManage`);

--
-- Indexes for table `medicine`
--
ALTER TABLE `medicine`
  ADD PRIMARY KEY (`id_medicine`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`icPatient`);

--
-- Indexes for table `prescription`
--
ALTER TABLE `prescription`
  ADD PRIMARY KEY (`id_prescription`),
  ADD KEY `room` (`id_room`),
  ADD KEY `medicine` (`id_medicine`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id_room`);

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
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `contactus`
--
ALTER TABLE `contactus`
  MODIFY `id_contact` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `doctorschedule`
--
ALTER TABLE `doctorschedule`
  MODIFY `scheduleId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id_feedback` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `medicine`
--
ALTER TABLE `medicine`
  MODIFY `id_medicine` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `prescription`
--
ALTER TABLE `prescription`
  MODIFY `id_prescription` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id_room` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `specialization`
--
ALTER TABLE `specialization`
  MODIFY `id_specialization` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_4` FOREIGN KEY (`patientIc`) REFERENCES `patient` (`icPatient`);

--
-- Constraints for table `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `FK_specialization` FOREIGN KEY (`specialization`) REFERENCES `specialization` (`id_specialization`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `medicine` FOREIGN KEY (`id_medicine`) REFERENCES `medicine` (`id_medicine`),
  ADD CONSTRAINT `room` FOREIGN KEY (`id_room`) REFERENCES `room` (`id_room`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
