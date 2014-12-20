-- phpMyAdmin SQL Dump
-- version 3.3.10.4
-- http://www.phpmyadmin.net
--
-- Host: mysql.ybdb.austinyellowbike.org
-- Generation Time: Jul 24, 2014 at 06:43 AM
-- Server version: 5.1.56
-- PHP Version: 5.4.20

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ybdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE IF NOT EXISTS `contacts` (
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
  PRIMARY KEY (`contact_id`),
  KEY `location_type` (`location_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 5120 kB' AUTO_INCREMENT=17010 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `project_id` varchar(50) NOT NULL DEFAULT '',
  `date_established` date NOT NULL DEFAULT '0000-00-00',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `public` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sale_log`
--

CREATE TABLE IF NOT EXISTS `sale_log` (
  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sale_type` varchar(45) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `amount` float NOT NULL DEFAULT '0',
  `sold_by` varchar(45) NOT NULL DEFAULT '',
  `sold_to` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE IF NOT EXISTS `shops` (
  `shop_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `shop_location` varchar(45) NOT NULL DEFAULT '',
  `shop_type` varchar(45) NOT NULL DEFAULT '',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_id`),
  KEY `shop_type` (`shop_type`),
  KEY `shop_location` (`shop_location`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2729 ;

-- --------------------------------------------------------

--
-- Table structure for table `shop_hours`
--

CREATE TABLE IF NOT EXISTS `shop_hours` (
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
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 4096 kB; (`contact_id`) REFER `nwilkes_ybdb/con' AUTO_INCREMENT=51328 ;

-- --------------------------------------------------------

--
-- Table structure for table `shop_locations`
--

CREATE TABLE IF NOT EXISTS `shop_locations` (
  `shop_location_id` varchar(30) NOT NULL DEFAULT '',
  `date_established` date NOT NULL DEFAULT '0000-00-00',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shop_types`
--

CREATE TABLE IF NOT EXISTS `shop_types` (
  `shop_type_id` varchar(30) NOT NULL DEFAULT '',
  `list_order` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shop_user_roles`
--

CREATE TABLE IF NOT EXISTS `shop_user_roles` (
  `shop_user_role_id` varchar(45) NOT NULL DEFAULT '',
  `hours_rank` int(10) unsigned NOT NULL DEFAULT '0',
  `volunteer` tinyint(1) NOT NULL DEFAULT '0',
  `sales` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `paid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_log`
--

CREATE TABLE IF NOT EXISTS `transaction_log` (
  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_startstorage` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `transaction_type` varchar(45) NOT NULL DEFAULT '',
  `amount` float DEFAULT '0',
  `description` varchar(200) DEFAULT NULL,
  `sold_to` int(10) unsigned DEFAULT NULL,
  `sold_by` int(10) unsigned DEFAULT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`transaction_id`),
  KEY `transaction_type` (`transaction_type`),
  KEY `sold_to` (`sold_to`),
  KEY `sold_by` (`sold_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13244 ;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_types`
--

CREATE TABLE IF NOT EXISTS `transaction_types` (
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
  `show_soldto_location` tinyint(1) NOT NULL DEFAULT '0',
  `fieldname_description` varchar(45) NOT NULL,
  `accounting_group` varchar(45) NOT NULL,
  PRIMARY KEY (`transaction_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_EmployeeHours`
--
CREATE TABLE IF NOT EXISTS `view_EmployeeHours` (
`Year` int(4)
,`Month` int(2)
,`YearWeek` int(5)
,`Week` int(2)
,`Date` date
,`ContactID` int(10) unsigned
,`Name` varchar(45)
,`Hours` decimal(5,2)
,`Pay` decimal(8,2)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_EmployeeHours_byMonth`
--
CREATE TABLE IF NOT EXISTS `view_EmployeeHours_byMonth` (
`Year` int(4)
,`Month` int(2)
,`ContactID` int(10) unsigned
,`Name` varchar(45)
,`Hours` decimal(27,2)
,`Pay` decimal(30,2)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_EmployeeHours_byMonth_WholeOper`
--
CREATE TABLE IF NOT EXISTS `view_EmployeeHours_byMonth_WholeOper` (
`Year` int(4)
,`Month` int(2)
,`Hours` decimal(49,2)
,`Pay` decimal(52,2)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_EmployeeHours_byWeek`
--
CREATE TABLE IF NOT EXISTS `view_EmployeeHours_byWeek` (
`Year` int(5)
,`Week` int(2)
,`ContactID` int(10) unsigned
,`Name` varchar(45)
,`Hours` decimal(27,2)
,`Pay` decimal(30,2)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_EmployeeHours_byWeek_WholeOper`
--
CREATE TABLE IF NOT EXISTS `view_EmployeeHours_byWeek_WholeOper` (
`Year` int(5)
,`Week` int(2)
,`Hours` decimal(49,2)
,`Pay` decimal(52,2)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_EmployeeMetrics_BikesbyMonth`
--
CREATE TABLE IF NOT EXISTS `view_EmployeeMetrics_BikesbyMonth` (
`Year` int(4)
,`Month` int(2)
,`contact_id` int(10) unsigned
,`TotalValue` double
,`AverageValue` double(17,0)
,`Count` bigint(21)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_EmployeeMetrics_BikesbyWeek`
--
CREATE TABLE IF NOT EXISTS `view_EmployeeMetrics_BikesbyWeek` (
`Year` int(4)
,`Week` int(2)
,`contact_id` int(10) unsigned
,`TotalValue` double
,`AverageValue` double(17,0)
,`Count` bigint(21)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_EmployeeMetrics_TotalsByMonth`
--
CREATE TABLE IF NOT EXISTS `view_EmployeeMetrics_TotalsByMonth` (
`Year` int(4)
,`Month` int(2)
,`Name` varchar(45)
,`ContactID` int(10) unsigned
,`OutputValueVsPayRatio` double(18,1)
,`HoursPerBike` decimal(27,1)
,`NumBikes` bigint(21)
,`AverageBikePrice` double(17,0)
,`TotalValueBikes` double
,`NumWheels` bigint(21)
,`AverageWheelPrice` double(17,0)
,`TotalValueWheels` double
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_EmployeeMetrics_TotalsByWeek`
--
CREATE TABLE IF NOT EXISTS `view_EmployeeMetrics_TotalsByWeek` (
`Year` int(5)
,`Week` int(2)
,`Name` varchar(45)
,`ContactID` int(10) unsigned
,`OutputValueVsPayRatio` double(18,1)
,`HoursPerBike` decimal(27,1)
,`NumBikes` bigint(21)
,`AverageBikePrice` double(17,0)
,`TotalValueBikes` double
,`NumWheels` bigint(21)
,`AverageWheelPrice` double(17,0)
,`TotalValueWheels` double
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_EmployeeMetrics_WheelsbyMonth`
--
CREATE TABLE IF NOT EXISTS `view_EmployeeMetrics_WheelsbyMonth` (
`Year` int(4)
,`Month` int(2)
,`contact_id` int(10) unsigned
,`TotalValue` double
,`AverageValue` double(17,0)
,`Count` bigint(21)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_EmployeeMetrics_WheelsbyWeek`
--
CREATE TABLE IF NOT EXISTS `view_EmployeeMetrics_WheelsbyWeek` (
`Year` int(4)
,`Week` int(2)
,`contact_id` int(10) unsigned
,`TotalValue` double
,`AverageValue` double(17,0)
,`Count` bigint(21)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_MechanicOperationMetrics_byMonth`
--
CREATE TABLE IF NOT EXISTS `view_MechanicOperationMetrics_byMonth` (
`Year` int(4)
,`Month` int(2)
,`Hours` decimal(49,2)
,`Pay` decimal(52,2)
,`NetSalesNewParts` double(19,2)
,`SalesUsedParts` double(19,2)
,`ValueBikesFixed` double(19,2)
,`ValueWheelsFixed` double(19,2)
,`ValueNewPartsOnBikes` double(19,2)
,`EstimatedNetIncome` double(19,2)
,`TotalBikesFixed` bigint(20)
,`TotalWheelsFixed` bigint(20)
,`HoursPerBike` decimal(49,1)
,`AverageBikeValue` double(18,1)
,`SalesBikes` double(19,2)
,`TotalBikesSold` bigint(20)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_MechanicOperationMetrics_byWeek`
--
CREATE TABLE IF NOT EXISTS `view_MechanicOperationMetrics_byWeek` (
`Year` int(5)
,`Week` int(2)
,`Hours` decimal(49,2)
,`Pay` decimal(52,2)
,`NetSalesNewParts` double(19,2)
,`SalesUsedParts` double(19,2)
,`ValueBikesFixed` double(19,2)
,`ValueWheelsFixed` double(19,2)
,`ValueNewPartsOnBikes` double(19,2)
,`EstimatedNetIncome` double(19,2)
,`TotalBikesFixed` bigint(20)
,`TotalWheelsFixed` bigint(20)
,`HoursPerBike` decimal(49,1)
,`AverageBikeValue` double(18,1)
,`SalesBikes` double(19,2)
,`TotalBikesSold` bigint(20)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_sales_by_week`
--
CREATE TABLE IF NOT EXISTS `view_sales_by_week` (
`Year` int(5)
,`Week` int(2)
,`TransactionType` varchar(45)
,`Total` double(19,2)
,`CountOfTrans` bigint(21)
,`AccountingGroup` varchar(45)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_Transactions`
--
CREATE TABLE IF NOT EXISTS `view_Transactions` (
`Year` int(4)
,`Month` int(2)
,`YearWeek` int(5)
,`Week` int(2)
,`TransactionType` varchar(45)
,`Total` double(19,2)
,`AccountingGroup` varchar(45)
,`ShopType` varchar(23)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_Transactions_MechOper_byMonth`
--
CREATE TABLE IF NOT EXISTS `view_Transactions_MechOper_byMonth` (
`Year` int(4)
,`Month` int(2)
,`TransactionType` varchar(45)
,`Total` double(19,2)
,`Count` bigint(21)
,`AccountingGroup` varchar(45)
,`ShopType` varchar(23)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_Transactions_MechOper_byMonth_pvTbl`
--
CREATE TABLE IF NOT EXISTS `view_Transactions_MechOper_byMonth_pvTbl` (
`Year` int(4)
,`Month` int(2)
,`NetSalesNewParts` double(19,2)
,`SalesUsedParts` double(19,2)
,`SalesBikes` double(19,2)
,`ValueBikesFixed` double(19,2)
,`ValueWheelsFixed` double(19,2)
,`TotalBikesSold` bigint(20)
,`TotalBikesFixed` bigint(20)
,`TotalWheelsFixed` bigint(20)
,`ValueNewPartsOnBikes` double(19,2)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_Transactions_MechOper_byWeek`
--
CREATE TABLE IF NOT EXISTS `view_Transactions_MechOper_byWeek` (
`Year` int(5)
,`Week` int(2)
,`TransactionType` varchar(45)
,`Total` double(19,2)
,`Count` bigint(21)
,`AccountingGroup` varchar(45)
,`ShopType` varchar(23)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_Transactions_MechOper_byWeek_pvTbl`
--
CREATE TABLE IF NOT EXISTS `view_Transactions_MechOper_byWeek_pvTbl` (
`Year` int(5)
,`Week` int(2)
,`NetSalesNewParts` double(19,2)
,`SalesUsedParts` double(19,2)
,`SalesBikes` double(19,2)
,`ValueBikesFixed` double(19,2)
,`ValueWheelsFixed` double(19,2)
,`TotalBikesSold` bigint(20)
,`TotalBikesFixed` bigint(20)
,`TotalWheelsFixed` bigint(20)
,`ValueNewPartsOnBikes` double(19,2)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_Transactions_VolRunShop_byMonth`
--
CREATE TABLE IF NOT EXISTS `view_Transactions_VolRunShop_byMonth` (
`Year` int(4)
,`Month` int(2)
,`TransactionType` varchar(45)
,`Total` double(19,2)
,`Count` bigint(21)
,`AccountingGroup` varchar(45)
,`ShopType` varchar(23)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_Transactions_VolRunShop_byMonth_pvTbl`
--
CREATE TABLE IF NOT EXISTS `view_Transactions_VolRunShop_byMonth_pvTbl` (
`Year` int(4)
,`Month` int(2)
,`NetSalesNewParts` double(19,2)
,`SalesUsedParts` double(19,2)
,`SalesBikes` double(19,2)
,`TotalBikesSold` bigint(20)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_Transactions_VolRunShop_byWeek`
--
CREATE TABLE IF NOT EXISTS `view_Transactions_VolRunShop_byWeek` (
`Year` int(5)
,`Week` int(2)
,`TransactionType` varchar(45)
,`Total` double(19,2)
,`Count` bigint(21)
,`AccountingGroup` varchar(45)
,`ShopType` varchar(23)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_Transactions_VolRunShop_byWeek_pvTbl`
--
CREATE TABLE IF NOT EXISTS `view_Transactions_VolRunShop_byWeek_pvTbl` (
`Year` int(5)
,`Week` int(2)
,`NetSalesNewParts` double(19,2)
,`SalesUsedParts` double(19,2)
,`SalesBikes` double(19,2)
,`TotalBikesSold` bigint(20)
);
-- --------------------------------------------------------

--
-- Structure for view `view_EmployeeHours`
--
DROP TABLE IF EXISTS `view_EmployeeHours`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_EmployeeHours` AS select year(`shop_hours`.`time_in`) AS `Year`,month(`shop_hours`.`time_in`) AS `Month`,if((week(`shop_hours`.`time_in`,0) <> 0),year(`shop_hours`.`time_in`),(year(`shop_hours`.`time_in`) - 1)) AS `YearWeek`,if((week(`shop_hours`.`time_in`,0) <> 0),week(`shop_hours`.`time_in`,0),53) AS `Week`,cast(`shop_hours`.`time_in` as date) AS `Date`,`contacts`.`contact_id` AS `ContactID`,concat(`contacts`.`last_name`,', ',`contacts`.`first_name`,' ',`contacts`.`middle_initial`) AS `Name`,round((hour(subtime(cast(`shop_hours`.`time_out` as time),cast(`shop_hours`.`time_in` as time))) + (minute(subtime(cast(`shop_hours`.`time_out` as time),cast(`shop_hours`.`time_in` as time))) / 60)),2) AS `Hours`,round((((hour(subtime(cast(`shop_hours`.`time_out` as time),cast(`shop_hours`.`time_in` as time))) + (minute(subtime(cast(`shop_hours`.`time_out` as time),cast(`shop_hours`.`time_in` as time))) / 60)) * 12) * 1.1),2) AS `Pay` from ((`shop_hours` left join `contacts` on((`shop_hours`.`contact_id` = `contacts`.`contact_id`))) left join `shop_user_roles` on((`shop_hours`.`shop_user_role` = `shop_user_roles`.`shop_user_role_id`))) where (`shop_user_roles`.`paid` = 1) order by `shop_hours`.`time_in` desc;

-- --------------------------------------------------------

--
-- Structure for view `view_EmployeeHours_byMonth`
--
DROP TABLE IF EXISTS `view_EmployeeHours_byMonth`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_EmployeeHours_byMonth` AS select `v`.`Year` AS `Year`,`v`.`Month` AS `Month`,`v`.`ContactID` AS `ContactID`,`v`.`Name` AS `Name`,sum(`v`.`Hours`) AS `Hours`,sum(`v`.`Pay`) AS `Pay` from `view_EmployeeHours` `v` group by `v`.`Year`,`v`.`Month`,`v`.`ContactID` order by `v`.`Year` desc,`v`.`Month` desc,`v`.`Name`;

-- --------------------------------------------------------

--
-- Structure for view `view_EmployeeHours_byMonth_WholeOper`
--
DROP TABLE IF EXISTS `view_EmployeeHours_byMonth_WholeOper`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_EmployeeHours_byMonth_WholeOper` AS select `v`.`Year` AS `Year`,`v`.`Month` AS `Month`,sum(`v`.`Hours`) AS `Hours`,sum(`v`.`Pay`) AS `Pay` from `view_EmployeeHours_byMonth` `v` group by `v`.`Year`,`v`.`Month` order by `v`.`Year` desc,`v`.`Month` desc;

-- --------------------------------------------------------

--
-- Structure for view `view_EmployeeHours_byWeek`
--
DROP TABLE IF EXISTS `view_EmployeeHours_byWeek`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_EmployeeHours_byWeek` AS select `v`.`YearWeek` AS `Year`,`v`.`Week` AS `Week`,`v`.`ContactID` AS `ContactID`,`v`.`Name` AS `Name`,sum(`v`.`Hours`) AS `Hours`,sum(`v`.`Pay`) AS `Pay` from `view_EmployeeHours` `v` group by `v`.`YearWeek`,`v`.`Week`,`v`.`ContactID` order by `v`.`YearWeek` desc,`v`.`Week` desc,`v`.`Name`;

-- --------------------------------------------------------

--
-- Structure for view `view_EmployeeHours_byWeek_WholeOper`
--
DROP TABLE IF EXISTS `view_EmployeeHours_byWeek_WholeOper`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_EmployeeHours_byWeek_WholeOper` AS select `v`.`Year` AS `Year`,`v`.`Week` AS `Week`,sum(`v`.`Hours`) AS `Hours`,sum(`v`.`Pay`) AS `Pay` from `view_EmployeeHours_byWeek` `v` group by `v`.`Year`,`v`.`Week` order by `v`.`Year` desc,`v`.`Week` desc;

-- --------------------------------------------------------

--
-- Structure for view `view_EmployeeMetrics_BikesbyMonth`
--
DROP TABLE IF EXISTS `view_EmployeeMetrics_BikesbyMonth`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_EmployeeMetrics_BikesbyMonth` AS select year(`t`.`date`) AS `Year`,month(`t`.`date`) AS `Month`,`t`.`sold_by` AS `contact_id`,sum(`t`.`amount`) AS `TotalValue`,round(avg(`t`.`amount`),0) AS `AverageValue`,count(`t`.`transaction_id`) AS `Count` from `transaction_log` `t` where (`t`.`transaction_type` = 'Metrics - Completed Mechanic Operation Bike') group by year(`t`.`date`),month(`t`.`date`),`t`.`sold_by` order by year(`t`.`date`) desc,month(`t`.`date`) desc,`t`.`sold_by`;

-- --------------------------------------------------------

--
-- Structure for view `view_EmployeeMetrics_BikesbyWeek`
--
DROP TABLE IF EXISTS `view_EmployeeMetrics_BikesbyWeek`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_EmployeeMetrics_BikesbyWeek` AS select year(`t`.`date`) AS `Year`,week(`t`.`date`,0) AS `Week`,`t`.`sold_by` AS `contact_id`,sum(`t`.`amount`) AS `TotalValue`,round(avg(`t`.`amount`),0) AS `AverageValue`,count(`t`.`transaction_id`) AS `Count` from `transaction_log` `t` where (`t`.`transaction_type` = 'Metrics - Completed Mechanic Operation Bike') group by year(`t`.`date`),week(`t`.`date`,0),`t`.`sold_by` order by year(`t`.`date`) desc,week(`t`.`date`,0) desc,`t`.`sold_by`;

-- --------------------------------------------------------

--
-- Structure for view `view_EmployeeMetrics_TotalsByMonth`
--
DROP TABLE IF EXISTS `view_EmployeeMetrics_TotalsByMonth`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_EmployeeMetrics_TotalsByMonth` AS select `eh`.`Year` AS `Year`,`eh`.`Month` AS `Month`,`eh`.`Name` AS `Name`,`eh`.`ContactID` AS `ContactID`,round(((`eb`.`TotalValue` + if(isnull(`ew`.`TotalValue`),0,`ew`.`TotalValue`)) / `eh`.`Pay`),1) AS `OutputValueVsPayRatio`,round((`eh`.`Hours` / `eb`.`Count`),1) AS `HoursPerBike`,`eb`.`Count` AS `NumBikes`,`eb`.`AverageValue` AS `AverageBikePrice`,`eb`.`TotalValue` AS `TotalValueBikes`,`ew`.`Count` AS `NumWheels`,`ew`.`AverageValue` AS `AverageWheelPrice`,`ew`.`TotalValue` AS `TotalValueWheels` from ((`view_EmployeeHours_byMonth` `eh` left join `view_EmployeeMetrics_BikesbyMonth` `eb` on(((`eh`.`ContactID` = `eb`.`contact_id`) and (`eh`.`Year` = `eb`.`Year`) and (`eh`.`Month` = `eb`.`Month`)))) left join `view_EmployeeMetrics_WheelsbyMonth` `ew` on(((`eh`.`ContactID` = `ew`.`contact_id`) and (`eh`.`Year` = `ew`.`Year`) and (`eh`.`Month` = `ew`.`Month`))));

-- --------------------------------------------------------

--
-- Structure for view `view_EmployeeMetrics_TotalsByWeek`
--
DROP TABLE IF EXISTS `view_EmployeeMetrics_TotalsByWeek`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_EmployeeMetrics_TotalsByWeek` AS select `eh`.`Year` AS `Year`,`eh`.`Week` AS `Week`,`eh`.`Name` AS `Name`,`eh`.`ContactID` AS `ContactID`,round(((`eb`.`TotalValue` + if(isnull(`ew`.`TotalValue`),0,`ew`.`TotalValue`)) / `eh`.`Pay`),1) AS `OutputValueVsPayRatio`,round((`eh`.`Hours` / `eb`.`Count`),1) AS `HoursPerBike`,`eb`.`Count` AS `NumBikes`,`eb`.`AverageValue` AS `AverageBikePrice`,`eb`.`TotalValue` AS `TotalValueBikes`,`ew`.`Count` AS `NumWheels`,`ew`.`AverageValue` AS `AverageWheelPrice`,`ew`.`TotalValue` AS `TotalValueWheels` from ((`view_EmployeeHours_byWeek` `eh` left join `view_EmployeeMetrics_BikesbyWeek` `eb` on(((`eh`.`ContactID` = `eb`.`contact_id`) and (`eh`.`Year` = `eb`.`Year`) and (`eh`.`Week` = `eb`.`Week`)))) left join `view_EmployeeMetrics_WheelsbyWeek` `ew` on(((`eh`.`ContactID` = `ew`.`contact_id`) and (`eh`.`Year` = `ew`.`Year`) and (`eh`.`Week` = `ew`.`Week`))));

-- --------------------------------------------------------

--
-- Structure for view `view_EmployeeMetrics_WheelsbyMonth`
--
DROP TABLE IF EXISTS `view_EmployeeMetrics_WheelsbyMonth`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_EmployeeMetrics_WheelsbyMonth` AS select year(`t`.`date`) AS `Year`,month(`t`.`date`) AS `Month`,`t`.`sold_by` AS `contact_id`,sum(`t`.`amount`) AS `TotalValue`,round(avg(`t`.`amount`),0) AS `AverageValue`,count(`t`.`transaction_id`) AS `Count` from `transaction_log` `t` where (`t`.`transaction_type` = 'Metrics - Completed Mechanic Operation Wheel') group by year(`t`.`date`),month(`t`.`date`),`t`.`sold_by` order by year(`t`.`date`) desc,month(`t`.`date`) desc,`t`.`sold_by`;

-- --------------------------------------------------------

--
-- Structure for view `view_EmployeeMetrics_WheelsbyWeek`
--
DROP TABLE IF EXISTS `view_EmployeeMetrics_WheelsbyWeek`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_EmployeeMetrics_WheelsbyWeek` AS select year(`t`.`date`) AS `Year`,week(`t`.`date`,0) AS `Week`,`t`.`sold_by` AS `contact_id`,sum(`t`.`amount`) AS `TotalValue`,round(avg(`t`.`amount`),0) AS `AverageValue`,count(`t`.`transaction_id`) AS `Count` from `transaction_log` `t` where (`t`.`transaction_type` = 'Metrics - Completed Mechanic Operation Wheel') group by year(`t`.`date`),week(`t`.`date`,0),`t`.`sold_by` order by year(`t`.`date`) desc,week(`t`.`date`,0) desc,`t`.`sold_by`;

-- --------------------------------------------------------

--
-- Structure for view `view_MechanicOperationMetrics_byMonth`
--
DROP TABLE IF EXISTS `view_MechanicOperationMetrics_byMonth`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_MechanicOperationMetrics_byMonth` AS select `Hours`.`Year` AS `Year`,`Hours`.`Month` AS `Month`,`Hours`.`Hours` AS `Hours`,`Hours`.`Pay` AS `Pay`,`Trans`.`NetSalesNewParts` AS `NetSalesNewParts`,`Trans`.`SalesUsedParts` AS `SalesUsedParts`,`Trans`.`ValueBikesFixed` AS `ValueBikesFixed`,`Trans`.`ValueWheelsFixed` AS `ValueWheelsFixed`,`Trans`.`ValueNewPartsOnBikes` AS `ValueNewPartsOnBikes`,((((`Trans`.`NetSalesNewParts` + `Trans`.`SalesUsedParts`) + `Trans`.`ValueBikesFixed`) + `Trans`.`ValueWheelsFixed`) - (`Hours`.`Pay` + `Trans`.`ValueNewPartsOnBikes`)) AS `EstimatedNetIncome`,`Trans`.`TotalBikesFixed` AS `TotalBikesFixed`,`Trans`.`TotalWheelsFixed` AS `TotalWheelsFixed`,round((`Hours`.`Hours` / `Trans`.`TotalBikesFixed`),1) AS `HoursPerBike`,round((`Trans`.`ValueBikesFixed` / `Trans`.`TotalBikesFixed`),1) AS `AverageBikeValue`,`Trans`.`SalesBikes` AS `SalesBikes`,`Trans`.`TotalBikesSold` AS `TotalBikesSold` from (`view_EmployeeHours_byMonth_WholeOper` `Hours` left join `view_Transactions_MechOper_byMonth_pvTbl` `Trans` on(((`Hours`.`Year` = `Trans`.`Year`) and (`Hours`.`Month` = `Trans`.`Month`)))) order by `Hours`.`Year` desc,`Hours`.`Month` desc;

-- --------------------------------------------------------

--
-- Structure for view `view_MechanicOperationMetrics_byWeek`
--
DROP TABLE IF EXISTS `view_MechanicOperationMetrics_byWeek`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_MechanicOperationMetrics_byWeek` AS select `Hours`.`Year` AS `Year`,`Hours`.`Week` AS `Week`,`Hours`.`Hours` AS `Hours`,`Hours`.`Pay` AS `Pay`,`Trans`.`NetSalesNewParts` AS `NetSalesNewParts`,`Trans`.`SalesUsedParts` AS `SalesUsedParts`,`Trans`.`ValueBikesFixed` AS `ValueBikesFixed`,`Trans`.`ValueWheelsFixed` AS `ValueWheelsFixed`,`Trans`.`ValueNewPartsOnBikes` AS `ValueNewPartsOnBikes`,((((`Trans`.`NetSalesNewParts` + `Trans`.`SalesUsedParts`) + `Trans`.`ValueBikesFixed`) + `Trans`.`ValueWheelsFixed`) - (`Hours`.`Pay` + `Trans`.`ValueNewPartsOnBikes`)) AS `EstimatedNetIncome`,`Trans`.`TotalBikesFixed` AS `TotalBikesFixed`,`Trans`.`TotalWheelsFixed` AS `TotalWheelsFixed`,round((`Hours`.`Hours` / `Trans`.`TotalBikesFixed`),1) AS `HoursPerBike`,round((`Trans`.`ValueBikesFixed` / `Trans`.`TotalBikesFixed`),1) AS `AverageBikeValue`,`Trans`.`SalesBikes` AS `SalesBikes`,`Trans`.`TotalBikesSold` AS `TotalBikesSold` from (`view_EmployeeHours_byWeek_WholeOper` `Hours` left join `view_Transactions_MechOper_byWeek_pvTbl` `Trans` on(((`Hours`.`Year` = `Trans`.`Year`) and (`Hours`.`Week` = `Trans`.`Week`)))) order by `Hours`.`Year` desc,`Hours`.`Week` desc;

-- --------------------------------------------------------

--
-- Structure for view `view_sales_by_week`
--
DROP TABLE IF EXISTS `view_sales_by_week`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_sales_by_week` AS select if((week(`t`.`date`,0) <> 0),year(`t`.`date`),(year(`t`.`date`) - 1)) AS `Year`,if((week(`t`.`date`,0) <> 0),week(`t`.`date`,0),53) AS `Week`,`t`.`transaction_type` AS `TransactionType`,round(sum(`t`.`amount`),2) AS `Total`,count(`t`.`transaction_id`) AS `CountOfTrans`,`transaction_types`.`accounting_group` AS `AccountingGroup` from ((`transaction_log` `t` left join `transaction_types` on((`t`.`transaction_type` = `transaction_types`.`transaction_type_id`))) left join `shops` on((`t`.`shop_id` = `shops`.`shop_id`))) where (`shops`.`shop_type` = 'Mechanic Operation Shop') group by `transaction_types`.`accounting_group`,`t`.`transaction_type`,year(`t`.`date`),quarter(`t`.`date`),month(`t`.`date`) order by if((week(`t`.`date`,0) <> 0),year(`t`.`date`),(year(`t`.`date`) - 1)) desc,if((week(`t`.`date`,0) <> 0),week(`t`.`date`,0),53) desc,`transaction_types`.`accounting_group`,`t`.`transaction_id`;

-- --------------------------------------------------------

--
-- Structure for view `view_Transactions`
--
DROP TABLE IF EXISTS `view_Transactions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_Transactions` AS select year(`t`.`date`) AS `Year`,month(`t`.`date`) AS `Month`,if((week(`t`.`date`,0) <> 0),year(`t`.`date`),(year(`t`.`date`) - 1)) AS `YearWeek`,if((week(`t`.`date`,0) <> 0),week(`t`.`date`,0),53) AS `Week`,`t`.`transaction_type` AS `TransactionType`,round(`t`.`amount`,2) AS `Total`,`transaction_types`.`accounting_group` AS `AccountingGroup`,if((`shops`.`shop_type` = 'Mechanic Operation Shop'),'Mechanic Operation Shop','Volunteer Run Shop') AS `ShopType` from ((`transaction_log` `t` left join `transaction_types` on((`t`.`transaction_type` = `transaction_types`.`transaction_type_id`))) left join `shops` on((`t`.`shop_id` = `shops`.`shop_id`)));

-- --------------------------------------------------------

--
-- Structure for view `view_Transactions_MechOper_byMonth`
--
DROP TABLE IF EXISTS `view_Transactions_MechOper_byMonth`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_Transactions_MechOper_byMonth` AS select `v`.`Year` AS `Year`,`v`.`Month` AS `Month`,`v`.`TransactionType` AS `TransactionType`,sum(`v`.`Total`) AS `Total`,count(`v`.`Total`) AS `Count`,`v`.`AccountingGroup` AS `AccountingGroup`,`v`.`ShopType` AS `ShopType` from `view_Transactions` `v` where (`v`.`ShopType` = 'Mechanic Operation Shop') group by `v`.`Year`,`v`.`Month`,`v`.`TransactionType` order by `v`.`Year` desc,`v`.`Month` desc,`v`.`TransactionType`;

-- --------------------------------------------------------

--
-- Structure for view `view_Transactions_MechOper_byMonth_pvTbl`
--
DROP TABLE IF EXISTS `view_Transactions_MechOper_byMonth_pvTbl`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_Transactions_MechOper_byMonth_pvTbl` AS select `v`.`Year` AS `Year`,`v`.`Month` AS `Month`,round(max(if((`v2`.`TransactionType` = 'Sale - New Parts'),(`v2`.`Total` / 2),0)),2) AS `NetSalesNewParts`,max(if((`v2`.`TransactionType` = 'Sale - Used Parts'),`v2`.`Total`,0)) AS `SalesUsedParts`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Total`,0)) AS `SalesBikes`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Bike'),`v2`.`Total`,0)) AS `ValueBikesFixed`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Wheel'),`v2`.`Total`,0)) AS `ValueWheelsFixed`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Count`,0)) AS `TotalBikesSold`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Bike'),`v2`.`Count`,0)) AS `TotalBikesFixed`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Wheel'),`v2`.`Count`,0)) AS `TotalWheelsFixed`,max(if((`v2`.`TransactionType` = 'Metrics - New Parts on a Completed Bike'),`v2`.`Total`,0)) AS `ValueNewPartsOnBikes` from (`view_Transactions_MechOper_byMonth` `v` left join `view_Transactions_MechOper_byMonth` `v2` on(((`v`.`Year` = `v2`.`Year`) and (`v`.`Month` = `v2`.`Month`)))) group by `v`.`Year`,`v`.`Month`;

-- --------------------------------------------------------

--
-- Structure for view `view_Transactions_MechOper_byWeek`
--
DROP TABLE IF EXISTS `view_Transactions_MechOper_byWeek`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_Transactions_MechOper_byWeek` AS select `v`.`YearWeek` AS `Year`,`v`.`Week` AS `Week`,`v`.`TransactionType` AS `TransactionType`,sum(`v`.`Total`) AS `Total`,count(`v`.`Total`) AS `Count`,`v`.`AccountingGroup` AS `AccountingGroup`,`v`.`ShopType` AS `ShopType` from `view_Transactions` `v` where (`v`.`ShopType` = 'Mechanic Operation Shop') group by `v`.`YearWeek`,`v`.`Week`,`v`.`TransactionType` order by `v`.`YearWeek`,`v`.`Week`,`v`.`TransactionType`;

-- --------------------------------------------------------

--
-- Structure for view `view_Transactions_MechOper_byWeek_pvTbl`
--
DROP TABLE IF EXISTS `view_Transactions_MechOper_byWeek_pvTbl`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_Transactions_MechOper_byWeek_pvTbl` AS select `v`.`Year` AS `Year`,`v`.`Week` AS `Week`,round(max(if((`v2`.`TransactionType` = 'Sale - New Parts'),(`v2`.`Total` / 2),0)),2) AS `NetSalesNewParts`,max(if((`v2`.`TransactionType` = 'Sale - Used Parts'),`v2`.`Total`,0)) AS `SalesUsedParts`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Total`,0)) AS `SalesBikes`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Bike'),`v2`.`Total`,0)) AS `ValueBikesFixed`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Wheel'),`v2`.`Total`,0)) AS `ValueWheelsFixed`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Count`,0)) AS `TotalBikesSold`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Bike'),`v2`.`Count`,0)) AS `TotalBikesFixed`,max(if((`v2`.`TransactionType` = 'Metrics - Completed Mechanic Operation Wheel'),`v2`.`Count`,0)) AS `TotalWheelsFixed`,max(if((`v2`.`TransactionType` = 'Metrics - New Parts on a Completed Bike'),`v2`.`Total`,0)) AS `ValueNewPartsOnBikes` from (`view_Transactions_MechOper_byWeek` `v` left join `view_Transactions_MechOper_byWeek` `v2` on(((`v`.`Year` = `v2`.`Year`) and (`v`.`Week` = `v2`.`Week`)))) group by `v`.`Year`,`v`.`Week`;

-- --------------------------------------------------------

--
-- Structure for view `view_Transactions_VolRunShop_byMonth`
--
DROP TABLE IF EXISTS `view_Transactions_VolRunShop_byMonth`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_Transactions_VolRunShop_byMonth` AS select `v`.`Year` AS `Year`,`v`.`Month` AS `Month`,`v`.`TransactionType` AS `TransactionType`,sum(`v`.`Total`) AS `Total`,count(`v`.`Total`) AS `Count`,`v`.`AccountingGroup` AS `AccountingGroup`,`v`.`ShopType` AS `ShopType` from `view_Transactions` `v` where (`v`.`ShopType` = 'Volunteer Run Shop') group by `v`.`Year`,`v`.`Month`,`v`.`TransactionType` order by `v`.`Year`,`v`.`Month`,`v`.`TransactionType`;

-- --------------------------------------------------------

--
-- Structure for view `view_Transactions_VolRunShop_byMonth_pvTbl`
--
DROP TABLE IF EXISTS `view_Transactions_VolRunShop_byMonth_pvTbl`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_Transactions_VolRunShop_byMonth_pvTbl` AS select `v`.`Year` AS `Year`,`v`.`Month` AS `Month`,round(max(if((`v2`.`TransactionType` = 'Sale - New Parts'),(`v2`.`Total` / 2),0)),2) AS `NetSalesNewParts`,max(if((`v2`.`TransactionType` = 'Sale - Used Parts'),`v2`.`Total`,0)) AS `SalesUsedParts`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Total`,0)) AS `SalesBikes`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Count`,0)) AS `TotalBikesSold` from (`view_Transactions_VolRunShop_byMonth` `v` left join `view_Transactions_VolRunShop_byMonth` `v2` on(((`v`.`Year` = `v2`.`Year`) and (`v`.`Month` = `v2`.`Month`)))) group by `v`.`Year`,`v`.`Month`;

-- --------------------------------------------------------

--
-- Structure for view `view_Transactions_VolRunShop_byWeek`
--
DROP TABLE IF EXISTS `view_Transactions_VolRunShop_byWeek`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_Transactions_VolRunShop_byWeek` AS select `v`.`YearWeek` AS `Year`,`v`.`Week` AS `Week`,`v`.`TransactionType` AS `TransactionType`,sum(`v`.`Total`) AS `Total`,count(`v`.`Total`) AS `Count`,`v`.`AccountingGroup` AS `AccountingGroup`,`v`.`ShopType` AS `ShopType` from `view_Transactions` `v` where (`v`.`ShopType` = 'Volunteer Run Shop') group by `v`.`YearWeek`,`v`.`Week`,`v`.`TransactionType` order by `v`.`YearWeek`,`v`.`Week`,`v`.`TransactionType`;

-- --------------------------------------------------------

--
-- Structure for view `view_Transactions_VolRunShop_byWeek_pvTbl`
--
DROP TABLE IF EXISTS `view_Transactions_VolRunShop_byWeek_pvTbl`;

CREATE ALGORITHM=UNDEFINED DEFINER=`admin`@`%.dyn.grandenetworks.net` SQL SECURITY DEFINER VIEW `view_Transactions_VolRunShop_byWeek_pvTbl` AS select `v`.`Year` AS `Year`,`v`.`Week` AS `Week`,round(max(if((`v2`.`TransactionType` = 'Sale - New Parts'),(`v2`.`Total` / 2),0)),2) AS `NetSalesNewParts`,max(if((`v2`.`TransactionType` = 'Sale - Used Parts'),`v2`.`Total`,0)) AS `SalesUsedParts`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Total`,0)) AS `SalesBikes`,max(if((`v2`.`TransactionType` = 'Sale - Complete Bike'),`v2`.`Count`,0)) AS `TotalBikesSold` from (`view_Transactions_VolRunShop_byWeek` `v` left join `view_Transactions_VolRunShop_byWeek` `v2` on(((`v`.`Year` = `v2`.`Year`) and (`v`.`Week` = `v2`.`Week`)))) group by `v`.`Year`,`v`.`Week`;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `location_type` FOREIGN KEY (`location_type`) REFERENCES `transaction_types` (`transaction_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `shops`
--
ALTER TABLE `shops`
  ADD CONSTRAINT `shop_location` FOREIGN KEY (`shop_location`) REFERENCES `shop_locations` (`shop_location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `shop_type` FOREIGN KEY (`shop_type`) REFERENCES `shop_types` (`shop_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `shop_hours`
--
ALTER TABLE `shop_hours`
  ADD CONSTRAINT `contact_id` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`contact_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_id` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `shop_id` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `shop_user_role` FOREIGN KEY (`shop_user_role`) REFERENCES `shop_user_roles` (`shop_user_role_id`) ON UPDATE CASCADE;

--
-- Constraints for table `transaction_log`
--
ALTER TABLE `transaction_log`
  ADD CONSTRAINT `sold_by` FOREIGN KEY (`sold_by`) REFERENCES `contacts` (`contact_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sold_to` FOREIGN KEY (`sold_to`) REFERENCES `contacts` (`contact_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_type` FOREIGN KEY (`transaction_type`) REFERENCES `transaction_types` (`transaction_type_id`) ON UPDATE CASCADE;
