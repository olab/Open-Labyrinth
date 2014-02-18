CREATE TABLE IF NOT EXISTS 'patient' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'name' varchar(30) NOT NULL,
  PRIMARY KEY ('id'),
  UNIQUE KEY 'name' ('name')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS 'patient_assign' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'id_assign' int(11) NOT NULL,
  'id_group' int(10) unsigned DEFAULT NULL,
  'id_user' int(10) unsigned DEFAULT NULL,
  'user_or_group' enum('user','group') NOT NULL,
  PRIMARY KEY ('id'),
  KEY 'id_group' ('id_group'),
  KEY 'id_user' ('id_user'),
  KEY 'id_assign' ('id_assign')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE 'patient_assign'
  ADD CONSTRAINT 'patient_assign_ibfk_1' FOREIGN KEY ('id_group') REFERENCES 'groups' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT 'patient_assign_ibfk_2' FOREIGN KEY ('id_user') REFERENCES 'users' ('id') ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS 'patient_condition' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'name' varchar(45) NOT NULL,
  'value' int(11) NOT NULL,
  PRIMARY KEY ('id')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS 'patient_condition_change' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'id_node' int(10) unsigned NOT NULL,
  'id_condition' int(11) NOT NULL,
  'id_patient' int(11) NOT NULL,
  'value' int(11) NOT NULL,
  'appear' int(11) NOT NULL,
  PRIMARY KEY ('id'),
  KEY 'id_node' ('id_node','id_condition'),
  KEY 'id_condition' ('id_condition'),
  KEY 'id_patient' ('id_patient')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE 'patient_condition_change'
  ADD CONSTRAINT 'patient_condition_change_ibfk_1' FOREIGN KEY ('id_condition') REFERENCES 'patient_condition' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT 'patient_condition_change_ibfk_2' FOREIGN KEY ('id_node') REFERENCES 'map_nodes' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT 'patient_condition_change_ibfk_3' FOREIGN KEY ('id_patient') REFERENCES 'patient' ('id') ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS 'patient_condition_relation' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'id_patient' int(11) NOT NULL,
  'id_condition' int(11) NOT NULL,
  PRIMARY KEY ('id'),
  KEY 'id_patient' ('id_patient'),
  KEY 'id_condition' ('id_condition'),
  KEY 'id_condition_2' ('id_condition')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE 'patient_condition_relation'
  ADD CONSTRAINT 'patient_condition_relation_ibfk_1' FOREIGN KEY ('id_patient') REFERENCES 'patient' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT 'patient_condition_relation_ibfk_2' FOREIGN KEY ('id_condition') REFERENCES 'patient_condition' ('id') ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS 'patient_map' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'id_map' int(10) unsigned NOT NULL,
  'id_patient' int(11) NOT NULL,
  'queue' int(11) NOT NULL,
  PRIMARY KEY ('id'),
  KEY 'id_map' ('id_map'),
  KEY 'id_patient' ('id_patient')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE 'patient_map'
  ADD CONSTRAINT 'patient_map_ibfk_2' FOREIGN KEY ('id_map') REFERENCES 'maps' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT 'patient_map_ibfk_3' FOREIGN KEY ('id_patient') REFERENCES 'patient' ('id') ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS 'patient_relation' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'id_first_patient' int(11) NOT NULL,
  'id_second_patient' int(11) NOT NULL,
  PRIMARY KEY ('id'),
  KEY 'id_first_patient' ('id_first_patient'),
  KEY 'id_second_patient' ('id_second_patient')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE 'patient_relation'
  ADD CONSTRAINT 'patient_relation_ibfk_1' FOREIGN KEY ('id_first_patient') REFERENCES 'patient' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT 'patient_relation_ibfk_2' FOREIGN KEY ('id_second_patient') REFERENCES 'patient' ('id') ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS 'patient_relation_rule' (
  'id' int(11) NOT NULL,
  'id_patient_relation' int(11) NOT NULL,
  'rule' text NOT NULL,
  'isCorrect' tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS 'patient_sessions' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'id_assign' int(11) NOT NULL,
  'id_patient' int(11) NOT NULL,
  'id_type' int(11) NOT NULL,
  'path' text NOT NULL,
  'patient_condition' text NOT NULL,
  'current_map' int(10) unsigned NOT NULL,
  PRIMARY KEY ('id'),
  KEY 'id_patient' ('id_patient'),
  KEY 'id_user' ('id_assign'),
  KEY 'id_type' ('id_type'),
  KEY 'current_map' ('current_map'),
  KEY 'current_map_2' ('current_map'),
  KEY 'current_map_3' ('current_map')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE 'patient_sessions'
  ADD CONSTRAINT 'patient_sessions_ibfk_1' FOREIGN KEY ('id_patient') REFERENCES 'patient' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT 'patient_sessions_ibfk_3' FOREIGN KEY ('id_type') REFERENCES 'patient_type' ('id') ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT 'patient_sessions_ibfk_4' FOREIGN KEY ('id_assign') REFERENCES 'patient_assign' ('id_assign') ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS 'patient_type' (
  'id' int(11) NOT NULL AUTO_INCREMENT,
  'type' varchar(45) NOT NULL,
  PRIMARY KEY ('id')
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

INSERT INTO 'patient_type' ('id', 'type') VALUES
(1, 'Longitudinal same assign'),
(2, 'Longitudinal different assign'),
(3, 'Parallel same assign'),
(4, 'Parallel different assign');