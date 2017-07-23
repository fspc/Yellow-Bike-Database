-- This is probably the first documentation ever created for this project!
-- (adjusted while developing, usually at the docker instance - https://github.com/fspc/ybdb )

-- Add shop_locations and shop_types

DELETE FROM shop_locations;
INSERT INTO shop_locations (shop_location_id, date_established, active) VALUES 
  ("Positive Spin", "2005-11-09",1), 
  ("Plan B", "2000-01-01", 1), 
  ("Third Hand", "2003-01-01", 1), 
  ("Austin Yellow Bike Project", "1997-01-01", 1);

-- Mechanic Operation Shop & Volunteer Run Shop are both special types of shops for shop_log.php
-- The are hardwired in MySQL Views and are used for METRIC Statistics

DELETE FROM shop_types;
INSERT INTO shop_types (shop_type_id, list_order) VALUES 
  ("Open Shop", 1),
  ("Bike Education", 4), 
  ("Volunteer Only", 2), 
  ("Meeting", 3 ),
  ("Mechanic Operation Shop", 5),
  ("Volunteer Run Shop", 6),
  ("Other", 7),
  ("Bike Delivery", 8);

-- Add shop user roles to shop_user_roles
-- sales == 1 if you want a role to be able to do sales
-- volunteer == 1 if you want to keep track of volunteer hours
-- paid == 1 if you want to track staff/employee and stats
-- other_volunteer == 1 if you want to keep track of other hours like community service and student service.
--
-- default select value for shop user may be set in Connections/database_functions.php,
-- a shop_type_id with the same name needs to exist in order for this to work.
--
-- Stand Time is a special type of user_role_id (assuming all 0's) where hours are figured out automatically in the
-- special Stand Time transaction_type_id. 

ALTER TABLE shop_user_roles ADD other_volunteer tinyint(1) NOT NULL DEFAULT '0';
DELETE FROM shop_user_roles;
INSERT INTO shop_user_roles (
  shop_user_role_id, hours_rank, volunteer, sales, paid, other_volunteer
) VALUES 
  ("Coordinator",0,1,1,0,0), 
  ("Stand Time",0,0,0,0,0), 
  ("Volunteer",0,1,0,0,0), 
  ("Greeter",0,1,1,0,0),
  ("Staff",0,0,1,1,0),
  ("Student Volunteer/Community Service Hours",0,0,0,0,1),
  ("Shopping",0,0,0,0,0);

-- Add some projects to projects

DELETE FROM projects;
INSERT INTO projects (project_id, date_established, active, public) VALUES 
  ("","",1,1), 
  ("Bike Building", "2014-12-13", 1, 1), 
  ("Computers", "2014-12-13", 1, 1), 
  ("Inventory", "2014-12-13", 1, 1), 
  ("Organization", "2014-12-13", 1, 1), 
  ("Website", "2014-12-13", 1, 1),
  ("Toy Give-Away", "2014-12-13", 1, 1);

-- (not a requirement) Add some people to contacts
-- easy solution - 
--  select GROUP_CONCAT(COLUMN_NAME) from information_schema.COLUMNS where TABLE_NAME='contacts'
-- Then take that output and find the values ..
-- SELECT CONCAT_WS('","', col1, col2, ..., coln) FROM my_table;
--
--  Added waiver

DELETE FROM shop_hours;
DELETE FROM contacts;
ALTER TABLE contacts AUTO_INCREMENT = 1;
ALTER TABLE contacts ADD waiver tinyint(1) NOT NULL DEFAULT '0';
INSERT INTO contacts (
  contact_id, first_name, middle_initial, last_name, email, phone, address1, 
  address2, city, state, country, receive_newsletter, date_created, 
  invited_newsletter, DOB, pass, zip, hidden, location_name, location_type,
  waiver
) VALUES 
  (1,"Jonathan","","Rosenbaum","info@positivespin.org","","","","Morgantown","WV","","1","2014-12-12 18:19:35",0,"2005-11-09","test","26501","0","",NULL, 1);

-- Set-up transaction types
-- This is object orienteed like :)
--
-- TEXT FIELDS (some presentation logic that is in the business logic, rather than kept cleanly separated from it)
-- Although, there are some clear advantages to this approach.
-- NOTE - (:colon is appended by default:)
--
-- fieldname_date: text field for the day the transaction transpires, e.g. "Sale Date"
-- fieldname_soldby: text field for the sales person (see shop_user_roles table) who performs the sale
-- message_transaction_id:  text field after transaction_id, a way to add instructions, etc.
-- fieldname_description: text field for description text area, e.g. "Description"
-- fieldname_soldto: text field for person being sold to, e.g. "Sold To"
-- show_soldto_signed_in or show_soldto_not_signed_in: while not a presentation, without one or the other, previous field,
-- 	"fieldname_soldto" is useless. (Also, see discussion about location) 
--
-- PRESENTATION
--
-- active: turns off/on the ability to select a transaction
-- community_bike: shows quantity field if set (usually ignored by volunteers in a real life shop setting)
-- show_soldto_signedshow / soldto_not_signed_in: Pull-down list for patrons - one needs to be 0, and the other 1 
-- 	or vice versa, in order to work properly.
--
-- show_transaction_id (currently not working), 
-- show_type (currently not working) 
-- show_startdate (discussion in storage transaction), 
-- show_amount (working) 
-- show_description (working)
-- USELESS or RESERVED FIELDS
-- show_soldto and show_soldby currently do not do anything, 
-- and not what you think they would do either :) 
--
-- (Developers) DISCUSSION ABOUT LOCATIONS - In transaction_log.php 
-- "show_soldto_location" was being used to show patrons. The probable history of this name was 
-- that YBP was using it to keep track of donation locations (ignore the "sold" word).  However, there is an
-- option in transaction_log.php that was meant to show current shop users that was 
-- commented out, basically, things were still being developed.  
-- The usefullness of location_add_edit_select.php (inserts new records into contact table) is that it allows
-- donors to be added who are not present at the shop, usually locations like Department stores, etc. without a password.
-- It looks like the end result was a compromise with list_donation_locations_withheader() for unlogged donors/patrons
-- being used for everything, rather than list_CurrentShopUsers_select when appropriate.  However, associating
-- certain types transactions with different behavior makes good sense.  In this revision, show_soldto_location has
-- been renamed to show_soldto_signed_in and a new column show_soldto_not_signed_in has been added.  This gives fine
-- grain control over transaction behavior.  One needs to be 0, and the other 1 or vice versa, in order to work properly,
-- currently there are some css issues when not_signed_in is used for some transactions.
--
--
-- METRICS - Transaction Types (built-in names) to add to make Metrics work properly:
--				 MySQL Views have these built-in.
--  "Metrics - Completed Mechanic Operation Bike" (quantity must be 1)
--  "Metrics - Completed Mechanic Operation Wheel" 
--  "Metrics - New Parts on a Completed Bike"
--  "Sale - Used Parts"
--  "Sale - New Parts"
--  "Sale - Complete Bike"
--
--  Note: good news, default select value for transaction types may be set in Connections/database_functions.php
--
--  Sales Tax Report - Hardwired Caveat: The same value used for accounting_group 
-- 									           needs to be defined in Connections/database_functions.php - ACCOUNTING_GROUP
--                                       Currently, Sales and Deposits are the two major accounting groups, although
--                                       Receivables could be added in the future if that functionality is desired.
--
--  STORAGE TRANSACTION	
--  show_startdate - is used by transactions where an item (usually a bicycle) is stored for
--  a defined period before it is purchased.  If this is set,
--  the behavior is to hide price (show_amount) and payment types (show_payment)
--  until a date (label defined by fieldname_date) is entered.  When the transaction is complete,
--  it rises to the current shop day, and is assigned the most recent transaction id.
--
--  Storage period may be defined in Connections/database_functions.php - STORAGE_PERIOD
--
-- DEPOSITS
-- Deposit (transaction_type_id) is a special transaction type that behaves differently in the log
-- for a good reason.
--
-- MEMBERSHIPS
-- Memberships (transaction_type_id) is a special transaction type in that the software keeps track of paid
-- memberships and statistics.
--
-- BICYCLES
-- Bicycles (transaction_type_id) is a special transaction type that can have limits applied to how many bicycles can be
-- earned with volunteer hour benefits (transforming volunteer hours into cash).
--
-- STAND TIME
-- Stand Time (transaction_type_id) is a special transaction type that automatically figures out the amount that needs to be paid based
-- on the configured hourly rate (15 minute grace period) and login status.
--
-- DONATIONS are best complimented with anonymous, see below.
--
-- "show_payment" shows cash, credit, and check payment types if selected.
-- Assuming show_soldto_location is set, "anonymous" allows anonymous transactions with a check box.

ALTER TABLE transaction_types CHANGE show_soldto_location show_soldto_signed_in tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE transaction_types ADD show_payment tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE transaction_types ADD anonymous tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE transaction_types ADD show_soldto_not_signed_in tinyint(1) NOT NULL DEFAULT '0';
INSERT INTO transaction_types 
  (transaction_type_id, rank, 
   active, community_bike, show_transaction_id, show_type, show_startdate, 
   show_amount, show_description, show_soldto, show_soldby, 
   fieldname_date, fieldname_soldby, message_transaction_id,
   fieldname_soldto, show_soldto_signed_in, fieldname_description, 
   accounting_group, show_payment, anonymous, show_soldto_not_signed_in
) VALUES 
  ("Build Your Own Bike", 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0), 
  ("Bicycles", 2, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0), 
  ("Used Parts", 3, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0), 
  ("Trade-ups/Ins", 4, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0), 
  ("Helmets", 5, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0), 
  ("Donations", 6, 1, 0, 1, 1, 0, 0, 1, 1, 1, "Sale Date", "Received by"," ", "Donated by", 1, "Description", "Donations", 0, 1, 0),
  ("Monetary Donations", 7, 1, 0, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Received by"," ", "Donated by", 1, "Description", "Sales", 1, 1, 0),     
  ("Memberships", 8, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0), 
  ("New Parts", 9, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0), 
  ("Cargo Related", 10, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0), 
  ("Car Racks", 11, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0), 
  ("Stand Time", 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0),
  ("Accounts Receivable Invoice", 13, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 0, 0, 0), 
  ("Accounts Receivable Payment", 14, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0), 
  ("Deposit", 15, 1, 0, 1, 1, 0, 1, 1, 1, 1, "Deposit Date", "Deposited By"," ", "", 0, "Description", "Deposits", 0, 0, 0),
  ("Metrics - Completed Mechanic Operation Bike", 16, 1, 0, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0),
  ("Metrics - Completed Mechanic Operation Wheel", 17, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0),
  ("Metrics - New Parts on a Completed Bike", 18, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0),
  ("Sale - Used Parts", 19, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0),
  ("Sale - New Parts", 20, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0),
  ("Sale - Complete Bike", 21, 1, 0, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales", 1, 0, 0),
  ("Giveaway", 22, 1, 0, 1, 1, 0, 0, 1, 1, 1, "Sale Date", "Given By"," ", "Given To", 1, "Description", "Sales", 0, 0, 0);

-- transaction_log - added paid or not
--                 - added payment_type (cash, check or cc)
--						 - added check_number (Check#)
--						 - added change_fund to keep track of changes in the fund
--						 - added anonymous to store whether set or not for a transaction
--						 - added history to store transaction history
--						 - modified description from varchar(200) to text to reflect GnuCash, and possible use of
--						 	a GUI editor in the future.
-- transaction_id, date_startstorage, date,transaction_type, amount,
-- description, sold_to, sold_by, quantity, shop_id, paid

ALTER TABLE transaction_log ADD paid tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE transaction_log ADD payment_type varchar(6) DEFAULT NULL;
ALTER TABLE transaction_log ADD check_number int(10) unsigned DEFAULT NULL;
ALTER TABLE transaction_log ADD change_fund float DEFAULT NULL;
ALTER TABLE transaction_log ADD anonymous tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE transaction_log ADD history longblob NOT NULL;
ALTER TABLE transaction_log MODIFY description text(2048) DEFAULT NULL;

-- options  
-- currently creates/updates/deletes volunteer interests (checkboxes), 
-- but could be used for other surveys
--
CREATE TABLE IF NOT EXISTS options (
	option_name_id int(10) unsigned NOT NULL AUTO_INCREMENT,
	option_name varchar(64) NOT NULL UNIQUE,
	PRIMARY KEY (option_name_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- selections
-- stores the volunteer interest selections made by people
--
CREATE TABLE IF NOT EXISTS selections (
	contact_id int(10) unsigned,
	selection int(10) unsigned,
	selection_value text,
	FOREIGN KEY (contact_id) REFERENCES contacts (contact_id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (selection) REFERENCES options (option_name_id) ON DELETE CASCADE ON UPDATE CASCADE	
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- create the comment row first
INSERT INTO options (option_name) VALUES("comments");

-- add a deposit to initialize the POS
INSERT INTO transaction_log (
	transaction_id, date, transaction_type, 
	amount, description, sold_by, quantity, 
	shop_id, paid, change_fund) 
	VALUES(1,NOW(),"Deposit",0,"Initialize POS",1,0,0,0,20);
	