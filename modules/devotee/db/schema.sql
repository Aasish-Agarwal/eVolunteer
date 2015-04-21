CREATE TABLE  _centre (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(45) NOT NULL,
  PRIMARY KEY (id) USING BTREE,
  UNIQUE KEY Index_2 (name) USING BTREE
) ;


CREATE TABLE  _centre_program (
  centre_id int(10) unsigned NOT NULL,
  prog_id int(10) unsigned NOT NULL,
  PRIMARY KEY (centre_id,prog_id)
) ;


CREATE TABLE  _event (
  centre_id int(10) unsigned NOT NULL,
  prog_id int(10) unsigned NOT NULL,
  event_date bigint(20) unsigned NOT NULL,
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (id),
  UNIQUE KEY Index_1 (centre_id,prog_id,event_date)
) ;

CREATE TABLE  _event_attendence (
  event_id int(10) unsigned NOT NULL,
  devotee_id int(10) unsigned NOT NULL,
  PRIMARY KEY (event_id,devotee_id)
) ;

CREATE TABLE  _program (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(45) NOT NULL,
  PRIMARY KEY (id) USING BTREE,
  UNIQUE KEY Index_2 (name) USING BTREE
) ;

CREATE TABLE  _program_enrollment (
  centre_id int(10) unsigned NOT NULL,
  prog_id int(10) unsigned NOT NULL,
  devotee_id int(10) unsigned NOT NULL,
  PRIMARY KEY (centre_id,prog_id,devotee_id)
) ;

