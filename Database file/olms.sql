-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2023 at 07:46 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `olms`
--

-- --------------------------------------------------------

--
-- Table structure for table `olms_books`
--

CREATE TABLE `olms_books` (
  `book_sl_no` int(11) NOT NULL,
  `book_id` varchar(255) NOT NULL,
  `book_nme` varchar(255) NOT NULL,
  `book_auth` varchar(255) NOT NULL,
  `book_pub` varchar(255) NOT NULL,
  `book_genre` varchar(255) NOT NULL,
  `book_stock` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `olms_books`
--

INSERT INTO `olms_books` (`book_sl_no`, `book_id`, `book_nme`, `book_auth`, `book_pub`, `book_genre`, `book_stock`) VALUES
(1, 'B1', 'Rich dad poor dad', 'Robert kiyosaki', '‎Plata Publishing', 'Non-Fiction', 10),
(2, 'B2', 'The Shadow of serenity', 'Emily harper', 'Horizon books', 'Mystery & Thriller', 15),
(3, 'B3', 'Ephemeral echoes', 'Benjamin Steele', 'Luminous Press', 'Science Fiction & Fantasy', 20),
(4, 'B4', 'Whispers in the Mist', 'Victoria Rivers', 'Enigma Publishing', 'Business', 30),
(5, 'B5', 'Beyond the veil', 'Olivia Wells', 'Celestials Books', 'Romance', 40),
(6, 'B6', 'Serpentine Secrets', 'Cassandra Black', 'Midnight Books', 'Mystery & Thriller', 12),
(7, 'B7', 'Astral Odyssey', 'Xavier Orion', 'Cosmos Publications', 'Science', 15),
(12, 'B8', 'To kill a mockingbird', 'Harper Lee', 'J.B. Lippincott & Co.', 'Fiction', 13),
(13, 'B9', '1984', 'George Orwell', 'Secker & Warburg', 'Fiction', 19),
(14, 'B10', 'Pride and prejudice', 'Jane austen', 'T. Egerton, Whitehall', 'Romance', 6),
(15, 'B11', 'The Great gatsby', 'F. Scott Fitzgerald', 'Charles Scribner\'s Son', 'Fiction', 21),
(16, 'B12', 'One hundred Years of solitude', 'Gabriel Garcia Marquez', 'Editorial Sudamericana', 'Science Fiction & Fantasy', 5),
(17, 'B13', 'The catcher in the rye', 'J.D. Salinger', 'Allen & Unwin', 'Fiction', 12),
(18, 'B14', 'The lord of the rings', 'J.R.R. Tolkein', 'Little, brown and company', 'Science Fiction & Fantasy', 14);

-- --------------------------------------------------------

--
-- Table structure for table `olms_issued`
--

CREATE TABLE `olms_issued` (
  `issue_no` int(11) NOT NULL,
  `issued_book_no` varchar(255) NOT NULL,
  `issued_to` varchar(255) NOT NULL,
  `issued_by` varchar(255) NOT NULL,
  `issued_on` varchar(10) NOT NULL,
  `issued_upto` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `olms_issue_requests`
--

CREATE TABLE `olms_issue_requests` (
  `request_no` int(11) NOT NULL,
  `request_id` varchar(255) NOT NULL,
  `requested_by` varchar(255) NOT NULL,
  `requested_book_id` varchar(255) NOT NULL,
  `requested_book_nme` varchar(255) NOT NULL,
  `requested_on` varchar(10) NOT NULL,
  `requested_by_uname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `olms_librarian`
--

CREATE TABLE `olms_librarian` (
  `lib_no` int(11) NOT NULL,
  `lib_id` varchar(255) NOT NULL,
  `lib_nme` varchar(255) NOT NULL,
  `lib_phn` varchar(20) NOT NULL,
  `lib_mail` varchar(255) NOT NULL,
  `lib_addr` varchar(255) NOT NULL,
  `reg_date` varchar(255) NOT NULL,
  `img_path` varchar(255) NOT NULL,
  `lib_uname` varchar(255) NOT NULL,
  `lib_pword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `olms_librarian`
--

INSERT INTO `olms_librarian` (`lib_no`, `lib_id`, `lib_nme`, `lib_phn`, `lib_mail`, `lib_addr`, `reg_date`, `img_path`, `lib_uname`, `lib_pword`) VALUES
(9, 'L1', 'Ankan Chakraborty', '9932749416', 'ankandgp1@outlook.com', 'Durgapur', '29-11-2023', 'profile_pics/Librarians/WIN_20230310_17_37_29_Pro.jpg', 'ankan', '$2y$10$J1XwWiO5J3UfzpHM5mpUsu1Hgh8TYSbDCBEy9J9JiQ83XZE5JQx1m');

-- --------------------------------------------------------

--
-- Table structure for table `olms_members`
--

CREATE TABLE `olms_members` (
  `mem_no` int(11) NOT NULL,
  `mem_id` varchar(255) NOT NULL,
  `mem_nme` varchar(255) NOT NULL,
  `mem_phn` varchar(20) NOT NULL,
  `mem_mail` varchar(255) NOT NULL,
  `mem_addr` varchar(255) NOT NULL,
  `reg_date` varchar(255) NOT NULL,
  `img_path` varchar(255) NOT NULL,
  `mem_uname` varchar(255) NOT NULL,
  `mem_pword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `olms_members`
--

INSERT INTO `olms_members` (`mem_no`, `mem_id`, `mem_nme`, `mem_phn`, `mem_mail`, `mem_addr`, `reg_date`, `img_path`, `mem_uname`, `mem_pword`) VALUES
(8, 'M1', 'Ankan Chakraborty', '9932749416', 'ankang670@gmail.com', 'Durgapur', '29-11-2023', 'profile_pics/Members/WIN_20230310_17_37_29_Pro.jpg', 'ankan', '$2y$10$0Rlqm8Odw0U.9EHlqUjfNuaJ3i/niMeSLqPJXOUT8PQgT874taSu6');

-- --------------------------------------------------------

--
-- Table structure for table `olms_messages`
--

CREATE TABLE `olms_messages` (
  `msg_no` int(11) NOT NULL,
  `msg_nme` varchar(255) NOT NULL,
  `msg_mail` varchar(255) NOT NULL,
  `msg_message` varchar(255) NOT NULL,
  `msg_time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `olms_messages`
--

INSERT INTO `olms_messages` (`msg_no`, `msg_nme`, `msg_mail`, `msg_message`, `msg_time`) VALUES
(1, 'Carl johnson', 'carl@carl.com', 'Hello, this is Carl.', '11-11-2023 01:02:55pm'),
(2, 'Martin Zolo', 'martin@zolo.com', 'Hello, this is Martin.', '11-11-2023 01:03:18pm');

-- --------------------------------------------------------

--
-- Table structure for table `olms_transactions`
--

CREATE TABLE `olms_transactions` (
  `transaction_no` int(11) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `transaction_type` varchar(6) NOT NULL,
  `transaction_date` varchar(10) NOT NULL,
  `book_id` varchar(10) NOT NULL,
  `book_name` varchar(255) NOT NULL,
  `member_id` varchar(10) NOT NULL,
  `member_name` varchar(255) NOT NULL,
  `librarian_id` varchar(10) NOT NULL,
  `librarian_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `olms_books`
--
ALTER TABLE `olms_books`
  ADD PRIMARY KEY (`book_sl_no`),
  ADD UNIQUE KEY `book_id` (`book_id`);

--
-- Indexes for table `olms_issued`
--
ALTER TABLE `olms_issued`
  ADD PRIMARY KEY (`issue_no`);

--
-- Indexes for table `olms_issue_requests`
--
ALTER TABLE `olms_issue_requests`
  ADD PRIMARY KEY (`request_no`);

--
-- Indexes for table `olms_librarian`
--
ALTER TABLE `olms_librarian`
  ADD PRIMARY KEY (`lib_no`),
  ADD UNIQUE KEY `lib_mail` (`lib_mail`),
  ADD UNIQUE KEY `lib_uname` (`lib_uname`);

--
-- Indexes for table `olms_members`
--
ALTER TABLE `olms_members`
  ADD PRIMARY KEY (`mem_no`),
  ADD UNIQUE KEY `mem_uname` (`mem_uname`),
  ADD UNIQUE KEY `mem_mail` (`mem_mail`);

--
-- Indexes for table `olms_messages`
--
ALTER TABLE `olms_messages`
  ADD PRIMARY KEY (`msg_no`);

--
-- Indexes for table `olms_transactions`
--
ALTER TABLE `olms_transactions`
  ADD PRIMARY KEY (`transaction_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `olms_books`
--
ALTER TABLE `olms_books`
  MODIFY `book_sl_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `olms_issued`
--
ALTER TABLE `olms_issued`
  MODIFY `issue_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `olms_issue_requests`
--
ALTER TABLE `olms_issue_requests`
  MODIFY `request_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `olms_librarian`
--
ALTER TABLE `olms_librarian`
  MODIFY `lib_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `olms_members`
--
ALTER TABLE `olms_members`
  MODIFY `mem_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `olms_messages`
--
ALTER TABLE `olms_messages`
  MODIFY `msg_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `olms_transactions`
--
ALTER TABLE `olms_transactions`
  MODIFY `transaction_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
