-- This file migrates a database from the monolothic
-- music publisher database for use with the modularized
-- mpdb system

USE `typo3`;

DROP TABLE tx_dmnorm_domain_model_gndgenre;
DROP TABLE tx_dmnorm_domain_model_gndinstrument;
DROP TABLE tx_dmont_domain_model_genre;
DROP TABLE tx_dmont_domain_model_instrument;
DROP TABLE tx_dmont_domain_model_mediumofperformance;
DROP TABLE tx_dmnorm_domain_model_gndperson;
DROP TABLE tx_dmnorm_domain_model_gndplace;
DROP TABLE tx_mpdbcore_domain_model_publisher;
DROP TABLE tx_mpdbcore_domain_model_publisheditem;
DROP TABLE tx_mpdbcore_domain_model_publishedsubitem;
DROP TABLE tx_dmnorm_domain_model_gndwork;
DROP TABLE tx_dmnorm_gndgenre_gndgenre_mm;
DROP TABLE tx_dmnorm_gndinstrument_gndinstrument_mm;
DROP TABLE tx_dmont_mediumofperformance_instrument_mm;
DROP TABLE tx_dmnorm_gndperson_placeofactivity_place_mm;
DROP TABLE tx_mpdbcore_publisheditem_firstcomposer_person_mm;
DROP TABLE tx_mpdbcore_publisheditem_instrument_mm;
DROP TABLE tx_mpdbcore_publisheditem_person_mm;
DROP TABLE tx_mpdbcore_publisheditem_work_mm;
DROP TABLE tx_mpdbcore_publishedsubitem_work_mm;
DROP TABLE tx_dmont_work_altmediumofperformance_mediumofperformance_mm;
DROP TABLE tx_dmnorm_gndwork_gndgenre_mm;
DROP TABLE tx_dmnorm_gndwork_gndinstrument_mm;
DROP TABLE tx_dmont_work_genre_mm;
DROP TABLE tx_mpdbcore_domain_model_publisheraction;

UPDATE tx_publisherdb_domain_model_form SET pid = 28;
UPDATE tx_publisherdb_domain_model_instrument SET pid = 28;
UPDATE tx_publisherdb_domain_model_mvdbgenre SET pid = 28;
UPDATE tx_publisherdb_domain_model_mvdbinstrument SET pid = 28;
UPDATE tx_publisherdb_domain_model_mvdbinstrumentation SET pid = 28;
UPDATE tx_publisherdb_domain_model_person SET pid = 28;
UPDATE tx_publisherdb_domain_model_place SET pid = 28;
UPDATE tx_publisherdb_domain_model_publisher SET pid = 28;
UPDATE tx_publisherdb_domain_model_publishermakroitem SET pid = 28;
UPDATE tx_publisherdb_domain_model_publishermikroitem SET pid = 28;
UPDATE tx_publisherdb_domain_model_publisheraction SET pid = 28;
UPDATE tx_publisherdb_domain_model_work SET pid = 28;

ALTER TABLE tx_publisherdb_domain_model_form RENAME TO tx_dmnorm_domain_model_gndgenre;
ALTER TABLE tx_publisherdb_domain_model_instrument RENAME TO tx_dmnorm_domain_model_gndinstrument;
ALTER TABLE tx_publisherdb_domain_model_mvdbgenre rename to tx_dmont_domain_model_genre;
ALTER TABLE tx_publisherdb_domain_model_mvdbinstrument RENAME TO tx_dmont_domain_model_instrument;
ALTER TABLE tx_publisherdb_domain_model_mvdbinstrumentation RENAME TO tx_dmont_domain_model_mediumofperformance;
ALTER TABLE tx_publisherdb_domain_model_person RENAME TO tx_dmnorm_domain_model_gndperson;
ALTER TABLE tx_publisherdb_domain_model_place RENAME TO tx_dmnorm_domain_model_gndplace;
ALTER TABLE tx_publisherdb_domain_model_publisher RENAME TO tx_mpdbcore_domain_model_publisher;
ALTER TABLE tx_publisherdb_domain_model_publishermakroitem RENAME TO tx_mpdbcore_domain_model_publisheditem;
ALTER TABLE tx_publisherdb_domain_model_publishermikroitem RENAME TO tx_mpdbcore_domain_model_publishedsubitem;
ALTER TABLE tx_publisherdb_domain_model_publisheraction RENAME TO tx_mpdbcore_domain_model_publisheraction;
ALTER TABLE tx_publisherdb_domain_model_work RENAME TO tx_dmnorm_domain_model_gndwork;
ALTER TABLE tx_publisherdb_form_form_mm RENAME TO tx_dmnorm_gndgenre_gndgenre_mm;
ALTER TABLE tx_publisherdb_instrument_instrument_mm RENAME TO tx_dmnorm_gndinstrument_gndinstrument_mm;
ALTER TABLE tx_publisherdb_mvdbinstrumentation_mvdbinstrument_mm RENAME TO tx_dmont_mediumofperformance_instrument_mm;
ALTER TABLE tx_publisherdb_person_placeofactivity_place_mm RENAME TO tx_dmnorm_gndperson_placeofactivity_place_mm;
ALTER TABLE tx_publisherdb_publishermakroitem_firstcomposer_person_mm RENAME TO tx_mpdbcore_publisheditem_firstcomposer_person_mm;
ALTER TABLE tx_publisherdb_publishermakroitem_instrument_mm RENAME TO tx_mpdbcore_publisheditem_instrument_mm;
ALTER TABLE tx_publisherdb_publishermakroitem_person_mm RENAME TO tx_mpdbcore_publisheditem_person_mm;
ALTER TABLE tx_publisherdb_publishermakroitem_work_mm RENAME TO tx_mpdbcore_publisheditem_work_mm;
ALTER TABLE tx_publisherdb_publishermikroitem_work_mm RENAME TO tx_mpdbcore_publishedsubitem_work_mm;
ALTER TABLE tx_publisherdb_work_altinstrumentation_mvdbinstrumentation_mm RENAME TO tx_dmont_work_altmediumofperformance_mediumofperformance_mm;
ALTER TABLE tx_publisherdb_work_form_mm RENAME TO tx_dmnorm_gndwork_gndgenre_mm;
ALTER TABLE tx_publisherdb_work_instrument_mm RENAME TO tx_dmnorm_gndwork_gndinstrument_mm;
ALTER TABLE tx_publisherdb_work_mvdbgenre_mm RENAME TO tx_dmont_work_genre_mm;

ALTER TABLE tx_mpdbcore_domain_model_publishedsubitem CHANGE `publisher_makro_item` `publisheditem`  INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE tx_dmnorm_domain_model_gndwork CHANGE `genre` `gnd_genres`  INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE tx_mpdbcore_domain_model_publisheditem CHANGE `publisher_mikro_items` `published_subitems`  INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE tx_dmnorm_domain_model_gndgenre CHANGE `super_form` `super_gnd_genre`  INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE tx_mpdbcore_domain_model_publisheraction CHANGE  `publishermikroitem` `publishedsubitem` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE tx_mpdbcore_domain_model_publisher ADD public SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE tx_mpdbcore_domain_model_publisheditem CHANGE published_subitems publishedsubitems INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE tx_dmnorm_domain_model_gndwork DROP COLUMN medium_of_performance;
ALTER TABLE tx_dmnorm_domain_model_gndwork CHANGE main_instrumentation medium_of_performance INT(11) UNSIGNED NOT NULL DEFAULT '0';
