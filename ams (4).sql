-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 27, 2013 at 06:58 PM
-- Server version: 5.5.29
-- PHP Version: 5.3.10-1ubuntu3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ams`
--

-- --------------------------------------------------------

--
-- Table structure for table `ams_allocation`
--

CREATE TABLE IF NOT EXISTS `ams_allocation` (
  `allocation_id` int(11) NOT NULL AUTO_INCREMENT,
  `allocation_fk_user_id` int(11) NOT NULL,
  `allocation_fk_dept_id` int(11) NOT NULL,
  `allocation_fk_asset_id` int(11) NOT NULL,
  `allocation_remarks` text NOT NULL,
  `allocation_date` datetime NOT NULL,
  PRIMARY KEY (`allocation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `ams_allocation`
--

INSERT INTO `ams_allocation` (`allocation_id`, `allocation_fk_user_id`, `allocation_fk_dept_id`, `allocation_fk_asset_id`, `allocation_remarks`, `allocation_date`) VALUES
(2, 5, 1, 36, '0', '2013-03-22 13:21:27'),
(3, 2, 1, 35, '0', '2013-03-24 14:43:51'),
(4, 0, 1, 34, '0', '2013-03-22 21:43:43'),
(5, 0, 1, 33, '0', '2013-03-22 21:43:51'),
(6, 0, 1, 32, '0', '2013-03-24 21:52:08'),
(7, 0, 1, 29, '0', '2013-03-24 22:50:27'),
(8, 0, 1, 40, '0', '2013-03-24 22:50:46'),
(9, 0, 1, 45, '0', '2013-03-24 23:07:07'),
(10, 0, 1, 45, '0', '2013-03-24 23:08:47'),
(11, 0, 1, 45, '0', '2013-03-24 23:09:04'),
(12, 0, 1, 45, '0', '2013-03-25 19:59:19');

-- --------------------------------------------------------

--
-- Table structure for table `ams_asset`
--

CREATE TABLE IF NOT EXISTS `ams_asset` (
  `asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_tag_id` varchar(45) NOT NULL,
  `asset_model` varchar(100) NOT NULL,
  `asset_fk_cat_id` int(11) NOT NULL,
  `asset_supply_date` date NOT NULL,
  `asset_cost` decimal(10,2) NOT NULL,
  `asset_state` tinyint(1) NOT NULL,
  `asset_meta_field_data` text NOT NULL,
  `asset_add_date` datetime NOT NULL,
  PRIMARY KEY (`asset_id`),
  KEY `cat_id_idx` (`asset_fk_cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=46 ;

--
-- Dumping data for table `ams_asset`
--

INSERT INTO `ams_asset` (`asset_id`, `asset_tag_id`, `asset_model`, `asset_fk_cat_id`, `asset_supply_date`, `asset_cost`, `asset_state`, `asset_meta_field_data`, `asset_add_date`) VALUES
(1, 'KU/CIT/1111/01111', 'Server', 0, '2013-03-04', 860000.00, 0, '', '0000-00-00 00:00:00'),
(2, 'KU/CIT/0/23/234', 'lenovo t60', 1, '0000-00-00', 60000.00, 0, '', '0000-00-00 00:00:00'),
(3, 'KU/CIT/0/23/234', 'lenovo t60', 1, '0000-00-00', 50000.00, 0, '', '0000-00-00 00:00:00'),
(4, 'KU/CIT/0/23/234', 'lenovo ', 1, '0000-00-00', 50000.00, 0, '', '0000-00-00 00:00:00'),
(5, 'KU/CIT/0/23/234', 'lenovo t60', 1, '2013-02-21', 50000.00, 0, '', '0000-00-00 00:00:00'),
(9, 'KU/REG/34/231', 'Brown wooden Table', 2, '2012-02-07', 2500.00, 0, '', '0000-00-00 00:00:00'),
(10, 'KU/SET/34/3423', 'Samsung Note II mobile handset', 1, '2013-02-08', 56000.00, 0, '', '0000-00-00 00:00:00'),
(11, 'KU/CIT/1/34/2344', 'Sony Projector', 4, '2013-02-27', 52000.00, 0, '', '0000-00-00 00:00:00'),
(12, 'KU/ED/324/231/1', 'Steel Cabinet', 2, '2013-02-06', 1000.00, -1, '', '0000-00-00 00:00:00'),
(13, 'KU/REG/2/45/3435', 'Table', 2, '2013-02-28', 76.00, 0, '', '0000-00-00 00:00:00'),
(15, 'ku/CIT/2324/2131/23', 'Laptop Dell 23', 1, '2013-02-20', 56400.00, 0, '', '0000-00-00 00:00:00'),
(18, 'KU/CIT/34/6786/4354', 'Table', 2, '2013-02-21', 5000.00, 0, '', '0000-00-00 00:00:00'),
(19, 'Ku/CIT/67/567/65767/56', 'Chair', 2, '2013-02-21', 5400.00, 0, '', '0000-00-00 00:00:00'),
(20, 'KU/CIT/67/45/32/55665', 'TAble', 2, '2013-02-15', 4000.00, 0, '', '0000-00-00 00:00:00'),
(22, 'KU/CIT/34/34/32/2', 'Desktop Dell ', 1, '2013-02-28', 54000.00, 0, '', '0000-00-00 00:00:00'),
(23, 'KU/CIT/0/23/23', 'Table', 2, '2013-03-07', 4000.00, 0, '', '0000-00-00 00:00:00'),
(24, 'KU/REG/34/2456/67', 'Server 112', 1, '2013-02-20', 540200.00, 0, '', '0000-00-00 00:00:00'),
(26, 'KU/HT/6778/886', 'Chair', 2, '2013-03-26', 5100.00, 0, '', '0000-00-00 00:00:00'),
(27, 'KU/CIT/34/2/32', 'Uninterruptible Power Supply', 1, '2010-01-02', 25000.00, 2, '', '0000-00-00 00:00:00'),
(28, 'KU/CIT/56/765/23/22/1', 'Samsung Note I', 1, '2011-03-09', 60000.00, 0, '', '2013-03-18 21:51:00'),
(29, 'KU/CIT/56/765/23/22/1', 'Samsung Note I', 1, '2011-03-09', 60000.00, 0, '', '2013-03-18 21:51:39'),
(30, 'KU/CIt/23/5/3/2', 'Samsung Note II mobile handset', 1, '2012-03-08', 45000.00, 0, '', '2013-03-18 21:53:07'),
(32, 'KU/CIT/45/23/232', 'Wooden Shelf', 2, '2011-03-15', 4500.00, 1, '', '2013-03-18 21:57:22'),
(33, 'KU/REG/34/12/122', 'Server 2013', 1, '2013-03-20', 450000.00, 1, '', '2013-03-19 13:40:56'),
(34, 'KU/CIT/4/32/32', 'CHAIR', 2, '2013-03-07', 3400.00, 1, '[{"attrib_name":"Color","attrib_type":"text","attrib_required":0,"attrib_config":[""],"attrib_value":"Brown"},{"attrib_name":"Number of legs","attrib_type":"number","attrib_required":0,"attrib_config":[""],"attrib_value":"8"}]', '2013-03-20 12:55:50'),
(35, 'KU/CIT/4/32/32', 'CHAIR', 2, '2013-03-07', 3400.00, 1, '[{"attrib_name":"Color","attrib_type":"text","attrib_required":0,"attrib_config":[""],"attrib_value":"Brown"},{"attrib_name":"Number of legs","attrib_type":"number","attrib_required":0,"attrib_config":[""],"attrib_value":"8"}]', '2013-03-20 12:57:32'),
(36, 'KU/CIT/4/32/32', 'CHAIR', 2, '2013-03-07', 3400.00, 1, '', '2013-03-20 13:02:22'),
(37, 'KU/CIT/89/232', 'Generator', 7, '2013-03-13', 20000.00, 0, '', '2013-03-24 00:44:08'),
(38, 'KU/CIT/453/323', 'Eve Machines', 7, '2013-03-22', 500000.00, 1, '', '2013-03-24 10:50:41'),
(39, 'KU/CIT/34/435/101', 'Dell Laptops', 1, '2013-03-06', 26000.00, 0, '[]', '2013-03-24 16:28:35'),
(40, 'KU/CIT/34/435/102', 'Dell Laptops', 1, '2013-03-06', 26000.00, 0, '[]', '2013-03-24 16:28:36'),
(41, 'KU/CIT/34/435/103', 'Dell Laptops', 1, '2013-03-06', 26000.00, 0, '[]', '2013-03-24 16:28:36'),
(42, 'KU/REG/2/133/301', 'Boardroom chairs', 1, '2013-03-26', 34000.00, 0, '[]', '2013-03-24 16:32:11'),
(43, 'KU/REG/2/133/302', 'Boardroom chairs', 1, '2013-03-26', 34000.00, 0, '[]', '2013-03-24 16:32:11'),
(44, 'KU/REG/2/133/303', 'Boardroom chairs', 1, '2013-03-26', 34000.00, 0, '[]', '2013-03-24 16:32:11'),
(45, 'KU/CIT/45/3/323', 'Server 2040', 1, '2013-04-03', 56000.00, 1, '[{"attrib_name":"Serial Number","attrib_type":"text","attrib_required":0,"attrib_config":["",""],"attrib_value":"2345467879790890"},{"attrib_name":"Supplier Name and Contact","attrib_type":"text","attrib_required":1,"attrib_config":[""],"attrib_value":"Dell Shop Nairobi Tel: 34322312"},{"attrib_name":"Warranty","attrib_type":"yesno","attrib_required":0,"attrib_config":[""],"attrib_value":"1"},{"attrib_name":"Warranty time (years)","attrib_type":"number","attrib_required":0,"attrib_config":[""],"attrib_value":"3"}]', '2013-03-24 16:33:41');

-- --------------------------------------------------------

--
-- Table structure for table `ams_asset_category`
--

CREATE TABLE IF NOT EXISTS `ams_asset_category` (
  `asset_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_category_name` varchar(100) NOT NULL,
  `asset_category_description` text NOT NULL,
  `asset_category_depreciation_rate` float NOT NULL,
  `asset_category_retire_age` varchar(8) NOT NULL,
  `asset_category_meta_fields` text NOT NULL,
  PRIMARY KEY (`asset_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `ams_asset_category`
--

INSERT INTO `ams_asset_category` (`asset_category_id`, `asset_category_name`, `asset_category_description`, `asset_category_depreciation_rate`, `asset_category_retire_age`, `asset_category_meta_fields`) VALUES
(0, 'Unspecified', 'All assets that are not classified fall here', 3, '', '[]'),
(1, 'Computers and Electronics', 'Laptops, desktops, modems, Servers, mobile phones', 0.5, '3', '[{"attrib_name":"Serial Number","attrib_type":"text","attrib_required":0,"attrib_config":["",""]},{"attrib_name":"Supplier Name and Contact","attrib_type":"text","attrib_required":1,"attrib_config":[""]},{"attrib_name":"Warranty","attrib_type":"yesno","attrib_required":0,"attrib_config":[""]},{"attrib_name":"Warranty time (years)","attrib_type":"number","attrib_required":0,"attrib_config":[""]}]'),
(2, 'Furniture and Fixtures', 'Tables, Shelves, chairs,Cabinets', 25, '3', '[{"attrib_name":"Color","attrib_type":"text","attrib_required":0,"attrib_config":[""]},{"attrib_name":"Number of legs","attrib_type":"number","attrib_required":0,"attrib_config":[""]}]'),
(3, 'Office Equipment', 'Papers, Pens, Staplers, Paper punches, felt pens', 3, '1', '[]'),
(6, 'Tablets', 'Tablet computers..... basically huuuge android/ios/blackberry/windows phones, starting from 7 inches in diagonal screen length', 50, '2', '[]'),
(7, 'Machinery & Equipment', 'All Machines and equipment', 14, '12', '[{"attrib_name":"Color","attrib_type":"options","attrib_required":0,"attrib_config":["Red"," Yellow"," Blue"," Black"," Green"," Pink"]},{"attrib_name":"Weight (tonnes)","attrib_type":"number","attrib_required":0,"attrib_config":["Array"]},{"attrib_name":"Height (metres)","attrib_type":"number","attrib_required":0,"attrib_config":["Array"]}]');

-- --------------------------------------------------------

--
-- Table structure for table `ams_asset_media`
--

CREATE TABLE IF NOT EXISTS `ams_asset_media` (
  `asset_media_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_media_fk_asset_id` int(11) NOT NULL,
  `asset_media_url_path` varchar(128) NOT NULL,
  PRIMARY KEY (`asset_media_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=40 ;

--
-- Dumping data for table `ams_asset_media`
--

INSERT INTO `ams_asset_media` (`asset_media_id`, `asset_media_fk_asset_id`, `asset_media_url_path`) VALUES
(1, 22, 'application/uploads/asset_images/0.656555001361954079.jpg'),
(2, 23, 'application/uploads/asset_images/0.302827001361964272.jpg'),
(3, 23, 'application/uploads/asset_images/0.542240001361995550.jpg'),
(4, 23, 'application/uploads/asset_images/0.099695001361995567.jpg'),
(5, 1, 'application/uploads/asset_images/0.102135001361995585.jpg'),
(6, 23, 'application/uploads/asset_images/0.868115001361995661.jpg'),
(7, 24, 'application/uploads/asset_images/0.181398001361996386.jpg'),
(8, 25, 'application/uploads/asset_images/0.583410001362059083.jpg'),
(9, 26, 'application/uploads/asset_images/0.885773001362924737.jpg'),
(10, 27, 'application/uploads/asset_images/0.955116001362937180.jpg'),
(11, 2, 'application/uploads/asset_images/0.756372001362957139.jpg'),
(12, 2, 'application/uploads/asset_images/0.044278001362957184.jpg'),
(13, 31, 'application/uploads/asset_images/0.967921001363632857.jpg'),
(14, 31, 'application/uploads/asset_images/0.034902001363632858.jpg'),
(15, 31, 'application/uploads/asset_images/0.091137001363632858.jpg'),
(16, 33, 'application/uploads/asset_images/0.777114001363689656.jpg'),
(17, 33, 'application/uploads/asset_images/0.969071001363689656.jpg'),
(18, 34, 'application/uploads/asset_images/0.085564001363773351.jpg'),
(19, 34, 'application/uploads/asset_images/0.300937001363773351.jpg'),
(20, 35, 'application/uploads/asset_images/0.456982001363773452.jpg'),
(21, 35, 'application/uploads/asset_images/0.513020001363773452.jpg'),
(22, 36, 'application/uploads/asset_images/0.615373001363773742.jpg'),
(23, 36, 'application/uploads/asset_images/0.660431001363773742.jpg'),
(25, 42, 'application/uploads/asset_images/0.410437001364131931.jpg'),
(26, 43, 'application/uploads/asset_images/0.410437001364131931.jpg'),
(27, 44, 'application/uploads/asset_images/0.410437001364131931.jpg'),
(28, 45, 'application/uploads/asset_images/0.944366001364132021.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `ams_department`
--

CREATE TABLE IF NOT EXISTS `ams_department` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(45) NOT NULL,
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ams_department`
--

INSERT INTO `ams_department` (`department_id`, `department_name`) VALUES
(0, 'Unspecified'),
(1, 'Computing and InformationTechnology'),
(2, 'Registry'),
(3, 'Finance'),
(4, 'ICT Directorate'),
(5, 'Fixed Assets'),
(6, 'Maintenance'),
(7, 'Main Administration');

-- --------------------------------------------------------

--
-- Table structure for table `ams_group`
--

CREATE TABLE IF NOT EXISTS `ams_group` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(128) NOT NULL,
  `group_description` varchar(256) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ams_group`
--

INSERT INTO `ams_group` (`group_id`, `group_name`, `group_description`) VALUES
(0, 'Super User', ''),
(1, 'DVC administrator', ''),
(2, 'Fixed asset Officer', ''),
(3, 'Finance officer', ''),
(4, 'Head of department', ''),
(5, 'Department employee', ''),
(6, 'Maintenance Officer', '');

-- --------------------------------------------------------

--
-- Table structure for table `ams_group_role`
--

CREATE TABLE IF NOT EXISTS `ams_group_role` (
  `group_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`group_role_id`),
  KEY `group_id` (`group_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=50 ;

--
-- Dumping data for table `ams_group_role`
--

INSERT INTO `ams_group_role` (`group_role_id`, `group_id`, `role_id`) VALUES
(27, 1, 6),
(2, 1, 7),
(3, 1, 18),
(23, 1, 21),
(34, 2, 1),
(41, 2, 6),
(38, 2, 7),
(37, 2, 8),
(49, 2, 10),
(40, 2, 11),
(43, 2, 12),
(42, 2, 13),
(35, 2, 17),
(44, 2, 18),
(47, 2, 28),
(45, 2, 29),
(46, 2, 30),
(48, 2, 31),
(39, 2, 34),
(36, 2, 39),
(11, 3, 1),
(12, 3, 7),
(13, 3, 8),
(30, 3, 31),
(33, 3, 39),
(14, 4, 4),
(32, 4, 9),
(16, 4, 20),
(28, 4, 22),
(29, 4, 23),
(25, 4, 24),
(26, 4, 25),
(20, 6, 15),
(19, 6, 16);

-- --------------------------------------------------------

--
-- Table structure for table `ams_log`
--

CREATE TABLE IF NOT EXISTS `ams_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_date` datetime NOT NULL,
  `log_by_user_id` int(11) NOT NULL,
  `log_to_user_id` int(11) NOT NULL,
  `log_to_dept_id` int(11) NOT NULL,
  `log_to_group_id` int(11) NOT NULL,
  `log_to_asset_id` int(11) NOT NULL,
  `log_description` text NOT NULL,
  `log_asset_description` text NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `ams_log`
--

INSERT INTO `ams_log` (`log_id`, `log_date`, `log_by_user_id`, `log_to_user_id`, `log_to_dept_id`, `log_to_group_id`, `log_to_asset_id`, `log_description`, `log_asset_description`) VALUES
(1, '2013-03-25 18:54:27', 1, 0, 0, 3, 0, 'Your group, Finance officer, has been <b>granted</b> the role View orders placed by Mwangi Eve,Super Administrator. <br/>Role description: View all the orders placed on requests in the system', ''),
(2, '2013-03-25 19:18:23', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has had the role Send requests to the DVC for assets <b>revoked</b> by Mwangi Eve,Super Administrator.<br/> Role description: This user can send direct requests to the DVC or the ICT directorate for assets', ''),
(3, '2013-03-25 19:18:24', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has had the role Add Asset <b>revoked</b> by Mwangi Eve,Super Administrator.<br/> Role description: Define a new asset record.', ''),
(4, '2013-03-25 19:18:26', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has had the role List Past Maintenance Requests <b>revoked</b> by Mwangi Eve,Super Administrator.<br/> Role description: View past asset-maintenance requests from users.', ''),
(5, '2013-03-25 19:18:27', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has had the role Add User <b>revoked</b> by Mwangi Eve,Super Administrator.<br/> Role description: Define a new user-account that may be used to access the system.', ''),
(6, '2013-03-25 19:18:28', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has had the role Assign Asset to Department <b>revoked</b> by Mwangi Eve,Super Administrator.<br/> Role description: Assign an asset to a department.', ''),
(7, '2013-03-25 19:18:28', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has had the role Assign Asset to User <b>revoked</b> by Mwangi Eve,Super Administrator.<br/> Role description: Assign an asset to a user.', ''),
(8, '2013-03-25 19:18:29', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has had the role List Groups <b>revoked</b> by Mwangi Eve,Super Administrator.<br/> Role description: View a tabled listing of user-groups.', ''),
(9, '2013-03-25 19:18:30', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has had the role Add Group <b>revoked</b> by Mwangi Eve,Super Administrator.<br/> Role description: Add a user-group.', ''),
(10, '2013-03-25 19:18:37', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role List Assets by Mwangi Eve,Super Administrator. <br/>Role description: View a tabled listing of all asset data.', ''),
(11, '2013-03-25 19:18:57', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role Edit Asset by Mwangi Eve,Super Administrator. <br/>Role description: Modify asset data.', ''),
(12, '2013-03-25 19:19:26', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role View orders placed by Mwangi Eve,Super Administrator. <br/>Role description: View all the orders placed on requests in the system', ''),
(13, '2013-03-25 19:19:34', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role Add Categories by Mwangi Eve,Super Administrator. <br/>Role description: Add an asset category.', ''),
(14, '2013-03-25 19:20:30', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role List Categories by Mwangi Eve,Super Administrator. <br/>Role description: View a tabled listing of asset categories.', ''),
(15, '2013-03-25 19:21:34', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role Delete asset categories. by Mwangi Eve,Super Administrator. <br/>Role description: The user has the permission to delete an asset category.', ''),
(16, '2013-03-25 19:21:41', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role Add Group by Mwangi Eve,Super Administrator. <br/>Role description: Add a user-group.', ''),
(17, '2013-03-25 19:21:53', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role Assign Asset to Department by Mwangi Eve,Super Administrator. <br/>Role description: Assign an asset to a department.', ''),
(18, '2013-03-25 19:22:06', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role Add Group Role by Mwangi Eve,Super Administrator. <br/>Role description: Grant an access-permission to a user-group.', ''),
(19, '2013-03-25 19:22:13', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role List Group Roles by Mwangi Eve,Super Administrator. <br/>Role description: View a tabled listing of the access-permissions assigned to a group.', ''),
(20, '2013-03-25 19:22:21', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role List of all users by Mwangi Eve,Super Administrator. <br/>Role description: A tabulated list of all users of the system in the institution', ''),
(21, '2013-03-25 19:22:28', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role Delete assets from the system by Mwangi Eve,Super Administrator. <br/>Role description: The user has the right to delete assets from the system.', ''),
(22, '2013-03-25 19:22:33', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role Dispose assets by Mwangi Eve,Super Administrator. <br/>Role description: The user can set assets for disposal', ''),
(23, '2013-03-25 19:22:38', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role View group roles by Mwangi Eve,Super Administrator. <br/>Role description: View the roles assigned to a group of users.', ''),
(24, '2013-03-25 19:22:58', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role View reports generated by the system. by Mwangi Eve,Super Administrator. <br/>Role description: The user can view reports generated by the system.', ''),
(25, '2013-03-25 19:23:14', 1, 0, 0, 2, 0, 'Your group, Fixed asset Officer, has been <b>granted</b> the role List Groups by Mwangi Eve,Super Administrator. <br/>Role description: View a tabled listing of user-groups.', ''),
(26, '2013-03-25 19:24:27', 3, 2, 0, 0, 0, 'Your request for a Water Dispenser , reference number REF/726255 has been <b>approved</b> by Mwangi Peris Wangui, DVC administrator', ''),
(27, '2013-03-25 22:57:22', 1, 0, 5, 0, 0, 'A new asset category:  has been created with a depreciation rate of % p.a.', ''),
(28, '2013-03-25 22:58:11', 1, 0, 5, 0, 0, 'A new asset category:  has been created with a depreciation rate of % p.a.', ''),
(29, '2013-03-26 16:29:34', 2, 5, 0, 0, 0, 'Your request for a Printer , reference number REF/88851 has been <b>approved</b> by Masiror Victor, Chairman', ''),
(30, '2013-03-26 16:31:05', 3, 2, 0, 0, 0, 'Your request for a printer , reference number REF/136719 has been <b>approved</b> by Mwangi Peris Wangui, DVC administrator', ''),
(31, '2013-03-26 16:35:26', 1, 1, 0, 0, 3, 'The asset, Model: lenovo t60 Tag  : KU/CIT/0/23/234, has been repaired. ', 'Repaired by Mwangi Eve.<br/>Problem: I need my laptop blown..<br/> Repair report: Fixed.'),
(32, '2013-03-26 16:37:33', 5, 0, 1, 0, 36, 'The asset, Tag Id:  Model:  has been returned by Olendi Samantha', '');

-- --------------------------------------------------------

--
-- Table structure for table `ams_maintenance`
--

CREATE TABLE IF NOT EXISTS `ams_maintenance` (
  `maintenance_id` int(11) NOT NULL AUTO_INCREMENT,
  `maintenance_fk_asset_id` int(11) NOT NULL,
  `maintenance_fk_cat_id` int(11) NOT NULL,
  `maintenance_fk_user_id` int(11) NOT NULL,
  `maintenance_fk_dept_id` int(11) NOT NULL,
  `maintenance_problem` text NOT NULL,
  `maintenance_problem_date` datetime NOT NULL,
  `maintenance_report` text NOT NULL,
  `maintenance_user_id` int(11) NOT NULL,
  `maintenance_report_date` datetime NOT NULL,
  `maintenance_done` tinyint(1) NOT NULL,
  PRIMARY KEY (`maintenance_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `ams_maintenance`
--

INSERT INTO `ams_maintenance` (`maintenance_id`, `maintenance_fk_asset_id`, `maintenance_fk_cat_id`, `maintenance_fk_user_id`, `maintenance_fk_dept_id`, `maintenance_problem`, `maintenance_problem_date`, `maintenance_report`, `maintenance_user_id`, `maintenance_report_date`, `maintenance_done`) VALUES
(1, 5, 1, 18, 1, 'My laptop is overheating please help.', '2013-02-26 19:58:14', 'Applied some heat sin on the CPU plate. Blew the machine to get rid of any dust', 18, '2013-03-01 14:58:03', 1),
(2, 15, 1, 18, 1, 'My desktop to CPU cable need replacing.', '2013-02-26 20:00:48', 'Replaced the Cable', 18, '2013-03-01 15:24:53', 1),
(3, 15, 1, 18, 1, 'This laptop won''t charge!!', '2013-02-26 20:58:52', 'Replaced charging cable', 18, '2013-03-01 15:11:03', 1),
(4, 5, 1, 18, 1, 'My comp won''t boot.', '2013-02-27 13:27:03', 'Replaced the OS.', 18, '2013-03-01 14:56:55', 1),
(5, 23, 2, 1, 1, 'bla blah blah', '2013-02-27 21:19:21', 'The table just needed a leg fix. Done.', 18, '2013-03-01 13:45:19', 1),
(6, 23, 2, 18, 1, 'kkdjfsdvskvbsdkvbksd', '2013-02-27 21:19:52', 'I flashed the OS and put in a linux version', 18, '2013-03-01 13:30:25', 1),
(7, 2, 1, 18, 1, 'I need a new power cable.', '2013-03-02 12:25:57', 'Request granted', 18, '2013-03-02 12:26:42', 1),
(8, 26, 2, 18, 1, 'One of the leg chairs are broken.', '2013-03-10 21:50:41', 'Fixed a new leg to the chair', 18, '2013-03-10 21:51:53', 1),
(9, 26, 2, 18, 1, 'The Fixed leg has broken again. I think I am gaining weight.', '2013-03-10 21:59:40', '', 0, '0000-00-00 00:00:00', 0),
(10, 3, 1, 1, 1, 'I need my laptop blown.', '2013-03-13 13:33:09', 'Fixed.', 1, '2013-03-26 16:35:26', 1),
(11, 26, 2, 5, 0, 'The chair has broken again.', '2013-03-13 21:50:08', '', 0, '0000-00-00 00:00:00', 0),
(12, 26, 2, 5, 0, 'The chair''s leg is broken.', '2013-03-14 10:23:11', 'Fixed', 7, '2013-03-19 08:35:43', 1),
(13, 3, 1, 1, 1, 'I need my computer blown.', '2013-03-19 12:12:25', '', 0, '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ams_memo`
--

CREATE TABLE IF NOT EXISTS `ams_memo` (
  `memo_id` int(11) NOT NULL AUTO_INCREMENT,
  `memo_to_fk_user_id` int(11) NOT NULL,
  `memo_fk_memo_data_id` int(11) NOT NULL,
  `memo_read` tinyint(1) NOT NULL,
  PRIMARY KEY (`memo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `ams_memo`
--

INSERT INTO `ams_memo` (`memo_id`, `memo_to_fk_user_id`, `memo_fk_memo_data_id`, `memo_read`) VALUES
(1, 2, 1, 1),
(2, 5, 1, 1),
(3, 2, 2, 1),
(4, 5, 2, 1),
(5, 2, 3, 1),
(6, 5, 3, 0),
(7, 2, 4, 1),
(8, 5, 4, 1),
(9, 5, 5, 1),
(10, 0, 6, 0),
(11, 0, 7, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ams_memo_data`
--

CREATE TABLE IF NOT EXISTS `ams_memo_data` (
  `memo_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `memo_data_title` varchar(128) NOT NULL,
  `memo_data_body` text NOT NULL,
  `memo_data_from_fk_user_id` int(11) NOT NULL,
  `memo_data_fk_dept_id` int(11) NOT NULL,
  `memo_data_date` datetime NOT NULL,
  PRIMARY KEY (`memo_data_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `ams_memo_data`
--

INSERT INTO `ams_memo_data` (`memo_data_id`, `memo_data_title`, `memo_data_body`, `memo_data_from_fk_user_id`, `memo_data_fk_dept_id`, `memo_data_date`) VALUES
(1, 'Memo', 'Content', 1, 1, '2013-03-19 22:59:16'),
(2, 'New Memo', 'Memo', 5, 1, '2013-03-19 23:06:44'),
(3, 'Memo 2', 'Content 2', 1, 0, '2013-03-24 12:03:41'),
(4, 'Hello There!', '<p><span style=\\"font-size: large;\\">We need to<span style=\\"text-decoration: underline;\\"><strong> meet up</strong> </span>about the use of the <span style=\\"text-decoration: underline;\\"><span style=\\"color: #99cc00; text-decoration: underline;\\">office photocopier.</span></span></span></p>', 1, 0, '2013-03-25 13:50:02'),
(5, 'You will get a new computer soon', '<p>We will be disposing if that <strong>CRT</strong> very soon :)</p>', 1, 0, '2013-03-25 14:31:58'),
(6, 'memo', '<p>memo</p>', 2, 0, '2013-03-26 16:20:13'),
(7, 'memo', '<p>memo</p>', 2, 0, '2013-03-26 16:20:14');

-- --------------------------------------------------------

--
-- Table structure for table `ams_news`
--

CREATE TABLE IF NOT EXISTS `ams_news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_title` varchar(128) NOT NULL,
  `news_body` text NOT NULL,
  `news_user_id` int(11) NOT NULL,
  `news_date` datetime NOT NULL,
  `news_publish` tinyint(1) NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ams_news`
--

INSERT INTO `ams_news` (`news_id`, `news_title`, `news_body`, `news_user_id`, `news_date`, `news_publish`) VALUES
(1, 'Assets Maintenance', 'It is important that every employee keeps tabs on assets allocated to them. Do call on the attention of the maintenance department when repairs are needed.', 1, '2013-03-12 08:19:22', 1),
(2, 'Keep your information up to date', 'It is imperative that you keep your profile information such as contact number, email, office or area of work up-to-date so that personnel can find you.', 1, '2013-03-12 12:38:21', 1),
(3, 'Delicate assets', 'It has come to our attention that employees have been neglecting their assets especially delicate ones such as computer screens. Remember, before you clear the department you will pay for any of the lost or damaged assets that were in your possession.', 1, '2013-03-12 17:47:45', 1),
(4, 'All Computers to be blown', '<p>All employees <span style=\\"text-decoration: underline;\\"><strong>should submit</strong></span> to the blowing of their <span style=\\"color: #ff6600;\\">computers.</span></p>', 0, '2013-03-25 13:43:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ams_order`
--

CREATE TABLE IF NOT EXISTS `ams_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_fk_request_id` int(11) NOT NULL,
  `order_fk_dept_id` int(11) NOT NULL,
  `order_fk_user_id` int(11) NOT NULL,
  `order_fk_asset_category_id` int(11) NOT NULL,
  `order_description` text NOT NULL,
  `order_date` datetime NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ams_order`
--

INSERT INTO `ams_order` (`order_id`, `order_fk_request_id`, `order_fk_dept_id`, `order_fk_user_id`, `order_fk_asset_category_id`, `order_description`, `order_date`) VALUES
(1, 5, 1, 3, 1, 'Water Dispenser Title: Needed Message: For meetings and whatnot.', '2013-03-25 19:53:27'),
(2, 7, 1, 3, 1, 'printer Title: Printer needed Message: ASAP.', '2013-03-26 16:31:31');

-- --------------------------------------------------------

--
-- Table structure for table `ams_report`
--

CREATE TABLE IF NOT EXISTS `ams_report` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `report_fk_asset_id` int(11) NOT NULL,
  `report_fk_user_id` int(11) NOT NULL,
  `report_fk_dept_id` int(11) NOT NULL,
  `report_remarks` text NOT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ams_report`
--

INSERT INTO `ams_report` (`report_id`, `report_fk_asset_id`, `report_fk_user_id`, `report_fk_dept_id`, `report_remarks`) VALUES
(1, 35, 2, 1, 'The chair has been stolen!');

-- --------------------------------------------------------

--
-- Table structure for table `ams_request`
--

CREATE TABLE IF NOT EXISTS `ams_request` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_reference` varchar(16) NOT NULL,
  `request_date` datetime NOT NULL,
  `request_fk_user_id` int(11) NOT NULL,
  `request_fk_asset_cat_id` int(11) NOT NULL,
  `request_fk_dept_id` int(11) NOT NULL,
  `request_model` varchar(100) NOT NULL,
  `request_title` varchar(100) NOT NULL,
  `request_body` text NOT NULL,
  `request_for_hod` int(11) NOT NULL,
  `request_for_dvc` int(11) NOT NULL,
  `request_hod_approve` tinyint(1) NOT NULL,
  `request_hod_remarks` text NOT NULL,
  `request_dvc_approve` tinyint(1) NOT NULL,
  `request_dvc_remarks` text NOT NULL,
  `request_fk_allocation_id` int(11) NOT NULL,
  `request_read` tinyint(1) NOT NULL,
  PRIMARY KEY (`request_id`),
  KEY `user_id_idx` (`request_fk_user_id`),
  KEY `cat_id_idx` (`request_fk_asset_cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `ams_request`
--

INSERT INTO `ams_request` (`request_id`, `request_reference`, `request_date`, `request_fk_user_id`, `request_fk_asset_cat_id`, `request_fk_dept_id`, `request_model`, `request_title`, `request_body`, `request_for_hod`, `request_for_dvc`, `request_hod_approve`, `request_hod_remarks`, `request_dvc_approve`, `request_dvc_remarks`, `request_fk_allocation_id`, `request_read`) VALUES
(1, 'REF/513495', '2013-03-21 23:50:38', 7, 1, 6, 'office printer and photocopier', 'office printer and photocopier', 'We need one urgently.', 1, 0, 0, '', 0, '', 0, 0),
(2, 'REF/1026435', '2013-03-24 17:04:03', 2, 1, 1, 'Dell Inspiron Laptps', 'We need 2 such laptios', 'Content here', 0, 1, 0, '', 1, '', 10, 1),
(3, 'REF/238302', '2013-03-24 22:16:48', 5, 6, 1, 'Note II', 'Need a Tablet', 'I need a tablet', 1, 0, 1, '', 0, '', 0, 1),
(4, 'REF/118443', '2013-03-24 22:18:56', 2, 1, 1, 'Note II', 'Needed', 'ASAP', 0, 1, 0, '', 1, '', 11, 1),
(5, 'REF/726255', '2013-03-25 18:59:56', 2, 1, 1, 'Water Dispenser', 'Needed', 'For meetings and whatnot.', 0, 1, 0, '', 1, '', 12, 1),
(6, 'REF/88851', '2013-03-26 16:28:52', 5, 1, 1, 'Printer', 'I need a Printer', 'as soon as possible.', 1, 0, 1, '', 0, '', 0, 1),
(7, 'REF/136719', '2013-03-26 16:30:03', 2, 1, 1, 'printer', 'Printer needed', 'ASAP.', 0, 1, 0, '', 1, 'Ok. I approve', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ams_returned_asset`
--

CREATE TABLE IF NOT EXISTS `ams_returned_asset` (
  `returned_asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `returned_asset_asset_id` int(11) NOT NULL,
  `returned_asset_user_id` int(11) NOT NULL,
  `returned_asset_dept_id` int(11) NOT NULL,
  `returned_by_dept_id` int(11) NOT NULL,
  PRIMARY KEY (`returned_asset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ams_returned_asset`
--

INSERT INTO `ams_returned_asset` (`returned_asset_id`, `returned_asset_asset_id`, `returned_asset_user_id`, `returned_asset_dept_id`, `returned_by_dept_id`) VALUES
(1, 36, 5, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ams_role`
--

CREATE TABLE IF NOT EXISTS `ams_role` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(128) NOT NULL,
  `role_alias` varchar(128) NOT NULL,
  `role_description` varchar(256) NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ams_role`
--

INSERT INTO `ams_role` (`role_id`, `role_name`, `role_alias`, `role_description`) VALUES
(0, '*', 'Super Admin', 'Grants full access to everything.'),
(1, 'LIST_ASSETS', 'List Assets', 'View a tabled listing of all asset data.'),
(2, 'ADD_ASSET', 'Add Asset', 'Define a new asset record.'),
(3, 'ADD_USER', 'Add User', 'Define a new user-account that may be used to access the system.'),
(4, 'LIST_DEPARTMENT_ASSETS', 'List Department Assets', 'View a tabled listing of only those assets of a particular department.'),
(5, 'ADD_DEPARTMENT_ASSET', 'Add Department Asset', 'Add a new asset record for an entity at the department level, which will be automatically assigned to that department.'),
(6, 'ASSIGN_ASSET_TO_DEPARTMENT', 'Assign Asset to Department', 'Assign an asset to a department.'),
(7, 'LIST_CATEGORIES', 'List Categories', 'View a tabled listing of asset categories.'),
(8, 'ADD_CATEGORY', 'Add Categories', 'Add an asset category.'),
(9, 'ASSIGN_ASSET_TO_USER', 'Assign Asset to User', 'Assign an asset to a user.'),
(10, 'LIST_GROUPS', 'List Groups', 'View a tabled listing of user-groups.'),
(11, 'ADD_GROUP', 'Add Group', 'Add a user-group.'),
(12, 'LIST_GROUP_ROLES', 'List Group Roles', 'View a tabled listing of the access-permissions assigned to a group.'),
(13, 'ADD_GROUP_ROLE', 'Add Group Role', 'Grant an access-permission to a user-group.'),
(14, 'DELETE_GROUP_ROLE', 'Delete Group Role', 'Revoke an access-permission from a user-group.'),
(15, 'LIST_MAINTENANCE_REQUESTS', 'List Maintenance Requests', 'View pending asset-maintenance requests from users.'),
(16, 'LIST_PAST_MAINTENANCE_REQUESTS', 'List Past Maintenance Requests', 'View past asset-maintenance requests from users.'),
(17, 'EDIT_ASSET', 'Edit Asset', 'Modify asset data.'),
(18, 'LIST_USERS', 'List of all users', 'A tabulated list of all users of the system in the institution'),
(19, 'LIST_LOGS', 'View all logs of activity', 'Lists out all activities carried out in the system'),
(20, 'EDIT_USER', 'Edit user information', 'Edit a user''s information in the system.'),
(21, 'LIST_DEPARTMENT_REQUESTS', 'View asset requests from various departments.', 'The user can view requests from departments and act on them.'),
(22, 'LIST_INHOUSE_REQUESTS', 'View asset requests from users in their department.', 'The user can view all asset requests from their own department and act on them.'),
(23, 'SEND_DVC_REQUESTS', 'Send requests to the DVC for assets', 'This user can send direct requests to the DVC or the ICT directorate for assets'),
(24, 'ADD_DEPARTMENT_USER', 'Add a user from your department.', 'The user has the right to add users to the system that are from their department.'),
(25, 'LIST_DEPARTMENT_USERS', 'View a tabled listing of all users in your departmement', 'The user can view all users from their department.'),
(26, 'VIEW_USER_ROLES', 'View a user''s roles', 'View all the permissions that a user have been granted.'),
(27, 'EDIT_DEPARTMENT_ASSET', 'Modify data about department assets', 'The user can modify any information about a department asset.'),
(28, 'VIEW_GROUP_ROLE', 'View group roles', 'View the roles assigned to a group of users.'),
(29, 'DELETE_ASSET', 'Delete assets from the system', 'The user has the right to delete assets from the system.'),
(30, 'DISPOSE_ASSET', 'Dispose assets', 'The user can set assets for disposal'),
(31, 'VIEW_REPORTS', 'View reports generated by the system.', 'The user can view reports generated by the system.'),
(32, 'VIEW_MAP', 'View a map of Kenyatta University', 'View a map of Kenyatta University'),
(33, 'VIEW_INCOMING_REQUESTS', 'View any incoming requests', 'The user can view either the requests coming from employees in a department or those requests coming from departments themselves'),
(34, 'DELETE_ASSET_CATEGORY', 'Delete asset categories.', 'The user has the permission to delete an asset category.'),
(35, 'COMPOSE_DEPARTMENT_REQUESTS', 'Compose requests on behalf of the department', 'Compose requests on behalf of the department to the DVC or the ICT directorate.'),
(36, 'VIEW_ASSETS_SET_FOR_DISPOSAL', 'View assets set for disposal', 'View all assets set for disposal.'),
(37, 'VIEW_RETURNED ASSETS', 'View returned assets awaiting disposal', 'View returned assets awaiting disposal.'),
(38, 'MAKE_NEWS', 'Make news that are visible to all users of the system', 'The user can create news content that will be available to all users of the system'),
(39, 'VIEW ORDERS', 'View orders placed', 'View all the orders placed on requests in the system');

-- --------------------------------------------------------

--
-- Table structure for table `ams_user`
--

CREATE TABLE IF NOT EXISTS `ams_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_username` varchar(45) NOT NULL,
  `user_password` varchar(45) NOT NULL,
  `user_employment_number` varchar(45) NOT NULL,
  `user_id_number` int(11) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_phone_number` varchar(20) NOT NULL,
  `user_surname` varchar(100) NOT NULL,
  `user_othername` varchar(100) NOT NULL,
  `user_employment_date` date NOT NULL,
  `user_role` varchar(45) NOT NULL,
  `user_office` varchar(50) NOT NULL,
  `user_fk_dept_id` varchar(45) NOT NULL,
  `user_fk_cat_id` int(11) NOT NULL,
  `user_last_login` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_fk_cat_id_idx` (`user_fk_cat_id`),
  KEY `user_surname` (`user_surname`),
  KEY `user_othername` (`user_othername`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `ams_user`
--

INSERT INTO `ams_user` (`user_id`, `user_username`, `user_password`, `user_employment_number`, `user_id_number`, `user_email`, `user_phone_number`, `user_surname`, `user_othername`, `user_employment_date`, `user_role`, `user_office`, `user_fk_dept_id`, `user_fk_cat_id`, `user_last_login`) VALUES
(0, 'N/A', '', '', 0, '', '', 'N/A', 'N/A', '0000-00-00', '', '', '', 0, '0000-00-00 00:00:00'),
(1, 'eve', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '222222', 27540429, 'muthoni@mail.com', '0711995601', 'Mwangi', 'Eve', '2013-02-27', 'Super Administrator', 'Twin towers room 2014', '0', 2, '2013-03-26 16:07:27'),
(2, 'koech', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '25/0384/23', 541654, 'koech@mail.com', '2454515', 'Masiror', 'Victor', '2007-01-04', 'Chairman', 'CIT department room 118', '1', 4, '2013-03-25 19:01:55'),
(3, 'peris', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '67/7765/234', 2545454, 'muthoni90@gmail.com', '0726532523', 'Mwangi', 'Peris Wangui', '2013-02-07', 'DVC administrator', '', '7', 1, '2013-03-26 16:37:45'),
(5, 'sam', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '67/452/23/322', 2434546, 'sam@mail.com', '0726464466', 'Olendi', 'Samantha', '2009-03-03', 'Receptionist', 'CIT department room 103', '1', 5, '2013-03-24 23:44:42'),
(6, 'mary', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '56/34/45/8/4', 125465465, 'mary@mail.com', '071664562', 'Gathangu', 'Mary Naomi', '2006-03-05', 'Chief Finance Officer', '', '3', 3, '0000-00-00 00:00:00'),
(7, 'mwangi', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '454/34/324/4556', 23454545, 'mwangi@mail.com', '072313251', 'Mwangi', 'Francis Kimotho', '2007-03-14', 'Chief Maintenance Officer', 'Maintenance Building Room 123A', '6', 6, '2013-03-26 16:16:14'),
(8, 'oliwa', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '11/22/11/33', 231301320, 'oliwa@mail.com', '0722416466', 'Oliwa ', 'John', '2013-03-27', 'Director', 'Fixed Assets Office Ground floor room 12', '5', 2, '2013-03-25 20:04:02');

-- --------------------------------------------------------

--
-- Table structure for table `ams_user_media`
--

CREATE TABLE IF NOT EXISTS `ams_user_media` (
  `user_media_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_media_fk_user_id` int(11) NOT NULL,
  `user_media_url_path` varchar(128) NOT NULL,
  PRIMARY KEY (`user_media_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `ams_user_media`
--

INSERT INTO `ams_user_media` (`user_media_id`, `user_media_fk_user_id`, `user_media_url_path`) VALUES
(1, 1, 'application/uploads/user_images/0.251931001364243206.jpg'),
(2, 2, 'application/uploads/user_images/0.113833001362953628.png'),
(3, 5, 'application/uploads/user_images/0.901132001362958822.png'),
(4, 6, 'application/uploads/user_images/0.806099001362959083.png'),
(5, 7, 'application/uploads/user_images/0.700256001362959205.png'),
(6, 3, 'application/uploads/user_images/0.615202001363071008.jpg'),
(7, 8, 'application/uploads/user_images/0.098525001364228201.png');

-- --------------------------------------------------------

--
-- Table structure for table `ams_user_role`
--

CREATE TABLE IF NOT EXISTS `ams_user_role` (
  `user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `user_role_grant` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `ams_user_role`
--

INSERT INTO `ams_user_role` (`user_role_id`, `user_id`, `role_id`, `user_role_grant`) VALUES
(1, 1, 0, 1),
(2, 2, 22, 1),
(4, 2, 27, 1),
(7, 7, 15, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
