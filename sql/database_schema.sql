-- MySQL dump 10.13  Distrib 5.5.40, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ybdb
-- ------------------------------------------------------
-- Server version	5.5.40-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(20) NOT NULL DEFAULT '',
  `middle_initial` char(2) NOT NULL DEFAULT '',
  `last_name` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(70) NOT NULL DEFAULT '',
  `phone` varchar(45) NOT NULL DEFAULT '',
  `address1` varchar(70) NOT NULL DEFAULT '',
  `address2` varchar(70) NOT NULL DEFAULT '',
  `city` varchar(25) NOT NULL DEFAULT '',
  `state` char(2) NOT NULL DEFAULT '',
  `country` varchar(25) NOT NULL DEFAULT '',
  `receive_newsletter` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` datetime DEFAULT NULL,
  `invited_newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `DOB` date NOT NULL DEFAULT '0000-00-00',
  `pass` varbinary(30) NOT NULL DEFAULT '',
  `zip` varchar(5) NOT NULL DEFAULT '',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `location_name` varchar(45) NOT NULL DEFAULT '',
  `location_type` varchar(45) DEFAULT NULL,
  `waiver` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contact_id`),
  KEY `location_type` (`location_type`),
  CONSTRAINT `location_type` FOREIGN KEY (`location_type`) REFERENCES `transaction_types` (`transaction_type_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=494 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 5120 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `options`
--

DROP TABLE IF EXISTS `options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `options` (
  `option_name_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(64) NOT NULL,
  PRIMARY KEY (`option_name_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `project_id` varchar(50) NOT NULL DEFAULT '',
  `date_established` date NOT NULL DEFAULT '0000-00-00',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `public` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sale_log`
--

DROP TABLE IF EXISTS `sale_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sale_log` (
  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sale_type` varchar(45) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `amount` float NOT NULL DEFAULT '0',
  `sold_by` varchar(45) NOT NULL DEFAULT '',
  `sold_to` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `selections`
--

DROP TABLE IF EXISTS `selections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `selections` (
  `contact_id` int(10) unsigned DEFAULT NULL,
  `selection` int(10) unsigned DEFAULT NULL,
  `selection_value` text,
  KEY `contact_id` (`contact_id`),
  KEY `selection` (`selection`),
  CONSTRAINT `selections_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `selections_ibfk_2` FOREIGN KEY (`selection`) REFERENCES `options` (`option_name_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_hours`
--

DROP TABLE IF EXISTS `shop_hours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_hours` (
  `shop_visit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(10) unsigned NOT NULL DEFAULT '0',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0',
  `shop_user_role` varchar(45) NOT NULL DEFAULT '',
  `project_id` varchar(45) DEFAULT NULL,
  `time_in` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_out` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment` tinytext,
  PRIMARY KEY (`shop_visit_id`),
  KEY `contact_id` (`contact_id`),
  KEY `shop_user_role` (`shop_user_role`),
  KEY `project_id` (`project_id`),
  KEY `shop_id` (`shop_id`),
  CONSTRAINT `contact_id` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`contact_id`) ON UPDATE CASCADE,
  CONSTRAINT `project_id` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  CONSTRAINT `shop_id` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON UPDATE CASCADE,
  CONSTRAINT `shop_user_role` FOREIGN KEY (`shop_user_role`) REFERENCES `shop_user_roles` (`shop_user_role_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2742 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 4096 kB; (`contact_id`) REFER `nwilkes_ybdb/con';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_locations`
--

DROP TABLE IF EXISTS `shop_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_locations` (
  `shop_location_id` varchar(30) NOT NULL DEFAULT '',
  `date_established` date NOT NULL DEFAULT '0000-00-00',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_types`
--

DROP TABLE IF EXISTS `shop_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_types` (
  `shop_type_id` varchar(30) NOT NULL DEFAULT '',
  `list_order` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_user_roles`
--

DROP TABLE IF EXISTS `shop_user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_user_roles` (
  `shop_user_role_id` varchar(45) NOT NULL DEFAULT '',
  `hours_rank` int(10) unsigned NOT NULL DEFAULT '0',
  `volunteer` tinyint(1) NOT NULL DEFAULT '0',
  `sales` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `paid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `other_volunteer` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shops`
--

DROP TABLE IF EXISTS `shops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shops` (
  `shop_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `shop_location` varchar(45) NOT NULL DEFAULT '',
  `shop_type` varchar(45) NOT NULL DEFAULT '',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_id`),
  KEY `shop_type` (`shop_type`),
  KEY `shop_location` (`shop_location`),
  CONSTRAINT `shop_location` FOREIGN KEY (`shop_location`) REFERENCES `shop_locations` (`shop_location_id`) ON UPDATE CASCADE,
  CONSTRAINT `shop_type` FOREIGN KEY (`shop_type`) REFERENCES `shop_types` (`shop_type_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=453 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transaction_log`
--

DROP TABLE IF EXISTS `transaction_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_log` (
  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_startstorage` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `transaction_type` varchar(45) NOT NULL DEFAULT '',
  `amount` float DEFAULT '0',
  `description` text,
  `sold_to` int(10) unsigned DEFAULT NULL,
  `sold_by` int(10) unsigned DEFAULT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `payment_type` varchar(6) DEFAULT NULL,
  `check_number` int(10) unsigned DEFAULT NULL,
  `change_fund` float DEFAULT NULL,
  `anonymous` tinyint(1) NOT NULL DEFAULT '0',
  `history` longblob NOT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `transaction_type` (`transaction_type`),
  KEY `sold_to` (`sold_to`),
  KEY `sold_by` (`sold_by`),
  CONSTRAINT `sold_by` FOREIGN KEY (`sold_by`) REFERENCES `contacts` (`contact_id`) ON UPDATE CASCADE,
  CONSTRAINT `sold_to` FOREIGN KEY (`sold_to`) REFERENCES `contacts` (`contact_id`) ON UPDATE CASCADE,
  CONSTRAINT `transaction_type` FOREIGN KEY (`transaction_type`) REFERENCES `transaction_types` (`transaction_type_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1056 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transaction_types`
--

DROP TABLE IF EXISTS `transaction_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_types` (
  `transaction_type_id` varchar(45) NOT NULL DEFAULT '',
  `rank` varchar(45) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `community_bike` tinyint(1) NOT NULL DEFAULT '0',
  `show_transaction_id` tinyint(1) NOT NULL DEFAULT '0',
  `show_type` tinyint(1) NOT NULL DEFAULT '0',
  `show_startdate` tinyint(1) NOT NULL DEFAULT '0',
  `show_amount` tinyint(1) NOT NULL DEFAULT '0',
  `show_description` tinyint(1) NOT NULL DEFAULT '0',
  `show_soldto` tinyint(1) NOT NULL DEFAULT '0',
  `show_soldby` tinyint(1) NOT NULL DEFAULT '0',
  `fieldname_date` varchar(25) NOT NULL DEFAULT '',
  `fieldname_soldby` varchar(25) NOT NULL DEFAULT '',
  `message_transaction_id` varchar(100) NOT NULL DEFAULT '',
  `fieldname_soldto` varchar(45) NOT NULL DEFAULT '',
  `show_soldto_signed_in` tinyint(1) NOT NULL DEFAULT '0',
  `fieldname_description` varchar(45) NOT NULL,
  `accounting_group` varchar(45) NOT NULL,
  `show_payment` tinyint(1) NOT NULL DEFAULT '1',
  `anonymous` tinyint(1) NOT NULL DEFAULT '0',
  `show_soldto_not_signed_in` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`transaction_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `view_EmployeeHours`
--

DROP TABLE IF EXISTS `view_EmployeeHours`;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeHours`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_EmployeeHours` (
  `Year` tinyint NOT NULL,
  `Month` tinyint NOT NULL,
  `YearWeek` tinyint NOT NULL,
  `Week` tinyint NOT NULL,
  `Date` tinyint NOT NULL,
  `ContactID` tinyint NOT NULL,
  `Name` tinyint NOT NULL,
  `Hours` tinyint NOT NULL,
  `Pay` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_EmployeeHours_byMonth`
--

DROP TABLE IF EXISTS `view_EmployeeHours_byMonth`;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeHours_byMonth`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_EmployeeHours_byMonth` (
  `Year` tinyint NOT NULL,
  `Month` tinyint NOT NULL,
  `ContactID` tinyint NOT NULL,
  `Name` tinyint NOT NULL,
  `Hours` tinyint NOT NULL,
  `Pay` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_EmployeeHours_byMonth_WholeOper`
--

DROP TABLE IF EXISTS `view_EmployeeHours_byMonth_WholeOper`;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeHours_byMonth_WholeOper`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_EmployeeHours_byMonth_WholeOper` (
  `Year` tinyint NOT NULL,
  `Month` tinyint NOT NULL,
  `Hours` tinyint NOT NULL,
  `Pay` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_EmployeeHours_byWeek`
--

DROP TABLE IF EXISTS `view_EmployeeHours_byWeek`;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeHours_byWeek`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_EmployeeHours_byWeek` (
  `Year` tinyint NOT NULL,
  `Week` tinyint NOT NULL,
  `ContactID` tinyint NOT NULL,
  `Name` tinyint NOT NULL,
  `Hours` tinyint NOT NULL,
  `Pay` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_EmployeeHours_byWeek_WholeOper`
--

DROP TABLE IF EXISTS `view_EmployeeHours_byWeek_WholeOper`;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeHours_byWeek_WholeOper`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_EmployeeHours_byWeek_WholeOper` (
  `Year` tinyint NOT NULL,
  `Week` tinyint NOT NULL,
  `Hours` tinyint NOT NULL,
  `Pay` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_EmployeeMetrics_BikesbyMonth`
--

DROP TABLE IF EXISTS `view_EmployeeMetrics_BikesbyMonth`;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeMetrics_BikesbyMonth`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_EmployeeMetrics_BikesbyMonth` (
  `Year` tinyint NOT NULL,
  `Month` tinyint NOT NULL,
  `contact_id` tinyint NOT NULL,
  `TotalValue` tinyint NOT NULL,
  `AverageValue` tinyint NOT NULL,
  `Count` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_EmployeeMetrics_BikesbyWeek`
--

DROP TABLE IF EXISTS `view_EmployeeMetrics_BikesbyWeek`;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeMetrics_BikesbyWeek`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_EmployeeMetrics_BikesbyWeek` (
  `Year` tinyint NOT NULL,
  `Week` tinyint NOT NULL,
  `contact_id` tinyint NOT NULL,
  `TotalValue` tinyint NOT NULL,
  `AverageValue` tinyint NOT NULL,
  `Count` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_EmployeeMetrics_TotalsByMonth`
--

DROP TABLE IF EXISTS `view_EmployeeMetrics_TotalsByMonth`;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeMetrics_TotalsByMonth`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_EmployeeMetrics_TotalsByMonth` (
  `Year` tinyint NOT NULL,
  `Month` tinyint NOT NULL,
  `Name` tinyint NOT NULL,
  `ContactID` tinyint NOT NULL,
  `OutputValueVsPayRatio` tinyint NOT NULL,
  `HoursPerBike` tinyint NOT NULL,
  `NumBikes` tinyint NOT NULL,
  `AverageBikePrice` tinyint NOT NULL,
  `TotalValueBikes` tinyint NOT NULL,
  `NumWheels` tinyint NOT NULL,
  `AverageWheelPrice` tinyint NOT NULL,
  `TotalValueWheels` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_EmployeeMetrics_TotalsByWeek`
--

DROP TABLE IF EXISTS `view_EmployeeMetrics_TotalsByWeek`;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeMetrics_TotalsByWeek`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_EmployeeMetrics_TotalsByWeek` (
  `Year` tinyint NOT NULL,
  `Week` tinyint NOT NULL,
  `Name` tinyint NOT NULL,
  `ContactID` tinyint NOT NULL,
  `OutputValueVsPayRatio` tinyint NOT NULL,
  `HoursPerBike` tinyint NOT NULL,
  `NumBikes` tinyint NOT NULL,
  `AverageBikePrice` tinyint NOT NULL,
  `TotalValueBikes` tinyint NOT NULL,
  `NumWheels` tinyint NOT NULL,
  `AverageWheelPrice` tinyint NOT NULL,
  `TotalValueWheels` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_EmployeeMetrics_WheelsbyMonth`
--

DROP TABLE IF EXISTS `view_EmployeeMetrics_WheelsbyMonth`;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeMetrics_WheelsbyMonth`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_EmployeeMetrics_WheelsbyMonth` (
  `Year` tinyint NOT NULL,
  `Month` tinyint NOT NULL,
  `contact_id` tinyint NOT NULL,
  `TotalValue` tinyint NOT NULL,
  `AverageValue` tinyint NOT NULL,
  `Count` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_EmployeeMetrics_WheelsbyWeek`
--

DROP TABLE IF EXISTS `view_EmployeeMetrics_WheelsbyWeek`;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeMetrics_WheelsbyWeek`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_EmployeeMetrics_WheelsbyWeek` (
  `Year` tinyint NOT NULL,
  `Week` tinyint NOT NULL,
  `contact_id` tinyint NOT NULL,
  `TotalValue` tinyint NOT NULL,
  `AverageValue` tinyint NOT NULL,
  `Count` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_MechanicOperationMetrics_byMonth`
--

DROP TABLE IF EXISTS `view_MechanicOperationMetrics_byMonth`;
/*!50001 DROP VIEW IF EXISTS `view_MechanicOperationMetrics_byMonth`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_MechanicOperationMetrics_byMonth` (
  `Year` tinyint NOT NULL,
  `Month` tinyint NOT NULL,
  `Hours` tinyint NOT NULL,
  `Pay` tinyint NOT NULL,
  `NetSalesNewParts` tinyint NOT NULL,
  `SalesUsedParts` tinyint NOT NULL,
  `ValueBikesFixed` tinyint NOT NULL,
  `ValueWheelsFixed` tinyint NOT NULL,
  `ValueNewPartsOnBikes` tinyint NOT NULL,
  `EstimatedNetIncome` tinyint NOT NULL,
  `TotalBikesFixed` tinyint NOT NULL,
  `TotalWheelsFixed` tinyint NOT NULL,
  `HoursPerBike` tinyint NOT NULL,
  `AverageBikeValue` tinyint NOT NULL,
  `SalesBikes` tinyint NOT NULL,
  `TotalBikesSold` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_MechanicOperationMetrics_byWeek`
--

DROP TABLE IF EXISTS `view_MechanicOperationMetrics_byWeek`;
/*!50001 DROP VIEW IF EXISTS `view_MechanicOperationMetrics_byWeek`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_MechanicOperationMetrics_byWeek` (
  `Year` tinyint NOT NULL,
  `Week` tinyint NOT NULL,
  `Hours` tinyint NOT NULL,
  `Pay` tinyint NOT NULL,
  `NetSalesNewParts` tinyint NOT NULL,
  `SalesUsedParts` tinyint NOT NULL,
  `ValueBikesFixed` tinyint NOT NULL,
  `ValueWheelsFixed` tinyint NOT NULL,
  `ValueNewPartsOnBikes` tinyint NOT NULL,
  `EstimatedNetIncome` tinyint NOT NULL,
  `TotalBikesFixed` tinyint NOT NULL,
  `TotalWheelsFixed` tinyint NOT NULL,
  `HoursPerBike` tinyint NOT NULL,
  `AverageBikeValue` tinyint NOT NULL,
  `SalesBikes` tinyint NOT NULL,
  `TotalBikesSold` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_Transactions`
--

DROP TABLE IF EXISTS `view_Transactions`;
/*!50001 DROP VIEW IF EXISTS `view_Transactions`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_Transactions` (
  `Year` tinyint NOT NULL,
  `Month` tinyint NOT NULL,
  `YearWeek` tinyint NOT NULL,
  `Week` tinyint NOT NULL,
  `TransactionType` tinyint NOT NULL,
  `Total` tinyint NOT NULL,
  `AccountingGroup` tinyint NOT NULL,
  `ShopType` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_Transactions_MechOper_byMonth`
--

DROP TABLE IF EXISTS `view_Transactions_MechOper_byMonth`;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_MechOper_byMonth`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_Transactions_MechOper_byMonth` (
  `Year` tinyint NOT NULL,
  `Month` tinyint NOT NULL,
  `TransactionType` tinyint NOT NULL,
  `Total` tinyint NOT NULL,
  `Count` tinyint NOT NULL,
  `AccountingGroup` tinyint NOT NULL,
  `ShopType` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_Transactions_MechOper_byMonth_pvTbl`
--

DROP TABLE IF EXISTS `view_Transactions_MechOper_byMonth_pvTbl`;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_MechOper_byMonth_pvTbl`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_Transactions_MechOper_byMonth_pvTbl` (
  `Year` tinyint NOT NULL,
  `Month` tinyint NOT NULL,
  `NetSalesNewParts` tinyint NOT NULL,
  `SalesUsedParts` tinyint NOT NULL,
  `SalesBikes` tinyint NOT NULL,
  `ValueBikesFixed` tinyint NOT NULL,
  `ValueWheelsFixed` tinyint NOT NULL,
  `TotalBikesSold` tinyint NOT NULL,
  `TotalBikesFixed` tinyint NOT NULL,
  `TotalWheelsFixed` tinyint NOT NULL,
  `ValueNewPartsOnBikes` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_Transactions_MechOper_byWeek`
--

DROP TABLE IF EXISTS `view_Transactions_MechOper_byWeek`;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_MechOper_byWeek`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_Transactions_MechOper_byWeek` (
  `Year` tinyint NOT NULL,
  `Week` tinyint NOT NULL,
  `TransactionType` tinyint NOT NULL,
  `Total` tinyint NOT NULL,
  `Count` tinyint NOT NULL,
  `AccountingGroup` tinyint NOT NULL,
  `ShopType` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_Transactions_MechOper_byWeek_pvTbl`
--

DROP TABLE IF EXISTS `view_Transactions_MechOper_byWeek_pvTbl`;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_MechOper_byWeek_pvTbl`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_Transactions_MechOper_byWeek_pvTbl` (
  `Year` tinyint NOT NULL,
  `Week` tinyint NOT NULL,
  `NetSalesNewParts` tinyint NOT NULL,
  `SalesUsedParts` tinyint NOT NULL,
  `SalesBikes` tinyint NOT NULL,
  `ValueBikesFixed` tinyint NOT NULL,
  `ValueWheelsFixed` tinyint NOT NULL,
  `TotalBikesSold` tinyint NOT NULL,
  `TotalBikesFixed` tinyint NOT NULL,
  `TotalWheelsFixed` tinyint NOT NULL,
  `ValueNewPartsOnBikes` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_Transactions_VolRunShop_byMonth`
--

DROP TABLE IF EXISTS `view_Transactions_VolRunShop_byMonth`;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_VolRunShop_byMonth`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_Transactions_VolRunShop_byMonth` (
  `Year` tinyint NOT NULL,
  `Month` tinyint NOT NULL,
  `TransactionType` tinyint NOT NULL,
  `Total` tinyint NOT NULL,
  `Count` tinyint NOT NULL,
  `AccountingGroup` tinyint NOT NULL,
  `ShopType` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_Transactions_VolRunShop_byMonth_pvTbl`
--

DROP TABLE IF EXISTS `view_Transactions_VolRunShop_byMonth_pvTbl`;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_VolRunShop_byMonth_pvTbl`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_Transactions_VolRunShop_byMonth_pvTbl` (
  `Year` tinyint NOT NULL,
  `Month` tinyint NOT NULL,
  `NetSalesNewParts` tinyint NOT NULL,
  `SalesUsedParts` tinyint NOT NULL,
  `SalesBikes` tinyint NOT NULL,
  `TotalBikesSold` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_Transactions_VolRunShop_byWeek`
--

DROP TABLE IF EXISTS `view_Transactions_VolRunShop_byWeek`;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_VolRunShop_byWeek`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_Transactions_VolRunShop_byWeek` (
  `Year` tinyint NOT NULL,
  `Week` tinyint NOT NULL,
  `TransactionType` tinyint NOT NULL,
  `Total` tinyint NOT NULL,
  `Count` tinyint NOT NULL,
  `AccountingGroup` tinyint NOT NULL,
  `ShopType` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_Transactions_VolRunShop_byWeek_pvTbl`
--

DROP TABLE IF EXISTS `view_Transactions_VolRunShop_byWeek_pvTbl`;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_VolRunShop_byWeek_pvTbl`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_Transactions_VolRunShop_byWeek_pvTbl` (
  `Year` tinyint NOT NULL,
  `Week` tinyint NOT NULL,
  `NetSalesNewParts` tinyint NOT NULL,
  `SalesUsedParts` tinyint NOT NULL,
  `SalesBikes` tinyint NOT NULL,
  `TotalBikesSold` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_sales_by_week`
--

DROP TABLE IF EXISTS `view_sales_by_week`;
/*!50001 DROP VIEW IF EXISTS `view_sales_by_week`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_sales_by_week` (
  `Year` tinyint NOT NULL,
  `Week` tinyint NOT NULL,
  `TransactionType` tinyint NOT NULL,
  `Total` tinyint NOT NULL,
  `CountOfTrans` tinyint NOT NULL,
  `AccountingGroup` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `view_EmployeeHours`
--

/*!50001 DROP TABLE IF EXISTS `view_EmployeeHours`*/;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeHours`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_EmployeeHours` AS select year(`shop_hours`.`time_in`) AS `Year`,month(`shop_hours`.`time_in`) AS `Month`,if((week(`shop_hours`.`time_in`,0) <> 0),year(`shop_hours`.`time_in`),(year(`shop_hours`.`time_in`) - 1)) AS `YearWeek`,if((week(`shop_hours`.`time_in`,0) <> 0),week(`shop_hours`.`time_in`,0),53) AS `Week`,cast(`shop_hours`.`time_in` as date) AS `Date`,`contacts`.`contact_id` AS `ContactID`,concat(`contacts`.`last_name`,', ',`contacts`.`first_name`,' ',`contacts`.`middle_initial`) AS `Name`,round((hour(subtime(cast(`shop_hours`.`time_out` as time),cast(`shop_hours`.`time_in` as time))) + (minute(subtime(cast(`shop_hours`.`time_out` as time),cast(`shop_hours`.`time_in` as time))) / 60)),2) AS `Hours`,round((((hour(subtime(cast(`shop_hours`.`time_out` as time),cast(`shop_hours`.`time_in` as time))) + (minute(subtime(cast(`shop_hours`.`time_out` as time),cast(`shop_hours`.`time_in` as time))) / 60)) * 12) * 1.1),2) AS `Pay` from ((`shop_hours` left join `contacts` on((`shop_hours`.`contact_id` = `contacts`.`contact_id`))) left join `shop_user_roles` on((`shop_hours`.`shop_user_role` = `shop_user_roles`.`shop_user_role_id`))) where (`shop_user_roles`.`paid` = 1) order by `shop_hours`.`time_in` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_EmployeeHours_byMonth`
--

/*!50001 DROP TABLE IF EXISTS `view_EmployeeHours_byMonth`*/;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeHours_byMonth`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_EmployeeHours_byMonth` AS select `v`.`Year` AS `Year`,`v`.`Month` AS `Month`,`v`.`ContactID` AS `ContactID`,`v`.`Name` AS `Name`,sum(`v`.`Hours`) AS `Hours`,sum(`v`.`Pay`) AS `Pay` from `view_EmployeeHours` `v` group by `v`.`Year`,`v`.`Month`,`v`.`ContactID` order by `v`.`Year` desc,`v`.`Month` desc,`v`.`Name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_EmployeeHours_byMonth_WholeOper`
--

/*!50001 DROP TABLE IF EXISTS `view_EmployeeHours_byMonth_WholeOper`*/;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeHours_byMonth_WholeOper`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_EmployeeHours_byMonth_WholeOper` AS select `v`.`Year` AS `Year`,`v`.`Month` AS `Month`,sum(`v`.`Hours`) AS `Hours`,sum(`v`.`Pay`) AS `Pay` from `view_EmployeeHours_byMonth` `v` group by `v`.`Year`,`v`.`Month` order by `v`.`Year` desc,`v`.`Month` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_EmployeeHours_byWeek`
--

/*!50001 DROP TABLE IF EXISTS `view_EmployeeHours_byWeek`*/;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeHours_byWeek`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_EmployeeHours_byWeek` AS select `v`.`YearWeek` AS `Year`,`v`.`Week` AS `Week`,`v`.`ContactID` AS `ContactID`,`v`.`Name` AS `Name`,sum(`v`.`Hours`) AS `Hours`,sum(`v`.`Pay`) AS `Pay` from `view_EmployeeHours` `v` group by `v`.`YearWeek`,`v`.`Week`,`v`.`ContactID` order by `v`.`YearWeek` desc,`v`.`Week` desc,`v`.`Name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_EmployeeHours_byWeek_WholeOper`
--

/*!50001 DROP TABLE IF EXISTS `view_EmployeeHours_byWeek_WholeOper`*/;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeHours_byWeek_WholeOper`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_EmployeeHours_byWeek_WholeOper` AS select `v`.`Year` AS `Year`,`v`.`Week` AS `Week`,sum(`v`.`Hours`) AS `Hours`,sum(`v`.`Pay`) AS `Pay` from `view_EmployeeHours_byWeek` `v` group by `v`.`Year`,`v`.`Week` order by `v`.`Year` desc,`v`.`Week` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_EmployeeMetrics_BikesbyMonth`
--

/*!50001 DROP TABLE IF EXISTS `view_EmployeeMetrics_BikesbyMonth`*/;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeMetrics_BikesbyMonth`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_EmployeeMetrics_BikesbyMonth` AS select year(`t`.`date`) AS `Year`,month(`t`.`date`) AS `Month`,`t`.`sold_by` AS `contact_id`,sum(`t`.`amount`) AS `TotalValue`,round(avg(`t`.`amount`),0) AS `AverageValue`,count(`t`.`transaction_id`) AS `Count` from `transaction_log` `t` where (`t`.`transaction_type` = 'Metrics - Completed Mechanic Operation Bike') group by year(`t`.`date`),month(`t`.`date`),`t`.`sold_by` order by year(`t`.`date`) desc,month(`t`.`date`) desc,`t`.`sold_by` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_EmployeeMetrics_BikesbyWeek`
--

/*!50001 DROP TABLE IF EXISTS `view_EmployeeMetrics_BikesbyWeek`*/;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeMetrics_BikesbyWeek`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_EmployeeMetrics_BikesbyWeek` AS select year(`t`.`date`) AS `Year`,week(`t`.`date`,0) AS `Week`,`t`.`sold_by` AS `contact_id`,sum(`t`.`amount`) AS `TotalValue`,round(avg(`t`.`amount`),0) AS `AverageValue`,count(`t`.`transaction_id`) AS `Count` from `transaction_log` `t` where (`t`.`transaction_type` = 'Metrics - Completed Mechanic Operation Bike') group by year(`t`.`date`),week(`t`.`date`,0),`t`.`sold_by` order by year(`t`.`date`) desc,week(`t`.`date`,0) desc,`t`.`sold_by` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_EmployeeMetrics_TotalsByMonth`
--

/*!50001 DROP TABLE IF EXISTS `view_EmployeeMetrics_TotalsByMonth`*/;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeMetrics_TotalsByMonth`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_EmployeeMetrics_TotalsByMonth` AS select `eh`.`Year` AS `Year`,`eh`.`Month` AS `Month`,`eh`.`Name` AS `Name`,`eh`.`ContactID` AS `ContactID`,round(((`eb`.`TotalValue` + if(isnull(`ew`.`TotalValue`),0,`ew`.`TotalValue`)) / `eh`.`Pay`),1) AS `OutputValueVsPayRatio`,round((`eh`.`Hours` / `eb`.`Count`),1) AS `HoursPerBike`,`eb`.`Count` AS `NumBikes`,`eb`.`AverageValue` AS `AverageBikePrice`,`eb`.`TotalValue` AS `TotalValueBikes`,`ew`.`Count` AS `NumWheels`,`ew`.`AverageValue` AS `AverageWheelPrice`,`ew`.`TotalValue` AS `TotalValueWheels` from ((`view_EmployeeHours_byMonth` `eh` left join `view_EmployeeMetrics_BikesbyMonth` `eb` on(((`eh`.`ContactID` = `eb`.`contact_id`) and (`eh`.`Year` = `eb`.`Year`) and (`eh`.`Month` = `eb`.`Month`)))) left join `view_EmployeeMetrics_WheelsbyMonth` `ew` on(((`eh`.`ContactID` = `ew`.`contact_id`) and (`eh`.`Year` = `ew`.`Year`) and (`eh`.`Month` = `ew`.`Month`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_EmployeeMetrics_TotalsByWeek`
--

/*!50001 DROP TABLE IF EXISTS `view_EmployeeMetrics_TotalsByWeek`*/;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeMetrics_TotalsByWeek`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_EmployeeMetrics_TotalsByWeek` AS select `eh`.`Year` AS `Year`,`eh`.`Week` AS `Week`,`eh`.`Name` AS `Name`,`eh`.`ContactID` AS `ContactID`,round(((`eb`.`TotalValue` + if(isnull(`ew`.`TotalValue`),0,`ew`.`TotalValue`)) / `eh`.`Pay`),1) AS `OutputValueVsPayRatio`,round((`eh`.`Hours` / `eb`.`Count`),1) AS `HoursPerBike`,`eb`.`Count` AS `NumBikes`,`eb`.`AverageValue` AS `AverageBikePrice`,`eb`.`TotalValue` AS `TotalValueBikes`,`ew`.`Count` AS `NumWheels`,`ew`.`AverageValue` AS `AverageWheelPrice`,`ew`.`TotalValue` AS `TotalValueWheels` from ((`view_EmployeeHours_byWeek` `eh` left join `view_EmployeeMetrics_BikesbyWeek` `eb` on(((`eh`.`ContactID` = `eb`.`contact_id`) and (`eh`.`Year` = `eb`.`Year`) and (`eh`.`Week` = `eb`.`Week`)))) left join `view_EmployeeMetrics_WheelsbyWeek` `ew` on(((`eh`.`ContactID` = `ew`.`contact_id`) and (`eh`.`Year` = `ew`.`Year`) and (`eh`.`Week` = `ew`.`Week`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_EmployeeMetrics_WheelsbyMonth`
--

/*!50001 DROP TABLE IF EXISTS `view_EmployeeMetrics_WheelsbyMonth`*/;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeMetrics_WheelsbyMonth`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_EmployeeMetrics_WheelsbyMonth` AS select year(`t`.`date`) AS `Year`,month(`t`.`date`) AS `Month`,`t`.`sold_by` AS `contact_id`,sum(`t`.`amount`) AS `TotalValue`,round(avg(`t`.`amount`),0) AS `AverageValue`,count(`t`.`transaction_id`) AS `Count` from `transaction_log` `t` where (`t`.`transaction_type` = 'Metrics - Completed Mechanic Operation Wheel') group by year(`t`.`date`),month(`t`.`date`),`t`.`sold_by` order by year(`t`.`date`) desc,month(`t`.`date`) desc,`t`.`sold_by` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_EmployeeMetrics_WheelsbyWeek`
--

/*!50001 DROP TABLE IF EXISTS `view_EmployeeMetrics_WheelsbyWeek`*/;
/*!50001 DROP VIEW IF EXISTS `view_EmployeeMetrics_WheelsbyWeek`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_EmployeeMetrics_WheelsbyWeek` AS select year(`t`.`date`) AS `Year`,week(`t`.`date`,0) AS `Week`,`t`.`sold_by` AS `contact_id`,sum(`t`.`amount`) AS `TotalValue`,round(avg(`t`.`amount`),0) AS `AverageValue`,count(`t`.`transaction_id`) AS `Count` from `transaction_log` `t` where (`t`.`transaction_type` = 'Metrics - Completed Mechanic Operation Wheel') group by year(`t`.`date`),week(`t`.`date`,0),`t`.`sold_by` order by year(`t`.`date`) desc,week(`t`.`date`,0) desc,`t`.`sold_by` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_MechanicOperationMetrics_byMonth`
--

/*!50001 DROP TABLE IF EXISTS `view_MechanicOperationMetrics_byMonth`*/;
/*!50001 DROP VIEW IF EXISTS `view_MechanicOperationMetrics_byMonth`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_MechanicOperationMetrics_byMonth` AS select `Hours`.`Year` AS `Year`,`Hours`.`Month` AS `Month`,`Hours`.`Hours` AS `Hours`,`Hours`.`Pay` AS `Pay`,`Trans`.`NetSalesNewParts` AS `NetSalesNewParts`,`Trans`.`SalesUsedParts` AS `SalesUsedParts`,`Trans`.`ValueBikesFixed` AS `ValueBikesFixed`,`Trans`.`ValueWheelsFixed` AS `ValueWheelsFixed`,`Trans`.`ValueNewPartsOnBikes` AS `ValueNewPartsOnBikes`,((((`Trans`.`NetSalesNewParts` + `Trans`.`SalesUsedParts`) + `Trans`.`ValueBikesFixed`) + `Trans`.`ValueWheelsFixed`) - (`Hours`.`Pay` + `Trans`.`ValueNewPartsOnBikes`)) AS `EstimatedNetIncome`,`Trans`.`TotalBikesFixed` AS `TotalBikesFixed`,`Trans`.`TotalWheelsFixed` AS `TotalWheelsFixed`,round((`Hours`.`Hours` / `Trans`.`TotalBikesFixed`),1) AS `HoursPerBike`,round((`Trans`.`ValueBikesFixed` / `Trans`.`TotalBikesFixed`),1) AS `AverageBikeValue`,`Trans`.`SalesBikes` AS `SalesBikes`,`Trans`.`TotalBikesSold` AS `TotalBikesSold` from (`view_EmployeeHours_byMonth_WholeOper` `Hours` left join `view_Transactions_MechOper_byMonth_pvTbl` `Trans` on(((`Hours`.`Year` = `Trans`.`Year`) and (`Hours`.`Month` = `Trans`.`Month`)))) order by `Hours`.`Year` desc,`Hours`.`Month` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_MechanicOperationMetrics_byWeek`
--

/*!50001 DROP TABLE IF EXISTS `view_MechanicOperationMetrics_byWeek`*/;
/*!50001 DROP VIEW IF EXISTS `view_MechanicOperationMetrics_byWeek`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_MechanicOperationMetrics_byWeek` AS select `Hours`.`Year` AS `Year`,`Hours`.`Week` AS `Week`,`Hours`.`Hours` AS `Hours`,`Hours`.`Pay` AS `Pay`,`Trans`.`NetSalesNewParts` AS `NetSalesNewParts`,`Trans`.`SalesUsedParts` AS `SalesUsedParts`,`Trans`.`ValueBikesFixed` AS `ValueBikesFixed`,`Trans`.`ValueWheelsFixed` AS `ValueWheelsFixed`,`Trans`.`ValueNewPartsOnBikes` AS `ValueNewPartsOnBikes`,((((`Trans`.`NetSalesNewParts` + `Trans`.`SalesUsedParts`) + `Trans`.`ValueBikesFixed`) + `Trans`.`ValueWheelsFixed`) - (`Hours`.`Pay` + `Trans`.`ValueNewPartsOnBikes`)) AS `EstimatedNetIncome`,`Trans`.`TotalBikesFixed` AS `TotalBikesFixed`,`Trans`.`TotalWheelsFixed` AS `TotalWheelsFixed`,round((`Hours`.`Hours` / `Trans`.`TotalBikesFixed`),1) AS `HoursPerBike`,round((`Trans`.`ValueBikesFixed` / `Trans`.`TotalBikesFixed`),1) AS `AverageBikeValue`,`Trans`.`SalesBikes` AS `SalesBikes`,`Trans`.`TotalBikesSold` AS `TotalBikesSold` from (`view_EmployeeHours_byWeek_WholeOper` `Hours` left join `view_Transactions_MechOper_byWeek_pvTbl` `Trans` on(((`Hours`.`Year` = `Trans`.`Year`) and (`Hours`.`Week` = `Trans`.`Week`)))) order by `Hours`.`Year` desc,`Hours`.`Week` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_Transactions`
--

/*!50001 DROP TABLE IF EXISTS `view_Transactions`*/;
/*!50001 DROP VIEW IF EXISTS `view_Transactions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_Transactions` AS select year(`t`.`date`) AS `Year`,month(`t`.`date`) AS `Month`,if((week(`t`.`date`,0) <> 0),year(`t`.`date`),(year(`t`.`date`) - 1)) AS `YearWeek`,if((week(`t`.`date`,0) <> 0),week(`t`.`date`,0),53) AS `Week`,`t`.`transaction_type` AS `TransactionType`,round(`t`.`amount`,2) AS `Total`,`transaction_types`.`accounting_group` AS `AccountingGroup`,if((`shops`.`shop_type` = 'Mechanic Operation Shop'),'Mechanic Operation Shop','Volunteer Run Shop') AS `ShopType` from ((`transaction_log` `t` left join `transaction_types` on((`t`.`transaction_type` = `transaction_types`.`transaction_type_id`))) left join `shops` on((`t`.`shop_id` = `shops`.`shop_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_Transactions_MechOper_byMonth`
--

/*!50001 DROP TABLE IF EXISTS `view_Transactions_MechOper_byMonth`*/;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_MechOper_byMonth`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_Transactions_MechOper_byMonth` AS select `v`.`Year` AS `Year`,`v`.`Month` AS `Month`,`v`.`TransactionType` AS `TransactionType`,sum(`v`.`Total`) AS `Total`,count(`v`.`Total`) AS `Count`,`v`.`AccountingGroup` AS `AccountingGroup`,`v`.`ShopType` AS `ShopType` from `view_Transactions` `v` where (`v`.`ShopType` = 'Mechanic Operation Shop') group by `v`.`Year`,`v`.`Month`,`v`.`TransactionType` order by `v`.`Year` desc,`v`.`Month` desc,`v`.`TransactionType` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_Transactions_MechOper_byMonth_pvTbl`
--

/*!50001 DROP TABLE IF EXISTS `view_Transactions_MechOper_byMonth_pvTbl`*/;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_MechOper_byMonth_pvTbl`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_Transactions_MechOper_byMonth_pvTbl` AS select `v`.`Year` AS `Year`,`v`.`Month` AS `Month`,round(max(if((`v2`.`TransactionType` = 'Sale - New Parts'),(`v2`.`Total` / 2),0)),2) AS `NetSalesNewParts`,max(if((`v2`.`TransactionType` = 'Sale - Used Parts'),`v2`.`Total`,0)) AS `SalesUsedParts`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Total`,0)) AS `SalesBikes`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Bike'),`v2`.`Total`,0)) AS `ValueBikesFixed`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Wheel'),`v2`.`Total`,0)) AS `ValueWheelsFixed`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Count`,0)) AS `TotalBikesSold`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Bike'),`v2`.`Count`,0)) AS `TotalBikesFixed`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Wheel'),`v2`.`Count`,0)) AS `TotalWheelsFixed`,max(if((`v2`.`TransactionType` = 'Metrics - New Parts on a Completed Bike'),`v2`.`Total`,0)) AS `ValueNewPartsOnBikes` from (`view_Transactions_MechOper_byMonth` `v` left join `view_Transactions_MechOper_byMonth` `v2` on(((`v`.`Year` = `v2`.`Year`) and (`v`.`Month` = `v2`.`Month`)))) group by `v`.`Year`,`v`.`Month` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_Transactions_MechOper_byWeek`
--

/*!50001 DROP TABLE IF EXISTS `view_Transactions_MechOper_byWeek`*/;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_MechOper_byWeek`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_Transactions_MechOper_byWeek` AS select `v`.`YearWeek` AS `Year`,`v`.`Week` AS `Week`,`v`.`TransactionType` AS `TransactionType`,sum(`v`.`Total`) AS `Total`,count(`v`.`Total`) AS `Count`,`v`.`AccountingGroup` AS `AccountingGroup`,`v`.`ShopType` AS `ShopType` from `view_Transactions` `v` where (`v`.`ShopType` = 'Mechanic Operation Shop') group by `v`.`YearWeek`,`v`.`Week`,`v`.`TransactionType` order by `v`.`YearWeek`,`v`.`Week`,`v`.`TransactionType` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_Transactions_MechOper_byWeek_pvTbl`
--

/*!50001 DROP TABLE IF EXISTS `view_Transactions_MechOper_byWeek_pvTbl`*/;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_MechOper_byWeek_pvTbl`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_Transactions_MechOper_byWeek_pvTbl` AS select `v`.`Year` AS `Year`,`v`.`Week` AS `Week`,round(max(if((`v2`.`TransactionType` = 'Sale - New Parts'),(`v2`.`Total` / 2),0)),2) AS `NetSalesNewParts`,max(if((`v2`.`TransactionType` = 'Sale - Used Parts'),`v2`.`Total`,0)) AS `SalesUsedParts`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Total`,0)) AS `SalesBikes`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Bike'),`v2`.`Total`,0)) AS `ValueBikesFixed`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Wheel'),`v2`.`Total`,0)) AS `ValueWheelsFixed`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Count`,0)) AS `TotalBikesSold`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Bike'),`v2`.`Count`,0)) AS `TotalBikesFixed`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Wheel'),`v2`.`Count`,0)) AS `TotalWheelsFixed`,max(if((`v2`.`TransactionType` = 'Metrics - New Parts on a Completed Bike'),`v2`.`Total`,0)) AS `ValueNewPartsOnBikes` from (`view_Transactions_MechOper_byWeek` `v` left join `view_Transactions_MechOper_byWeek` `v2` on(((`v`.`Year` = `v2`.`Year`) and (`v`.`Week` = `v2`.`Week`)))) group by `v`.`Year`,`v`.`Week` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_Transactions_VolRunShop_byMonth`
--

/*!50001 DROP TABLE IF EXISTS `view_Transactions_VolRunShop_byMonth`*/;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_VolRunShop_byMonth`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_Transactions_VolRunShop_byMonth` AS select `v`.`Year` AS `Year`,`v`.`Month` AS `Month`,`v`.`TransactionType` AS `TransactionType`,sum(`v`.`Total`) AS `Total`,count(`v`.`Total`) AS `Count`,`v`.`AccountingGroup` AS `AccountingGroup`,`v`.`ShopType` AS `ShopType` from `view_Transactions` `v` where (`v`.`ShopType` = 'Volunteer Run Shop') group by `v`.`Year`,`v`.`Month`,`v`.`TransactionType` order by `v`.`Year`,`v`.`Month`,`v`.`TransactionType` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_Transactions_VolRunShop_byMonth_pvTbl`
--

/*!50001 DROP TABLE IF EXISTS `view_Transactions_VolRunShop_byMonth_pvTbl`*/;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_VolRunShop_byMonth_pvTbl`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_Transactions_VolRunShop_byMonth_pvTbl` AS select `v`.`Year` AS `Year`,`v`.`Month` AS `Month`,round(max(if((`v2`.`TransactionType` = 'Sale - New Parts'),(`v2`.`Total` / 2),0)),2) AS `NetSalesNewParts`,max(if((`v2`.`TransactionType` = 'Sale - Used Parts'),`v2`.`Total`,0)) AS `SalesUsedParts`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Total`,0)) AS `SalesBikes`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Count`,0)) AS `TotalBikesSold` from (`view_Transactions_VolRunShop_byMonth` `v` left join `view_Transactions_VolRunShop_byMonth` `v2` on(((`v`.`Year` = `v2`.`Year`) and (`v`.`Month` = `v2`.`Month`)))) group by `v`.`Year`,`v`.`Month` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_Transactions_VolRunShop_byWeek`
--

/*!50001 DROP TABLE IF EXISTS `view_Transactions_VolRunShop_byWeek`*/;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_VolRunShop_byWeek`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_Transactions_VolRunShop_byWeek` AS select `v`.`YearWeek` AS `Year`,`v`.`Week` AS `Week`,`v`.`TransactionType` AS `TransactionType`,sum(`v`.`Total`) AS `Total`,count(`v`.`Total`) AS `Count`,`v`.`AccountingGroup` AS `AccountingGroup`,`v`.`ShopType` AS `ShopType` from `view_Transactions` `v` where (`v`.`ShopType` = 'Volunteer Run Shop') group by `v`.`YearWeek`,`v`.`Week`,`v`.`TransactionType` order by `v`.`YearWeek`,`v`.`Week`,`v`.`TransactionType` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_Transactions_VolRunShop_byWeek_pvTbl`
--

/*!50001 DROP TABLE IF EXISTS `view_Transactions_VolRunShop_byWeek_pvTbl`*/;
/*!50001 DROP VIEW IF EXISTS `view_Transactions_VolRunShop_byWeek_pvTbl`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_Transactions_VolRunShop_byWeek_pvTbl` AS select `v`.`Year` AS `Year`,`v`.`Week` AS `Week`,round(max(if((`v2`.`TransactionType` = 'Sale - New Parts'),(`v2`.`Total` / 2),0)),2) AS `NetSalesNewParts`,max(if((`v2`.`TransactionType` = 'Sale - Used Parts'),`v2`.`Total`,0)) AS `SalesUsedParts`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Total`,0)) AS `SalesBikes`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Count`,0)) AS `TotalBikesSold` from (`view_Transactions_VolRunShop_byWeek` `v` left join `view_Transactions_VolRunShop_byWeek` `v2` on(((`v`.`Year` = `v2`.`Year`) and (`v`.`Week` = `v2`.`Week`)))) group by `v`.`Year`,`v`.`Week` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_sales_by_week`
--

/*!50001 DROP TABLE IF EXISTS `view_sales_by_week`*/;
/*!50001 DROP VIEW IF EXISTS `view_sales_by_week`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER */
/*!50001 VIEW `view_sales_by_week` AS select if((week(`t`.`date`,0) <> 0),year(`t`.`date`),(year(`t`.`date`) - 1)) AS `Year`,if((week(`t`.`date`,0) <> 0),week(`t`.`date`,0),53) AS `Week`,`t`.`transaction_type` AS `TransactionType`,round(sum(`t`.`amount`),2) AS `Total`,count(`t`.`transaction_id`) AS `CountOfTrans`,`transaction_types`.`accounting_group` AS `AccountingGroup` from ((`transaction_log` `t` left join `transaction_types` on((`t`.`transaction_type` = `transaction_types`.`transaction_type_id`))) left join `shops` on((`t`.`shop_id` = `shops`.`shop_id`))) where (`shops`.`shop_type` = 'Mechanic Operation Shop') group by `transaction_types`.`accounting_group`,`t`.`transaction_type`,year(`t`.`date`),quarter(`t`.`date`),month(`t`.`date`) order by if((week(`t`.`date`,0) <> 0),year(`t`.`date`),(year(`t`.`date`) - 1)) desc,if((week(`t`.`date`,0) <> 0),week(`t`.`date`,0),53) desc,`transaction_types`.`accounting_group`,`t`.`transaction_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-07-13 19:43:45
