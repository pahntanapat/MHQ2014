-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 
-- Server version: 5.1.68-community
-- PHP Version: 5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mahidol_quiz`
--
CREATE DATABASE IF NOT EXISTS `mahidol_quiz` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `mahidol_quiz`;

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `nickname` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `student_id` varchar(9) COLLATE utf8_unicode_ci NOT NULL COMMENT 'รหัสนักศึกษา',
  `phone` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'เบอร์โทร',
  `permission` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Binary value: 00000-11111, ให้ sorted_id, ตรวจหลักฐานโอนเงิน, ตรวจ quiz, แก้ไขผู้สมัคร, แก้ไข admin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='ข้อมูล admin';

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `new_account`
--

CREATE TABLE IF NOT EXISTS `new_account` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(32) CHARACTER SET utf8 NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '0 = ร.ร., 1 = อิสระ',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `confirm_code` varchar(32) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='account ใหม่ รอ confirm email';

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `notification`
--

CREATE TABLE IF NOT EXISTS `notification` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `detail` text COLLATE utf8_unicode_ci,
  `tag` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'ประเภทการเตือน',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='แจ้งเตือนการกระทำของ admin ให้ admin ทุกคนรู้';

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `quiz_ans`
--

CREATE TABLE IF NOT EXISTS `quiz_ans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint(20) unsigned NOT NULL COMMENT 'รหัสทีม (id จาก team_info)',
  `used_time` float NOT NULL DEFAULT '0' COMMENT 'เวลาที่ใช้ไป',
  `answer` text COLLATE utf8_unicode_ci COMMENT 'คำตอบ',
  `start_time` datetime NOT NULL,
  `sent_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'เวลาส่งคำตอบ',
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'สถานะยึดตามสถานะมาตรฐาน',
  `score` decimal(5,2) DEFAULT NULL COMMENT 'คะแนน',
  `comment` text COLLATE utf8_unicode_ci COMMENT 'comment กรรมการ (อ่านเอง)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_id` (`team_id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='คำตอบจากทีมอิสระ';

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `student_info`
--

CREATE TABLE IF NOT EXISTS `student_info` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint(20) unsigned NOT NULL COMMENT 'รหัสทีม (id จาก team_info)',
  `title` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'คำนำหน้าชื่อ',
  `firstname` text COLLATE utf8_unicode_ci NOT NULL,
  `lastname` text COLLATE utf8_unicode_ci NOT NULL,
  `gender` tinyint(1) NOT NULL COMMENT '1 = male, 0 = female',
  `phone` varchar(10) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `school` text COLLATE utf8_unicode_ci NOT NULL,
  `sci_grade` decimal(3,2) NOT NULL COMMENT 'เกรดวิทย์',
  `is_pass` tinyint(4) NOT NULL DEFAULT '2' COMMENT 'สถานะการกรอกข้อมูล',
  `is_upload` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'สถานะของปพ.1',
  `sorted_id` varchar(9) CHARACTER SET utf8 DEFAULT NULL COMMENT 'รหัสผู้แข่งขัน',
  `exam_room` text COLLATE utf8_unicode_ci COMMENT 'ห้องสอบ ที่นั่งสอบ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='ข้อมูลผู้สมัครรายคน';

--
-- MIME TYPES FOR TABLE `student_info`:
--   `exam_room`
--       `Text_Plain`
--

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `team_info`
--

CREATE TABLE IF NOT EXISTS `team_info` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(127) CHARACTER SET utf8 NOT NULL,
  `password` varchar(32) CHARACTER SET utf8 NOT NULL,
  `team_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'ชื่อทีม',
  `type` tinyint(1) NOT NULL COMMENT 'ประเภททีม, 0 = ร.ร., 1 = อิสระ',
  `t_firstname` text COLLATE utf8_unicode_ci COMMENT 'ชื่อครูที่ปรึกษา',
  `t_lastname` text COLLATE utf8_unicode_ci COMMENT 'นามสกุลครูที่ปรึกษา',
  `t_phone` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'เบอร์โทรครูที่ปรึกษา',
  `is_pass` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'สถานะข้อมูลทีม ตาม status มาตรฐาน',
  `is_pay` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'สถานะการจ่ายเงิน ตาม status มาตรฐาน',
  `sorted_id` varchar(9) CHARACTER SET utf8 DEFAULT NULL COMMENT 'รหัสทีมที่เรียงใหม่',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `email` (`email`,`team_name`),
  UNIQUE KEY `team_name` (`team_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='ข้อมูลผู้สมัครรายทีม';

--
-- MIME TYPES FOR TABLE `team_info`:
--   `email`
--       `Text_Plain`
--   `t_firstname`
--       `Text_Plain`
--   `t_fistname`
--       `Text_Plain`
--   `t_lastname`
--       `Text_Plain`
--   `t_phone`
--       `Text_Plain`
--

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `team_message`
--

CREATE TABLE IF NOT EXISTS `team_message` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint(20) unsigned NOT NULL,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `detail` text COLLATE utf8_unicode_ci,
  `sender_id` bigint(20) unsigned NOT NULL COMMENT 'คนประกาศ',
  `show_page` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'แสดงในหน้าไหนบ้าง',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='ข้อความที่ส่งให้แต่ละทีม';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
