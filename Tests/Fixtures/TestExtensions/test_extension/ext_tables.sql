CREATE TABLE tx_testextension_domain_model_main (
	name varchar(255) NOT NULL DEFAULT '',
	identifier varchar(255) NOT NULL DEFAULT '',
	description text,
	my_date date DEFAULT NULL,
	mail varchar(255) NOT NULL DEFAULT '',
	child1 int(11) unsigned DEFAULT '0',
	children2 int(11) unsigned NOT NULL DEFAULT '0',
	child3 int(11) unsigned DEFAULT '0',
	children4 int(11) unsigned NOT NULL DEFAULT '0'
);

CREATE TABLE tx_testextension_domain_model_child1 (
	name varchar(255) NOT NULL DEFAULT '',
	flag smallint(1) unsigned NOT NULL DEFAULT '0'
);

CREATE TABLE tx_testextension_domain_model_child2 (
	main int(11) unsigned DEFAULT '0' NOT NULL,
	name varchar(255) NOT NULL DEFAULT '',
	date_property1 date DEFAULT NULL,
	date_property2 datetime DEFAULT NULL,
	date_property3 int(11) NOT NULL DEFAULT '0',
	date_property4 int(11) NOT NULL DEFAULT '0'
);

CREATE TABLE tx_testextension_domain_model_child3 (
	name varchar(255) NOT NULL DEFAULT '',
	password varchar(255) NOT NULL DEFAULT '',
	image_property int(11) unsigned NOT NULL DEFAULT '0'
);

CREATE TABLE tx_testextension_domain_model_child4 (
	name varchar(255) NOT NULL DEFAULT '',
	file_property int(11) unsigned NOT NULL DEFAULT '0'
);

CREATE TABLE tx_testextension_main_child4_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_testextension_domain_model_child3 (
	categories int(11) unsigned DEFAULT '0' NOT NULL
);
