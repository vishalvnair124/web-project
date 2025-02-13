-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2024 at 06:42 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `droplife`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_id` int(11) NOT NULL,
  `Admin_email` varchar(30) NOT NULL,
  `Admin_name` varchar(30) NOT NULL,
  `Admin_password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_id`, `Admin_email`, `Admin_name`, `Admin_password`) VALUES
(1, 'admin@gmail.com', 'vishal', '$2y$10$YkMBN6r2g4nDQfxWIA6A7eeEcrVQsLKyOA3la8.6cJsK7KpyTVRJS');

-- --------------------------------------------------------

--
-- Table structure for table `enquiry`
--

CREATE TABLE `enquiry` (
  `enquiry_id` int(11) NOT NULL,
  `enquirer_name` varchar(30) NOT NULL,
  `enquirer_email` varchar(30) NOT NULL,
  `enquirer_message` varchar(200) NOT NULL,
  `enquiry_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `enquiry_status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `enquiry`
--

INSERT INTO `enquiry` (`enquiry_id`, `enquirer_name`, `enquirer_email`, `enquirer_message`, `enquiry_time`, `enquiry_status`) VALUES
(1, 'VISHAL V NAIR', 'vishalvnair124@gmail.com', 'hello how are you....?', '2024-07-28 13:58:22', 0),
(2, 'VISHAL V NAIR', 'vishalvnair124@gmail.com', 'hai hello', '2024-07-28 13:58:28', 0),
(3, 'VISHAL V NAIR', 'vishalvnair124@gmail.com', 'how are you..?', '2024-07-28 16:41:32', 1),
(4, 'VISHAL V NAIR', 'vishalvnair124@gmail.com', 'jfegvjjjjj;HFEIUGHedhehadhohudhVBKJHNKJDVnskjhiuDHHJDJKHKDS\\\r\nFS\r\n[\'S\r\nF;SFB\r\nSF\\B\r\n];]L;;G\r\n\r\nSRBF\r\nB\r\nFKLLFSLLB\r\n;\'lk],.;\'nn\\n\\.bn\\k\'bn;\r\nF\r\n\'\r\nFFN\r\nLLF\r\n;A\r\nA', '2024-07-28 14:09:33', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `user_email` varchar(30) NOT NULL,
  `user_password` varchar(100) NOT NULL,
  `user_blood_group` varchar(10) NOT NULL,
  `user_gender` varchar(10) NOT NULL,
  `user_latitude` float NOT NULL,
  `user_longitude` float NOT NULL,
  `user_ready_donate` int(11) NOT NULL,
  `user_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_blood_group`, `user_gender`, `user_latitude`, `user_longitude`, `user_ready_donate`, `user_status`) VALUES
(1, 'VISHAL V NAIR ', 'vishalvnair124@gmail.com', '$2y$10$CgmsWx5stCX0/FQLhUSDX.T03ESZqlXH1WJf0Nfz8sA4GSnqLt6fG', 'unknown', 'unspecifie', 0, 0, 0, 1),
(2, 'VISHAL V NAIR ', 'vishalvnair0124@gmail.com', '$2y$10$vtnlNCldZjoVkaJUsSPCfOviuAXSmpX7XMPDYfXPI.0IEySvGz/.u', 'unknown', 'unspecifie', 0, 0, 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_id`);

--
-- Indexes for table `enquiry`
--
ALTER TABLE `enquiry`
  ADD PRIMARY KEY (`enquiry_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enquiry`
--
ALTER TABLE `enquiry`
  MODIFY `enquiry_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
