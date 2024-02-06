#
# Table structure for table 'tx_mpdbcore_domain_model_subiteminstrument'
#
CREATE TABLE tx_mpdbcore_domain_model_subiteminstrument (
    abbreviation varchar(255) DEFAULT '' NOT NULL,
    expanded varchar(255) DEFAULT '' NOT NULL
);

#
# Table structure for table 'tx_mpdbcore_domain_model_publishedsubitem'
#
CREATE TABLE tx_mpdbcore_domain_model_publishedsubitem (

	publisher_makro_item int(11) unsigned DEFAULT '0' NOT NULL,
	plate_id varchar(255) DEFAULT '' NOT NULL,
	part varchar(255) DEFAULT '' NOT NULL,
	voice varchar(255) DEFAULT '' NOT NULL,
	price int(11) DEFAULT '0' NOT NULL,
	comment text,
	is_piano_reduction smallint(5) unsigned DEFAULT '0' NOT NULL,
	piano_reduction_type varchar(255) DEFAULT '' NOT NULL,
	date_of_publishing date DEFAULT NULL,
	has_negative_store smallint(5) unsigned DEFAULT '0' NOT NULL,
	approve_negative_store smallint(5) unsigned DEFAULT '0' NOT NULL,
	start_store int(11) DEFAULT '0' NOT NULL,
	db_identifier int(11) DEFAULT '0' NOT NULL,
	contained_works int(11) unsigned DEFAULT '0' NOT NULL,
	publisher_actions int(11) unsigned DEFAULT '0' NOT NULL,
    mvdb_id varchar(255) DEFAULT '' NOT NULL

);

#
# Table structure for table 'tx_mpdbcore_domain_model_publisheditem'
#
CREATE TABLE tx_mpdbcore_domain_model_publisheditem (

	title varchar(255) DEFAULT '' NOT NULL,
	piano_combination varchar(255) DEFAULT '' NOT NULL,
	type varchar(255) DEFAULT '' NOT NULL,
	instrumentation varchar(255) DEFAULT '' NOT NULL,
	data_acquisition_certain smallint(5) unsigned DEFAULT '0' NOT NULL,
	related_persons_known smallint(5) unsigned DEFAULT '0' NOT NULL,
	work_examined smallint(5) unsigned DEFAULT '0' NOT NULL,
	data_set_manually_checked smallint(5) unsigned DEFAULT '0' NOT NULL,
	contained_works_identified smallint(5) unsigned DEFAULT '0' NOT NULL,
	responsible_person varchar(255) DEFAULT '' NOT NULL,
	date_of_publishing date DEFAULT NULL,
	final int(11) DEFAULT '0' NOT NULL,
	language varchar(255) DEFAULT '' NOT NULL,
	mvdb_id varchar(255) DEFAULT '' NOT NULL,
	plate_ids varchar(255) DEFAULT '' NOT NULL,
	comment text,
	contained_works int(11) unsigned DEFAULT '0' NOT NULL,
	editors int(11) unsigned DEFAULT '0' NOT NULL,
	instruments int(11) unsigned DEFAULT '0' NOT NULL,
	genre int(11) unsigned DEFAULT '0' NOT NULL,
	first_composer int(11) unsigned DEFAULT '0' NOT NULL,
	publisher_mikro_items int(11) unsigned DEFAULT '0' NOT NULL,
	publisher int(11) unsigned DEFAULT '0'

);


#
# Table structure for table 'tx_mpdbcore_domain_model_publisheraction'
#
CREATE TABLE tx_mpdbcore_domain_model_publisheraction (

	publishedsubitem int(11) unsigned DEFAULT '0' NOT NULL,
	date_of_action date DEFAULT NULL,
	quantity int(11) DEFAULT '0' NOT NULL,
	type varchar(255) DEFAULT '' NOT NULL,
	certain smallint(5) unsigned DEFAULT '0' NOT NULL,
	inferred smallint(5) unsigned DEFAULT '0' NOT NULL,
	in_store smallint(5) unsigned DEFAULT '0' NOT NULL

);

#
# Table structure for table 'tx_mpdbcore_domain_model_publisher'
#
CREATE TABLE tx_mpdbcore_domain_model_publisher (

	name varchar(255) DEFAULT '' NOT NULL,
	shorthand varchar(255) DEFAULT '' NOT NULL,
	location varchar(255) DEFAULT '' NOT NULL,
	alternate_name varchar(255) DEFAULT '' NOT NULL,
	active_from date DEFAULT NULL,
	active_to date DEFAULT NULL,
	responsible_persons int(11) unsigned DEFAULT '0' NOT NULL,
	public smallint(5) unsigned DEFAULT '0' NOT NULL

);

#
# Table structure for table 'tx_mpdbcore_publisheditem_work_mm'
#
CREATE TABLE tx_mpdbcore_publisheditem_work_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_mpdbcore_publishedsubitem_work_mm'
#
CREATE TABLE tx_mpdbcore_publishedsubitem_work_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_mpdbcore_publisheditem_person_mm'
#
CREATE TABLE tx_mpdbcore_publisheditem_person_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_mpdbcore_publisheditem_instrument_mm'
#
CREATE TABLE tx_mpdbcore_publisheditem_instrument_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_mpdbcore_publisheditem_genre_mm'
#
CREATE TABLE tx_mpdbcore_publisheditem_genre_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_mpdbcore_publisheditem_firstcomposer_person_mm'
#
CREATE TABLE tx_mpdbcore_publisheditem_firstcomposer_person_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);
