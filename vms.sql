-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 27, 2021 at 03:55 PM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vms`
--
CREATE DATABASE IF NOT EXISTS `vms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `vms`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `get_customer_data`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_customer_data` (IN `booking_id` INT)  SELECT * FROM `booking` AS b INNER JOIN customer AS c ON b.dl_number = c.dl_number INNER JOIN vehicle AS v ON b.reg_number = v.reg_number WHERE b.booking_id = booking_id AND b.is_paid = 0 LIMIT 1$$

DROP PROCEDURE IF EXISTS `get_late_fee`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_late_fee` (IN `booking_id` INT)  BEGIN
SET @lfh =(SELECT late_fee_per_hour  FROM v_category AS vc INNER JOIN vehicle AS v ON vc.category_name = v.category_name INNER JOIN booking AS b ON v.reg_number= b.reg_number WHERE b.booking_id = booking_id LIMIT 1);

UPDATE booking SET booking.late_fee = @lfh
WHERE booking.booking_id = booking_id;

END$$

DROP PROCEDURE IF EXISTS `new_book`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `new_book` (IN `book_dt` DATETIME, IN `return_dt` DATETIME, IN `amount` FLOAT, IN `reg_number` VARCHAR(7), IN `dl_number` VARCHAR(15))  BEGIN
INSERT INTO booking
(booking.book_dt,booking.return_dt,booking.amount,booking.reg_number,booking.dl_number)
VALUES (book_dt,return_dt,amount,reg_number,dl_number);

UPDATE vehicle AS v SET v.is_available = 0 WHERE v.reg_number = reg_number;

END$$

DROP PROCEDURE IF EXISTS `update_bill`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_bill` (IN `bill_date` DATETIME, IN `book_price` FLOAT, IN `total_late_fee` FLOAT, IN `total` FLOAT, IN `booking_id` INT(11), IN `emp_id` INT(11), IN `reg_number` VARCHAR(7))  BEGIN
INSERT INTO bill
(bill.bill_date,bill.book_price,bill.total_late_fee,bill.total,bill.booking_id,bill.emp_id)
VALUES
(bill_date,book_price,total_late_fee,total,booking_id,emp_id);

UPDATE booking AS b SET b.is_paid = 1 WHERE b.booking_id = booking_id;

UPDATE vehicle AS v SET v.is_available = 1 WHERE v.reg_number = reg_number;
END$$

DROP PROCEDURE IF EXISTS `update_last_login`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_last_login` (IN `dl_number` VARCHAR(15), IN `last_login` DATETIME)  UPDATE customer SET customer.last_login = last_login WHERE customer.dl_number = dl_number$$

DROP PROCEDURE IF EXISTS `update_last_login_admin`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_last_login_admin` (IN `user_id` INT, IN `last_login` DATETIME)  UPDATE employer SET employer.last_login = last_login WHERE employer.user_id = user_id$$

DROP PROCEDURE IF EXISTS `update_late_fee`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_late_fee` (IN `booking_id` INT)  BEGIN
/*get late fee per hour from v_category table */
SET @lfh =(SELECT late_fee_per_hour  
           FROM v_category AS vc 
           INNER JOIN vehicle AS v ON vc.category_name = v.category_name 
           INNER JOIN booking AS b ON v.reg_number= b.reg_number 
           WHERE b.booking_id = booking_id 
           LIMIT 1);
/*get return date & time from booking table */
SET @return_dt = (SELECT return_dt 
                  FROM booking 
                  WHERE booking.booking_id = booking_id 
                  LIMIT 1);
/* set current date & time */
SET @current_dt = (CURRENT_TIMESTAMP);
/*check current d&t and return d&t */
SET @hourDifference = TIMEDIFF(@current_dt , @return_dt);
IF @hourDifference >0 THEN           
    SET @totalLateFee = @hourDifference * @lfh;
ELSE
    SET @totalLateFee = 0;
END IF;
/* update late fee value to booking table */
UPDATE booking 
SET late_fee = @totalLateFee
WHERE booking.booking_id = booking_id;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bill`
--
-- Creation: Dec 19, 2021 at 06:24 AM
--

DROP TABLE IF EXISTS `bill`;
CREATE TABLE IF NOT EXISTS `bill` (
  `bill_id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_date` datetime NOT NULL DEFAULT current_timestamp(),
  `book_price` float NOT NULL,
  `total_late_fee` float NOT NULL,
  `total` float NOT NULL,
  `booking_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  PRIMARY KEY (`bill_id`),
  KEY `booking_id` (`booking_id`),
  KEY `emp_id` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `bill`:
--   `booking_id`
--       `booking` -> `booking_id`
--   `emp_id`
--       `employer` -> `user_id`
--

--
-- Dumping data for table `bill`
--

INSERT INTO `bill` (`bill_id`, `bill_date`, `book_price`, `total_late_fee`, `total`, `booking_id`, `emp_id`) VALUES
(2, '2021-11-21 21:58:55', 6000, 754200, 760200, 6, 1),
(4, '2021-11-23 21:56:48', 57000, 389880, 446880, 7, 1),
(7, '2021-12-02 11:12:12', 14000, 329700, 343700, 8, 1),
(8, '2021-12-19 11:58:04', 22400, 0, 22400, 9, 1);

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--
-- Creation: Dec 19, 2021 at 06:24 AM
--

DROP TABLE IF EXISTS `booking`;
CREATE TABLE IF NOT EXISTS `booking` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `book_dt` datetime NOT NULL,
  `return_dt` datetime NOT NULL,
  `amount` float NOT NULL,
  `reg_number` varchar(7) NOT NULL,
  `dl_number` varchar(15) NOT NULL,
  `is_paid` tinyint(1) DEFAULT 0,
  `late_fee` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`booking_id`),
  KEY `reg_number` (`reg_number`),
  KEY `dl_number` (`dl_number`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `booking`:
--   `reg_number`
--       `vehicle` -> `reg_number`
--   `dl_number`
--       `customer` -> `dl_number`
--

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `book_dt`, `return_dt`, `amount`, `reg_number`, `dl_number`, `is_paid`, `late_fee`) VALUES
(6, '2021-10-04 20:23:00', '2021-10-06 20:23:00', 6000, 'ABB3245', 'F9764521', 1, 754200),
(7, '2021-10-09 15:08:00', '2021-11-09 15:08:00', 57000, 'TRE9726', 'F2345611', 1, 955320),
(8, '2021-11-23 21:58:00', '2021-11-25 21:58:00', 14000, 'PJ9296', 'F9764521', 1, 1402800),
(9, '2021-12-19 11:57:00', '2021-12-26 12:00:00', 22400, 'CAT9356', 'F2345611', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--
-- Creation: Dec 19, 2021 at 06:24 AM
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `dl_number` varchar(15) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(12) NOT NULL,
  `hashed_password` varchar(40) NOT NULL,
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`dl_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `customer`:
--

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`dl_number`, `first_name`, `last_name`, `email`, `phone_number`, `hashed_password`, `last_login`) VALUES
('F2345611', 'CHAMARA', 'MADUSANKA', 'chamara@gmail.com', '+94818918754', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', '2021-12-23 18:27:38'),
('F9764521', 'KAVISHKA', 'VIDUSHAN', 'kavishka@gmail.com', '+94469647859', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', '2021-12-02 11:24:56');

-- --------------------------------------------------------

--
-- Table structure for table `employer`
--
-- Creation: Dec 19, 2021 at 06:24 AM
--

DROP TABLE IF EXISTS `employer`;
CREATE TABLE IF NOT EXISTS `employer` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `dl_number` varchar(15) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(12) NOT NULL,
  `hashed_password` varchar(40) NOT NULL,
  `last_login` datetime NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `employer`:
--

--
-- Dumping data for table `employer`
--

INSERT INTO `employer` (`user_id`, `dl_number`, `first_name`, `last_name`, `email`, `phone_number`, `hashed_password`, `last_login`, `is_admin`) VALUES
(1, 'P1234567', 'KAVINDU', 'SANKALPA', 'kavindu@gmail.com', '+94990876251', 'd033e22ae348aeb5660fc2140aec35850c4da997', '2021-12-23 18:28:04', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--
-- Creation: Dec 19, 2021 at 06:24 AM
--

DROP TABLE IF EXISTS `vehicle`;
CREATE TABLE IF NOT EXISTS `vehicle` (
  `reg_number` varchar(7) NOT NULL,
  `model_name` varchar(25) NOT NULL,
  `make` varchar(25) NOT NULL,
  `model_year` year(4) NOT NULL,
  `traveled_distance` float NOT NULL,
  `owner_id` int(11) NOT NULL,
  `category_name` varchar(25) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`reg_number`),
  KEY `owner_id` (`owner_id`),
  KEY `category_name` (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `vehicle`:
--   `owner_id`
--       `employer` -> `user_id`
--   `category_name`
--       `v_category` -> `category_name`
--

--
-- Dumping data for table `vehicle`
--

INSERT INTO `vehicle` (`reg_number`, `model_name`, `make`, `model_year`, `traveled_distance`, `owner_id`, `category_name`, `is_available`) VALUES
('AAA4567', 'FIESTA', 'FORD', 2015, 15000, 1, 'ECONOMY', 1),
('ABB3245', 'ACCENT', 'HYUNDAI', 2014, 12350, 1, 'ECONOMY', 1),
('ABX1234', 'CIVIC', 'HONDA', 2014, 10000, 1, 'ECONOMY', 1),
('CAR2376', 'COROLLA', 'TOYOTA', 2016, 5000, 1, 'ECONOMY', 1),
('CAT9356', 'FOCUS', 'FORD', 2016, 10090, 1, 'COMPACT', 1),
('CCC4620', 'LEGACY', 'SUBARU', 2012, 20000, 1, 'MID SIZE', 1),
('HJK1234', 'CIVIC', 'HONDA', 2015, 10000, 1, 'ECONOMY', 1),
('MNB8654', 'FALCON', 'FORD', 2012, 10900, 1, 'FULL SIZE', 1),
('PJ9296', 'ODYSSEY', 'HONDA', 2016, 5000, 1, 'MINI VAN', 1),
('TRE9726', '200', 'CHRYSTLER', 2012, 14000, 1, 'STANDARD', 1);

-- --------------------------------------------------------

--
-- Table structure for table `v_category`
--
-- Creation: Dec 19, 2021 at 06:24 AM
--

DROP TABLE IF EXISTS `v_category`;
CREATE TABLE IF NOT EXISTS `v_category` (
  `category_name` varchar(25) NOT NULL,
  `no_of_person` int(11) NOT NULL,
  `cost_per_day` float NOT NULL,
  `cost_per_month` float NOT NULL,
  `late_fee_per_hour` float NOT NULL,
  PRIMARY KEY (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `v_category`:
--

--
-- Dumping data for table `v_category`
--

INSERT INTO `v_category` (`category_name`, `no_of_person`, `cost_per_day`, `cost_per_month`, `late_fee_per_hour`) VALUES
('COMPACT', 5, 3200, 48000, 960),
('ECONOMY', 5, 3000, 45000, 900),
('FULL SIZE', 5, 4000, 60000, 1200),
('FULL SIZE SUV', 8, 6000, 90000, 1800),
('LUXURY CAR', 5, 7500, 112500, 2250),
('MID SIZE', 5, 3500, 52500, 1050),
('MID SUZE SUV', 5, 3600, 54000, 1080),
('MINI VAN', 7, 7000, 105000, 2100),
('STANDARD', 5, 3800, 57000, 1140),
('STANDARD SUV', 5, 4000, 60000, 1200);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`),
  ADD CONSTRAINT `bill_ibfk_2` FOREIGN KEY (`emp_id`) REFERENCES `employer` (`user_id`);

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`reg_number`) REFERENCES `vehicle` (`reg_number`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`dl_number`) REFERENCES `customer` (`dl_number`);

--
-- Constraints for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD CONSTRAINT `vehicle_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `employer` (`user_id`),
  ADD CONSTRAINT `vehicle_ibfk_2` FOREIGN KEY (`category_name`) REFERENCES `v_category` (`category_name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
