-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2024 at 03:03 PM
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
-- Database: `voting`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `candidate_id` int(50) NOT NULL,
  `election_id` int(11) DEFAULT NULL,
  `candidate_name` text NOT NULL,
  `password` varchar(200) NOT NULL,
  `candidate_number` varchar(20) NOT NULL,
  `department` text NOT NULL,
  `candidate_role` enum('Lecturer','Professor','Assistant_Professor','Associate_Professor') NOT NULL,
  `address` text NOT NULL,
  `socialmedia_links` text NOT NULL,
  `profile_pic` varchar(500) NOT NULL,
  `manifesto` text NOT NULL,
  `status` enum('Approved','Rejected','Pending','') NOT NULL,
  `document_status` enum('pending','verified','rejected','') NOT NULL DEFAULT 'pending',
  `symbol` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `candidate_email` varchar(200) NOT NULL,
  `has_voted` tinyint(1) DEFAULT 0,
  `role` enum('candidate','voter','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`candidate_id`, `election_id`, `candidate_name`, `password`, `candidate_number`, `department`, `candidate_role`, `address`, `socialmedia_links`, `profile_pic`, `manifesto`, `status`, `document_status`, `symbol`, `created_at`, `updated_at`, `candidate_email`, `has_voted`, `role`) VALUES
(65, NULL, 'Faheem Yaqoob', '$2y$10$.Klrbo.AWkE5pcpsPyHuxeN9xAeRuqughjpqn4RlFwtvLNz8xSdCi', '', 'cs', 'Lecturer', '', '', 'uploads/profile_pics/candidate_65.jpg', '', 'Approved', 'pending', '/uploads/symbols/671fef3ce972e_sword.jpeg', '2024-10-29 00:50:07', '2024-10-29 00:50:07', 'faheem@gmail.com', 0, 'candidate'),
(66, NULL, 'imran khan', '$2y$10$Mx1H9NuwVbdEFPD3ampjHOXCFZy2uO00.BEEcSRQfET/lD0WqNw7C', '', 'cs', 'Lecturer', '', '', 'uploads/profile_pics/candidate_66.jpg', '', 'Approved', 'pending', '/uploads/symbols/671fec1fd1814_how-to-draw-a-zebra.jpeg', '2024-10-29 00:52:08', '2024-10-29 00:52:08', 'imran@gmail.com', 0, 'candidate'),
(67, NULL, 'Kashif Khattak', '$2y$10$EAYovKhEBWxJxxlkOenR4.7KkM5q225JIEXZcypKVT2EokuwL3.MS', '', 'English', 'Lecturer', '', '', 'uploads/profile_pics/candidate_67.jpg', '', 'Approved', 'pending', '/uploads/symbols/671fef6bbe127_deer.jpg', '2024-10-29 01:09:15', '2024-10-29 01:09:15', 'kashif@gmail.com', 0, 'candidate'),
(68, NULL, 'Anam Naseer', '$2y$10$yzK.V9yoqPM9.mDARyqTa.b8//EbiKbSxuLDLkxM19Zyhq/ngD7vW', '', 'cs', 'Lecturer', '', '', 'uploads/profile_pics/candidate_68.jpg', '', 'Approved', 'pending', '/uploads/symbols/671fefaa696b0_bow.jpeg', '2024-10-29 01:10:18', '2024-10-29 01:10:18', 'Anam@gmail.com', 0, 'candidate'),
(69, NULL, 'Irfan Javaid', '$2y$10$qW/LvipBnCjEN8nwpmqBpuk5JFycWJm/1PH5Q4F95.jilUM92lpta', '', 'cs', 'Lecturer', '', '', '', '', 'Approved', 'pending', NULL, '2024-10-29 10:40:01', '2024-10-29 10:40:01', 'irfan@gmail.com', 0, 'candidate'),
(70, NULL, 'abdul sami', '$2y$10$7xEOlnRtC/Eep.bQ9uN47.kRBeYnRo5HFH/9KoHxP3d1hMIiAvWCa', '', 'English', 'Lecturer', '', '', '', '', 'Approved', 'pending', NULL, '2024-10-29 23:54:48', '2024-10-29 23:54:48', 'abdulsami123@gmail.com', 0, 'voter'),
(71, NULL, 'Rehan Khan', '$2y$10$XwfcN19a.Quld.wUWVRE6uwu4maiFEu4b.QhQMSlyi2RvX7KyvMYK', '', 'cs', 'Associate_Professor', '', '', '', '', 'Approved', 'pending', NULL, '2024-11-03 17:32:51', '2024-11-03 17:32:51', 'rehan123@gmail.com', 0, 'candidate'),
(72, NULL, 'Mooiz', '$2y$10$AWpq6p8HbgyLl8NL6JS.VupWBD9ClJrn3l191FRgDbKrpmQ6Ww0gW', '', 'cs', 'Lecturer', '', '', '', '', 'Approved', 'pending', NULL, '2024-11-03 17:38:59', '2024-11-03 17:38:59', 'Mooiz@gmail.com', 0, 'voter');

-- --------------------------------------------------------

--
-- Table structure for table `candidate_documents`
--

CREATE TABLE `candidate_documents` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `submission_date` date NOT NULL DEFAULT current_timestamp(),
  `verification_status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `verified_by` int(11) NOT NULL,
  `status` varchar(200) NOT NULL,
  `withdrawn` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidate_documents`
--

INSERT INTO `candidate_documents` (`id`, `candidate_id`, `election_id`, `document_path`, `submission_date`, `verification_status`, `verified_by`, `status`, `withdrawn`) VALUES
(40, 65, 47, '../uploads/documents/Assignment#01.pdf', '2024-11-03', 'verified', 0, '', 0),
(41, 68, 47, '../uploads/documents/Assignment#01.pdf', '2024-11-03', 'verified', 0, '', 0),
(42, 66, 47, '../uploads/documents/Assignment#01.pdf', '2024-11-03', 'verified', 0, '', 0),
(43, 67, 47, '../uploads/documents/Assignment#01.pdf', '2024-11-03', 'verified', 0, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `elections`
--

CREATE TABLE `elections` (
  `election_id` int(11) NOT NULL,
  `election_name` text NOT NULL,
  `election_date` date NOT NULL,
  `last_date_documents` date NOT NULL,
  `last_date_symbols` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('upcoming','inactive','active','completed') NOT NULL,
  `description` text NOT NULL,
  `role` enum('Professor','Lecturer','Assistant_Professor','Associate_Professor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `elections`
--

INSERT INTO `elections` (`election_id`, `election_name`, `election_date`, `last_date_documents`, `last_date_symbols`, `start_time`, `end_time`, `status`, `description`, `role`) VALUES
(47, 'senate Elections', '2024-11-15', '2024-11-03', '2024-11-05', '21:22:00', '17:25:00', 'completed', '', 'Lecturer');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','reviewed','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `candidate_id`, `feedback_text`, `date`, `status`) VALUES
(10, 68, 'Please improve your design . It is not very good at the moment', '2024-10-29 07:06:45', 'pending'),
(11, 65, 'Na samjh i ho kisi baat ki?', '2024-10-29 07:08:22', 'reviewed');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `noti_type` varchar(255) NOT NULL,
  `noti_seen` enum('seen','unseen','','') NOT NULL,
  `noti_date` datetime NOT NULL,
  `noti_message` varchar(300) NOT NULL,
  `noti_url` varchar(300) NOT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `user_role` enum('voter','candidate','admin','') NOT NULL,
  `noti_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `admin_id` int(11) NOT NULL,
  `Name` text NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`admin_id`, `Name`, `email`, `password`) VALUES
(1, 'Rehan Khan', 'rehan10crkt@gmail.com', '$2y$10$2ONjXxOxerCOWACWu.EAROsvYBnjZ2BPuva9GwMW3U4mEGILSqJhm'),
(3, 'Rehan Khan', 'rehankhan.upr@gmail.com', '$2y$10$bI1TTXlXJ7f9ys4x3.9MTONVcXOGln9RWAf2Tk3H0mG8iroC1lhPG');

-- --------------------------------------------------------

--
-- Table structure for table `voters`
--

CREATE TABLE `voters` (
  `voter_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `password` varchar(20) NOT NULL,
  `has_voted` tinyint(1) NOT NULL DEFAULT 0,
  `voted_for` text NOT NULL,
  `token` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voters`
--

INSERT INTO `voters` (`voter_id`, `name`, `email`, `password`, `has_voted`, `voted_for`, `token`) VALUES
(1, 'Rehan', 'rehan10crkt@gmail.com', 'badmash420', 0, '', '3b92cbda4322d982459133fdc08383a1'),
(2, 'sami', 'abdulsami.kh001@gmail.com', '$2y$10$zbe0PTL/sS1m0', 0, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `candidate_id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `total_votes` int(200) NOT NULL,
  `candidate_name` varchar(200) NOT NULL,
  `role` varchar(200) NOT NULL,
  `department` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`candidate_id`, `election_id`, `id`, `total_votes`, `candidate_name`, `role`, `department`) VALUES
(68, 47, 31, 2, '', '', ''),
(65, 47, 32, 1, '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `voting_history`
--

CREATE TABLE `voting_history` (
  `history_id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `voter_hash` varchar(255) NOT NULL,
  `election_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voting_history`
--

INSERT INTO `voting_history` (`history_id`, `election_id`, `id`, `candidate_id`, `voter_hash`, `election_date`) VALUES
(7, 47, 31, 68, '', '2024-11-03 12:57:13'),
(8, 47, 32, 65, '', '2024-11-03 12:57:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`candidate_id`),
  ADD KEY `e_id` (`election_id`);

--
-- Indexes for table `candidate_documents`
--
ALTER TABLE `candidate_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `candidate_id` (`candidate_id`),
  ADD KEY `electi_id` (`election_id`);

--
-- Indexes for table `elections`
--
ALTER TABLE `elections`
  ADD PRIMARY KEY (`election_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `c_id` (`candidate_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `voters`
--
ALTER TABLE `voters`
  ADD PRIMARY KEY (`voter_id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cand` (`candidate_id`),
  ADD KEY `elect_id` (`election_id`);

--
-- Indexes for table `voting_history`
--
ALTER TABLE `voting_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `ca_id` (`candidate_id`),
  ADD KEY `votes_id` (`id`),
  ADD KEY `election_id` (`election_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `candidate_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `candidate_documents`
--
ALTER TABLE `candidate_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `elections`
--
ALTER TABLE `elections`
  MODIFY `election_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `voters`
--
ALTER TABLE `voters`
  MODIFY `voter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `voting_history`
--
ALTER TABLE `voting_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `e_id` FOREIGN KEY (`election_id`) REFERENCES `elections` (`election_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `candidate_documents`
--
ALTER TABLE `candidate_documents`
  ADD CONSTRAINT `electi_id` FOREIGN KEY (`election_id`) REFERENCES `elections` (`election_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `candidate_id` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `c_id` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `cand` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `elect_id` FOREIGN KEY (`election_id`) REFERENCES `elections` (`election_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `voting_history`
--
ALTER TABLE `voting_history`
  ADD CONSTRAINT `ca_id` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `election_id` FOREIGN KEY (`election_id`) REFERENCES `elections` (`election_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `votes_id` FOREIGN KEY (`id`) REFERENCES `votes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
