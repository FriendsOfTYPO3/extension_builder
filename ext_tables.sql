#
# Table structure for table 'tx_extbasekickstarter_extension'
#
CREATE TABLE tx_extbasekickstarter_extension (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	extensionkey tinytext,
	title tinytext,
	description text,
	state tinytext,
	languages tinytext,
	persons text,
	domainobjects text,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_extbasekickstarter_person'
#
CREATE TABLE tx_extbasekickstarter_person (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name tinytext,
	email tinytext,
	role tinytext,
	extension int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_extbasekickstarter_domainObjects'
#
CREATE TABLE tx_extbasekickstarter_domainObjects (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name tinytext,
	description text,
	relateddomainobjects text,
	extension int(11) DEFAULT '0' NOT NULL,
	backendconfigurationoptions text,
	properties text,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_extbasekickstarter_properties'
#
CREATE TABLE tx_extbasekickstarter_properties (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name tinytext,
	description text,
	validationrules text,
	isrequired tinyint(3) DEFAULT '0' NOT NULL,
	accessmethods text,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);