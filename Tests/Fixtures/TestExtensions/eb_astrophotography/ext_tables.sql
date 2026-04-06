CREATE TABLE tx_ebastrophotography_domain_model_astroimage (
	title varchar(255) NOT NULL DEFAULT '',
	slug varchar(255) NOT NULL DEFAULT '',
	description text,

	featured smallint(1) unsigned NOT NULL DEFAULT '0',
	stack_count int(11) NOT NULL DEFAULT '0',
	celestial_objects text NOT NULL,
	imaging_sessions text NOT NULL,
	processing_recipes text NOT NULL,
	awards text NOT NULL
);

CREATE TABLE tx_ebastrophotography_domain_model_celestialobject (
	name varchar(255) NOT NULL DEFAULT '',
	catalog_id varchar(255) NOT NULL DEFAULT '',
	object_type int(11) DEFAULT '0' NOT NULL,
	constellation varchar(255) NOT NULL DEFAULT '',
	right_ascension varchar(255) NOT NULL DEFAULT '',
	declination varchar(255) NOT NULL DEFAULT '',
	magnitude double(11,2) NOT NULL DEFAULT '0.00',
	distance_lightyears double(11,2) NOT NULL DEFAULT '0.00',
	description text,
	
	active smallint(1) unsigned NOT NULL DEFAULT '0'
);

CREATE TABLE tx_ebastrophotography_domain_model_telescope (
	name varchar(255) NOT NULL DEFAULT '',
	brand varchar(255) NOT NULL DEFAULT '',
	telescope_type int(11) DEFAULT '0' NOT NULL,
	focal_length int(11) NOT NULL DEFAULT '0',
	aperture int(11) NOT NULL DEFAULT '0',
	focal_ratio double(11,2) NOT NULL DEFAULT '0.00',
	purchase_date date DEFAULT NULL,
	active smallint(1) unsigned NOT NULL DEFAULT '0',
	notes text NOT NULL DEFAULT ''
	
);

CREATE TABLE tx_ebastrophotography_domain_model_camera (
	name varchar(255) NOT NULL DEFAULT '',
	brand varchar(255) NOT NULL DEFAULT '',
	sensor_type int(11) DEFAULT '0' NOT NULL,
	sensor_width double(11,2) NOT NULL DEFAULT '0.00',
	sensor_height double(11,2) NOT NULL DEFAULT '0.00',
	pixel_size double(11,2) NOT NULL DEFAULT '0.00',
	megapixels double(11,2) NOT NULL DEFAULT '0.00',
	cooled smallint(1) unsigned NOT NULL DEFAULT '0',
	purchase_date date DEFAULT NULL,
	active smallint(1) unsigned NOT NULL DEFAULT '0'
);

CREATE TABLE tx_ebastrophotography_domain_model_astrofilter (
	name varchar(255) NOT NULL DEFAULT '',
	filter_type int(11) DEFAULT '0' NOT NULL,
	central_wavelength int(11) NOT NULL DEFAULT '0',
	bandwidth double(11,2) NOT NULL DEFAULT '0.00',
	color varchar(7) NOT NULL DEFAULT '',
	manufacturer varchar(255) NOT NULL DEFAULT '',
	diameter double(11,2) NOT NULL DEFAULT '0.00',
	active smallint(1) unsigned NOT NULL DEFAULT '0'
);

CREATE TABLE tx_ebastrophotography_domain_model_observingsite (
	name varchar(255) NOT NULL DEFAULT '',
	description text NOT NULL DEFAULT '',
	latitude double(11,2) NOT NULL DEFAULT '0.00',
	longitude double(11,2) NOT NULL DEFAULT '0.00',
	altitude int(11) NOT NULL DEFAULT '0',
	bortle_class int(11) NOT NULL DEFAULT '0',
	website varchar(255) NOT NULL DEFAULT '',
	contact_email varchar(255) NOT NULL DEFAULT '',
	active smallint(1) unsigned NOT NULL DEFAULT '0'
	
);

CREATE TABLE tx_ebastrophotography_domain_model_imagingsession (
	session_date date DEFAULT NULL,
	start_time time DEFAULT NULL,

	temperature double(11,2) NOT NULL DEFAULT '0.00',
	humidity int(11) NOT NULL DEFAULT '0',
	seeing_conditions int(11) NOT NULL DEFAULT '0',
	transparency int(11) NOT NULL DEFAULT '0',
	moon_phase int(11) NOT NULL DEFAULT '0',
	total_frames int(11) NOT NULL DEFAULT '0',
	usable_frames int(11) NOT NULL DEFAULT '0',
	notes text NOT NULL DEFAULT '',
	observing_sites text NOT NULL,
	telescopes text NOT NULL,
	cameras text NOT NULL,
	astro_filters text NOT NULL
);

CREATE TABLE tx_ebastrophotography_domain_model_processingrecipe (
	title varchar(255) NOT NULL DEFAULT '',
	software varchar(255) NOT NULL DEFAULT '',
	description text,
	stacking_method int(11) DEFAULT '0' NOT NULL,
	total_integration_time double(11,2) NOT NULL DEFAULT '0.00',
	processing_date datetime DEFAULT NULL,
	
	active smallint(1) unsigned NOT NULL DEFAULT '0',
	notes text NOT NULL DEFAULT '',
	cameras text NOT NULL
);

CREATE TABLE tx_ebastrophotography_domain_model_award (
	title varchar(255) NOT NULL DEFAULT '',
	organization varchar(255) NOT NULL DEFAULT '',
	award_date date DEFAULT NULL,
	description text NOT NULL DEFAULT '',
	
	source_url varchar(255) NOT NULL DEFAULT '',
	active smallint(1) unsigned NOT NULL DEFAULT '0'
);
