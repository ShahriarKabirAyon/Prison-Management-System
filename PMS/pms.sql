-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 31, 2025 at 11:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pms`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminstaff`
--

CREATE TABLE `adminstaff` (
  `StaffID` int(11) NOT NULL,
  `Department` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `block`
--

CREATE TABLE `block` (
  `BlockID` int(11) NOT NULL,
  `PrisonID` int(11) NOT NULL,
  `Name` varchar(80) NOT NULL,
  `Type` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `block`
--

INSERT INTO `block` (`BlockID`, `PrisonID`, `Name`, `Type`) VALUES
(1, 1, 'White House', 'General'),
(2, 2, 'MPH', 'General'),
(3, 3, 'White House', 'General');

-- --------------------------------------------------------

--
-- Table structure for table `case`
--

CREATE TABLE `case` (
  `CaseID` int(11) NOT NULL,
  `CaseNumber` varchar(50) NOT NULL,
  `CourtName` varchar(100) NOT NULL,
  `OffenceType` varchar(100) NOT NULL,
  `StartDate` date NOT NULL,
  `Status` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cell`
--

CREATE TABLE `cell` (
  `CellID` int(11) NOT NULL,
  `BlockID` int(11) NOT NULL,
  `CellNumber` varchar(20) NOT NULL,
  `Capacity` int(11) NOT NULL,
  `SecurityLevel` varchar(30) NOT NULL,
  `Status` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cell`
--

INSERT INTO `cell` (`CellID`, `BlockID`, `CellNumber`, `Capacity`, `SecurityLevel`, `Status`) VALUES
(1, 1, '129583', 2, 'Low', 'Occupied'),
(2, 2, '140247', 2, 'Low', 'Occupied'),
(3, 3, '129583', 20, 'Low', 'Empty');

-- --------------------------------------------------------

--
-- Table structure for table `convictedprisoner`
--

CREATE TABLE `convictedprisoner` (
  `PrisonerID` int(11) NOT NULL,
  `SentenceStartDate` date NOT NULL,
  `SentenceEndDate` date DEFAULT NULL,
  `ParoleEligibilityDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `duty_roster`
--

CREATE TABLE `duty_roster` (
  `RosterID` int(11) NOT NULL,
  `StaffID` int(11) NOT NULL,
  `BlockID` int(11) NOT NULL,
  `ShiftDate` date NOT NULL,
  `Shift` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guard`
--

CREATE TABLE `guard` (
  `StaffID` int(11) NOT NULL,
  `Rank` varchar(50) DEFAULT NULL,
  `AssignedShiftType` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicalrecord`
--

CREATE TABLE `medicalrecord` (
  `RecordID` int(11) NOT NULL,
  `PrisonerID` int(11) NOT NULL,
  `RecordedByUserID` int(11) NOT NULL,
  `RecordDate` date NOT NULL,
  `Diagnosis` varchar(150) NOT NULL,
  `Treatment` varchar(150) DEFAULT NULL,
  `Notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicalstaff`
--

CREATE TABLE `medicalstaff` (
  `StaffID` int(11) NOT NULL,
  `Specialty` varchar(50) DEFAULT NULL,
  `LicenseNo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_record`
--

CREATE TABLE `medical_record` (
  `RecordID` int(11) NOT NULL,
  `PrisonerID` int(11) NOT NULL,
  `StaffID` int(11) NOT NULL,
  `RecordDate` date NOT NULL,
  `Diagnosis` varchar(200) DEFAULT NULL,
  `Treatment` varchar(200) DEFAULT NULL,
  `Notes` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prison`
--

CREATE TABLE `prison` (
  `PrisonID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Location` varchar(150) NOT NULL,
  `TotalCapacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prison`
--

INSERT INTO `prison` (`PrisonID`, `Name`, `Location`, `TotalCapacity`) VALUES
(1, 'Matrichaya', 'H21', 4),
(2, 'BRACU', '9F23L', 40),
(3, 'MPH', 'H21', 30);

-- --------------------------------------------------------

--
-- Table structure for table `prisoner`
--

CREATE TABLE `prisoner` (
  `PrisonerID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `Gender` varchar(20) NOT NULL,
  `NationalID` varchar(30) NOT NULL,
  `AdmissionDate` date NOT NULL,
  `Status` varchar(30) NOT NULL,
  `SecurityLevel` varchar(30) NOT NULL,
  `CellID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prisoner`
--

INSERT INTO `prisoner` (`PrisonerID`, `FullName`, `DateOfBirth`, `Gender`, `NationalID`, `AdmissionDate`, `Status`, `SecurityLevel`, `CellID`) VALUES
(10, 'm', '2333-12-31', 'Male', '16518569515', '1123-12-31', 'Convicted', 'High', 2);

-- --------------------------------------------------------

--
-- Table structure for table `prisonertask`
--

CREATE TABLE `prisonertask` (
  `AssignmentID` int(11) NOT NULL,
  `PrisonerID` int(11) DEFAULT NULL,
  `TaskID` int(11) NOT NULL,
  `AssignedByUserID` int(11) NOT NULL,
  `AssignedDate` date NOT NULL,
  `Status` enum('Pending','InProgress','Completed') NOT NULL DEFAULT 'Pending',
  `Note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prisoner_case`
--

CREATE TABLE `prisoner_case` (
  `PrisonerID` int(11) NOT NULL,
  `CaseID` int(11) NOT NULL,
  `Role` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sentence`
--

CREATE TABLE `sentence` (
  `SentenceID` int(11) NOT NULL,
  `PrisonerID` int(11) NOT NULL,
  `CaseID` int(11) NOT NULL,
  `SentenceType` varchar(50) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date DEFAULT NULL,
  `Remarks` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `StaffID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Phone` varchar(30) DEFAULT NULL,
  `JoinDate` date NOT NULL,
  `Address` varchar(200) DEFAULT NULL,
  `BaseSalary` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `TaskID` int(11) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Priority` enum('Low','Medium','High') NOT NULL DEFAULT 'Medium',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`TaskID`, `Title`, `Description`, `Priority`, `CreatedAt`) VALUES
(1, 'Cleaning', 'Ghor Mocha', 'Medium', '2025-12-29 17:21:45');

-- --------------------------------------------------------

--
-- Table structure for table `undertrialprisoner`
--

CREATE TABLE `undertrialprisoner` (
  `PrisonerID` int(11) NOT NULL,
  `CourtName` varchar(100) NOT NULL,
  `NextHearingDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `FullName`, `Username`, `PasswordHash`, `Role`) VALUES
(1, 'System Admin', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN'),
(2, 'Orni', 'orni1234', '$2y$10$qyQ2KKhBGgNZFlvd7G7VsulfTwd.jvkhVXidmiXgDr.wgrXiYWUae', 'ADMIN');

-- --------------------------------------------------------

--
-- Table structure for table `visit`
--

CREATE TABLE `visit` (
  `VisitID` int(11) NOT NULL,
  `VisitorID` int(11) NOT NULL,
  `PrisonerID` int(11) NOT NULL,
  `VisitDateTime` datetime NOT NULL,
  `DurationMinutes` int(11) DEFAULT NULL,
  `ApprovedBy` int(11) NOT NULL,
  `Purpose` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visitor`
--

CREATE TABLE `visitor` (
  `VisitorID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Phone` varchar(30) DEFAULT NULL,
  `Address` varchar(200) DEFAULT NULL,
  `IDDocumentNo` varchar(50) NOT NULL,
  `Relation` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor`
--

INSERT INTO `visitor` (`VisitorID`, `FullName`, `Phone`, `Address`, `IDDocumentNo`, `Relation`) VALUES
(1, 'SHAHRIAR KABIR AYON', '01772989188', 'H 21, R 15, DIT Project, Merul Badda, Dhaka', '23301158', 'Lawyer'),
(2, 'KD', '01516500905', 'molla bari, moddho bongram', '23301154', 'Lawyer');

-- --------------------------------------------------------

--
-- Table structure for table `warden`
--

CREATE TABLE `warden` (
  `StaffID` int(11) NOT NULL,
  `ResponsibilityArea` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminstaff`
--
ALTER TABLE `adminstaff`
  ADD PRIMARY KEY (`StaffID`);

--
-- Indexes for table `block`
--
ALTER TABLE `block`
  ADD PRIMARY KEY (`BlockID`),
  ADD KEY `PrisonID` (`PrisonID`);

--
-- Indexes for table `case`
--
ALTER TABLE `case`
  ADD PRIMARY KEY (`CaseID`),
  ADD UNIQUE KEY `CaseNumber` (`CaseNumber`);

--
-- Indexes for table `cell`
--
ALTER TABLE `cell`
  ADD PRIMARY KEY (`CellID`),
  ADD KEY `BlockID` (`BlockID`);

--
-- Indexes for table `convictedprisoner`
--
ALTER TABLE `convictedprisoner`
  ADD PRIMARY KEY (`PrisonerID`);

--
-- Indexes for table `duty_roster`
--
ALTER TABLE `duty_roster`
  ADD PRIMARY KEY (`RosterID`),
  ADD KEY `StaffID` (`StaffID`),
  ADD KEY `BlockID` (`BlockID`);

--
-- Indexes for table `guard`
--
ALTER TABLE `guard`
  ADD PRIMARY KEY (`StaffID`);

--
-- Indexes for table `medicalrecord`
--
ALTER TABLE `medicalrecord`
  ADD PRIMARY KEY (`RecordID`),
  ADD KEY `PrisonerID` (`PrisonerID`),
  ADD KEY `RecordedByUserID` (`RecordedByUserID`);

--
-- Indexes for table `medicalstaff`
--
ALTER TABLE `medicalstaff`
  ADD PRIMARY KEY (`StaffID`),
  ADD UNIQUE KEY `LicenseNo` (`LicenseNo`);

--
-- Indexes for table `medical_record`
--
ALTER TABLE `medical_record`
  ADD PRIMARY KEY (`RecordID`),
  ADD KEY `PrisonerID` (`PrisonerID`),
  ADD KEY `StaffID` (`StaffID`);

--
-- Indexes for table `prison`
--
ALTER TABLE `prison`
  ADD PRIMARY KEY (`PrisonID`);

--
-- Indexes for table `prisoner`
--
ALTER TABLE `prisoner`
  ADD PRIMARY KEY (`PrisonerID`),
  ADD UNIQUE KEY `NationalID` (`NationalID`),
  ADD KEY `CellID` (`CellID`);

--
-- Indexes for table `prisonertask`
--
ALTER TABLE `prisonertask`
  ADD PRIMARY KEY (`AssignmentID`),
  ADD KEY `TaskID` (`TaskID`),
  ADD KEY `AssignedByUserID` (`AssignedByUserID`),
  ADD KEY `fk_PrisonerID` (`PrisonerID`);

--
-- Indexes for table `prisoner_case`
--
ALTER TABLE `prisoner_case`
  ADD PRIMARY KEY (`PrisonerID`,`CaseID`),
  ADD KEY `CaseID` (`CaseID`);

--
-- Indexes for table `sentence`
--
ALTER TABLE `sentence`
  ADD PRIMARY KEY (`SentenceID`),
  ADD KEY `PrisonerID` (`PrisonerID`),
  ADD KEY `CaseID` (`CaseID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`StaffID`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`TaskID`);

--
-- Indexes for table `undertrialprisoner`
--
ALTER TABLE `undertrialprisoner`
  ADD PRIMARY KEY (`PrisonerID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `visit`
--
ALTER TABLE `visit`
  ADD PRIMARY KEY (`VisitID`),
  ADD KEY `VisitorID` (`VisitorID`),
  ADD KEY `PrisonerID` (`PrisonerID`),
  ADD KEY `ApprovedBy` (`ApprovedBy`);

--
-- Indexes for table `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`VisitorID`),
  ADD UNIQUE KEY `IDDocumentNo` (`IDDocumentNo`);

--
-- Indexes for table `warden`
--
ALTER TABLE `warden`
  ADD PRIMARY KEY (`StaffID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `block`
--
ALTER TABLE `block`
  MODIFY `BlockID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `case`
--
ALTER TABLE `case`
  MODIFY `CaseID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cell`
--
ALTER TABLE `cell`
  MODIFY `CellID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `duty_roster`
--
ALTER TABLE `duty_roster`
  MODIFY `RosterID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicalrecord`
--
ALTER TABLE `medicalrecord`
  MODIFY `RecordID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_record`
--
ALTER TABLE `medical_record`
  MODIFY `RecordID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prison`
--
ALTER TABLE `prison`
  MODIFY `PrisonID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `prisoner`
--
ALTER TABLE `prisoner`
  MODIFY `PrisonerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `prisonertask`
--
ALTER TABLE `prisonertask`
  MODIFY `AssignmentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sentence`
--
ALTER TABLE `sentence`
  MODIFY `SentenceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `StaffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `TaskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `visit`
--
ALTER TABLE `visit`
  MODIFY `VisitID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visitor`
--
ALTER TABLE `visitor`
  MODIFY `VisitorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adminstaff`
--
ALTER TABLE `adminstaff`
  ADD CONSTRAINT `adminstaff_ibfk_1` FOREIGN KEY (`StaffID`) REFERENCES `staff` (`StaffID`);

--
-- Constraints for table `block`
--
ALTER TABLE `block`
  ADD CONSTRAINT `block_ibfk_1` FOREIGN KEY (`PrisonID`) REFERENCES `prison` (`PrisonID`);

--
-- Constraints for table `cell`
--
ALTER TABLE `cell`
  ADD CONSTRAINT `cell_ibfk_1` FOREIGN KEY (`BlockID`) REFERENCES `block` (`BlockID`);

--
-- Constraints for table `convictedprisoner`
--
ALTER TABLE `convictedprisoner`
  ADD CONSTRAINT `convictedprisoner_ibfk_1` FOREIGN KEY (`PrisonerID`) REFERENCES `prisoner` (`PrisonerID`);

--
-- Constraints for table `duty_roster`
--
ALTER TABLE `duty_roster`
  ADD CONSTRAINT `duty_roster_ibfk_1` FOREIGN KEY (`StaffID`) REFERENCES `staff` (`StaffID`),
  ADD CONSTRAINT `duty_roster_ibfk_2` FOREIGN KEY (`BlockID`) REFERENCES `block` (`BlockID`);

--
-- Constraints for table `guard`
--
ALTER TABLE `guard`
  ADD CONSTRAINT `guard_ibfk_1` FOREIGN KEY (`StaffID`) REFERENCES `staff` (`StaffID`);

--
-- Constraints for table `medicalrecord`
--
ALTER TABLE `medicalrecord`
  ADD CONSTRAINT `medicalrecord_ibfk_1` FOREIGN KEY (`PrisonerID`) REFERENCES `prisoner` (`PrisonerID`),
  ADD CONSTRAINT `medicalrecord_ibfk_2` FOREIGN KEY (`RecordedByUserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `medicalstaff`
--
ALTER TABLE `medicalstaff`
  ADD CONSTRAINT `medicalstaff_ibfk_1` FOREIGN KEY (`StaffID`) REFERENCES `staff` (`StaffID`);

--
-- Constraints for table `medical_record`
--
ALTER TABLE `medical_record`
  ADD CONSTRAINT `medical_record_ibfk_1` FOREIGN KEY (`PrisonerID`) REFERENCES `prisoner` (`PrisonerID`),
  ADD CONSTRAINT `medical_record_ibfk_2` FOREIGN KEY (`StaffID`) REFERENCES `staff` (`StaffID`);

--
-- Constraints for table `prisoner`
--
ALTER TABLE `prisoner`
  ADD CONSTRAINT `prisoner_ibfk_1` FOREIGN KEY (`CellID`) REFERENCES `cell` (`CellID`);

--
-- Constraints for table `prisonertask`
--
ALTER TABLE `prisonertask`
  ADD CONSTRAINT `fk_PrisonerID` FOREIGN KEY (`PrisonerID`) REFERENCES `prisoner` (`PrisonerID`),
  ADD CONSTRAINT `prisonertask_ibfk_1` FOREIGN KEY (`PrisonerID`) REFERENCES `prisoner` (`PrisonerID`),
  ADD CONSTRAINT `prisonertask_ibfk_2` FOREIGN KEY (`TaskID`) REFERENCES `task` (`TaskID`),
  ADD CONSTRAINT `prisonertask_ibfk_3` FOREIGN KEY (`AssignedByUserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `prisoner_case`
--
ALTER TABLE `prisoner_case`
  ADD CONSTRAINT `prisoner_case_ibfk_1` FOREIGN KEY (`PrisonerID`) REFERENCES `prisoner` (`PrisonerID`),
  ADD CONSTRAINT `prisoner_case_ibfk_2` FOREIGN KEY (`CaseID`) REFERENCES `case` (`CaseID`);

--
-- Constraints for table `sentence`
--
ALTER TABLE `sentence`
  ADD CONSTRAINT `sentence_ibfk_1` FOREIGN KEY (`PrisonerID`) REFERENCES `prisoner` (`PrisonerID`),
  ADD CONSTRAINT `sentence_ibfk_2` FOREIGN KEY (`CaseID`) REFERENCES `case` (`CaseID`);

--
-- Constraints for table `undertrialprisoner`
--
ALTER TABLE `undertrialprisoner`
  ADD CONSTRAINT `undertrialprisoner_ibfk_1` FOREIGN KEY (`PrisonerID`) REFERENCES `prisoner` (`PrisonerID`);

--
-- Constraints for table `visit`
--
ALTER TABLE `visit`
  ADD CONSTRAINT `visit_ibfk_1` FOREIGN KEY (`VisitorID`) REFERENCES `visitor` (`VisitorID`),
  ADD CONSTRAINT `visit_ibfk_2` FOREIGN KEY (`PrisonerID`) REFERENCES `prisoner` (`PrisonerID`),
  ADD CONSTRAINT `visit_ibfk_3` FOREIGN KEY (`ApprovedBy`) REFERENCES `staff` (`StaffID`);

--
-- Constraints for table `warden`
--
ALTER TABLE `warden`
  ADD CONSTRAINT `warden_ibfk_1` FOREIGN KEY (`StaffID`) REFERENCES `staff` (`StaffID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
