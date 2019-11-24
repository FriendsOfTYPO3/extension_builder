#
# Table structure for table 'tx_testextension_domain_model_main'
#
CREATE TABLE tx_testextension_domain_model_main (

	name varchar(255) DEFAULT '' NOT NULL,
	identifier varchar(255) DEFAULT '' NOT NULL,
	description text,
	my_date date DEFAULT NULL,
	mail varchar(255) DEFAULT '' NOT NULL,
	child1 int(11) unsigned DEFAULT '0',
	children2 int(11) unsigned DEFAULT '0' NOT NULL,
	child3 int(11) unsigned DEFAULT '0',
	children4 int(11) unsigned DEFAULT '0' NOT NULL

);

#
# Table structure for table 'tx_testextension_domain_model_child1'
#
CREATE TABLE tx_testextension_domain_model_child1 (

	name varchar(255) DEFAULT '' NOT NULL,
	flag smallint(5) unsigned DEFAULT '0' NOT NULL

);

#
# Table structure for table 'tx_testextension_domain_model_child2'
#
CREATE TABLE tx_testextension_domain_model_child2 (

	main int(11) unsigned DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	date_property1 date DEFAULT NULL,
	date_property2 datetime DEFAULT NULL,
	date_property3 int(11) DEFAULT '0' NOT NULL,
	date_property4 int(11) DEFAULT '0' NOT NULL

);

#
# Table structure for table 'tx_testextension_domain_model_child3'
#
CREATE TABLE tx_testextension_domain_model_child3 (

	name varchar(255) DEFAULT '' NOT NULL,
	password varchar(255) DEFAULT '' NOT NULL,
	image_property int(11) unsigned NOT NULL default '0'

);

#
# Table structure for table 'tx_testextension_domain_model_child4'
#
CREATE TABLE tx_testextension_domain_model_child4 (

	name varchar(255) DEFAULT '' NOT NULL,
	file_property int(11) unsigned NOT NULL default '0'

);

#
# Table structure for table 'tx_testextension_main_child4_mm'
#
CREATE TABLE tx_testextension_main_child4_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_testextension_domain_model_child3'
#
CREATE TABLE tx_testextension_domain_model_child3 (
	categories int(11) unsigned DEFAULT '0' NOT NULL
);
