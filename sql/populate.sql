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
  ("Other", 7);

-- Add shop user roles to shop_user_roles
-- sales == 1 if you want a role to be able to do sales
-- volunteer == 1 if you want to keep track of volunteer hours
-- paid == 1 if you want to track staff/employee and stats
--
-- default select value for shop user may be set in Connections/database_functions.php,
-- a shop_type_id with the same name needs to exist in order for this to work.

DELETE FROM shop_user_roles;
INSERT INTO shop_user_roles (
  shop_user_role_id, hours_rank, volunteer, sales, paid
) VALUES 
  ("Coordinator",0,0,1,0), 
  ("Personal",0,0,0,0), 
  ("Volunteer",0,1,0,0), 
  ("Greeter",0,0,1,0),
  ("Staff",0,0,1,1);

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

DELETE FROM shop_hours;
DELETE FROM contacts;
ALTER TABLE contacts AUTO_INCREMENT = 1;
INSERT INTO contacts (
  contact_id, first_name, middle_initial, last_name, email, phone, address1, 
  address2, city, state, country, receive_newsletter, date_created, 
  invited_newsletter, DOB, pass, zip, hidden, location_name, location_type
) VALUES 
  (1,"Jonathan","","Rosenbaum","info@positivespin.org","","","","Morgantown","WV","","1","2014-12-12 18:19:35",0,"2005-11-09","test","26501","0","",NULL);

-- Set-up transaction types
-- This is object orienteed like :)
--
-- Storage period may be defined in Connections/database_functions.php
--
-- SILLY TEXT FIELDS (some presentation logic that is in the business logic, rather than kept cleanly separated from it)
-- NOTE - (:colon is appended by default:)
--
-- fieldname_date: text field for the day the transaction transpires, e.g. "Sale Date"
-- fieldname_soldby: text field for the sales person (see shop_user_roles table) who performs the sale
-- message_transaction_id:  text field after transaction_id .. seems pointless
-- fieldname_soldto: text field for person being sold to, e.g. "Sold To"
-- show_soldto_location: while not a presentation, without it, previous field is useless. (Also, see discussion about location) 
-- fieldname_description: text field for description text area, e.g. "Description"
--
-- DISCUSSION ABOUT LOCATIONS in transaction_log.php 
-- show_soldto_location is now used to show patrons. The history of this name is 
-- that YBP was using it to keep track of donations and locations.  However, there is an
-- option in transaction_log.php that would show current shop users that was 
-- commented out, but never developed to work.
-- More than 1 shop with its own accounting?  Run a different instance of YBDB.
--
-- USELESS or RESERVED FIELDS
-- show_soldto and show_soldby currently do not do anything, 
-- and not what you think they would do either :) 
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
-- 									           needs to be defined in Connections/database_functions.php
--	

INSERT INTO transaction_types 
  (transaction_type_id, rank, active, community_bike, show_transaction_id, 
  show_type, show_startdate, show_amount, show_description, show_soldto, 
  show_soldby, fieldname_date, fieldname_soldby, message_transaction_id, 
  fieldname_soldto, show_soldto_location, fieldname_description, 
  accounting_group
) VALUES 
  ("Build Your Own Bike", 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"), 
  ("Bicycles", 2, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"), 
  ("Non-inventory Parts", 3, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"), 
  ("Trade-ups/Ins", 4, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"), 
  ("Helmets", 5, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"), 
  ("Donations", 6, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"), 
  ("Memberships", 7, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"), 
  ("Inventory Parts", 8, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"), 
  ("Cargo Related", 9, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"), 
  ("Car Racks", 10, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"), 
  ("DIY Repairs", 11, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"),
  ("Accounts Receivable Invoice", 12, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"), 
  ("Accounts Receivable Payment", 13, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"), 
  ("Deposit", 14, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"),
  ("Metrics - Completed Mechanic Operation Bike", 15, 1, 0, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"),
  ("Metrics - Completed Mechanic Operation Wheel", 16, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"),
  ("Metrics - New Parts on a Completed Bike", 17, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"),
  ("Sale - Used Parts", 18, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"),
  ("Sale - New Parts", 19, 1, 1, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales"),
  ("Sale - Complete Bike", 20, 1, 0, 1, 1, 0, 1, 1, 1, 1, "Sale Date", "Sold By"," ", "Sold To", 1, "Description", "Sales");

-- transaction_log - added paid or not
--  - added payment_type (cash, check or cc)
-- transaction_id, date_startstorage, date,transaction_type, amount,
-- description, sold_to, sold_by, quantity, shop_id, paid
ALTER TABLE transaction_log ADD paid tinyint(1) NOT NULL DEFAULT '0';



