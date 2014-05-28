ALTER TABLE `patient_assign` 
DROP FOREIGN KEY `patient_assign_ibfk_2`,
DROP FOREIGN KEY `patient_assign_ibfk_1`;

ALTER TABLE `patient_condition_change`
DROP FOREIGN KEY `patient_condition_change_ibfk_3`,
DROP FOREIGN KEY `patient_condition_change_ibfk_2`,
DROP FOREIGN KEY `patient_condition_change_ibfk_1`;

ALTER TABLE `patient_condition_relation`
DROP FOREIGN KEY `patient_condition_relation_ibfk_1`,
DROP FOREIGN KEY `patient_condition_relation_ibfk_2`;

ALTER TABLE `patient_map`
DROP FOREIGN KEY `patient_map_ibfk_2`,
DROP FOREIGN KEY `patient_map_ibfk_3`;

ALTER TABLE `patient_relation`
DROP FOREIGN KEY `patient_relation_ibfk_1`,
DROP FOREIGN KEY `patient_relation_ibfk_2`;

ALTER TABLE `patient_sessions`
DROP FOREIGN KEY `patient_sessions_ibfk_1`,
DROP FOREIGN KEY `patient_sessions_ibfk_3`,
DROP FOREIGN KEY `patient_sessions_ibfk_4`;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE `patient`;
DROP TABLE `patient_assign`;
DROP TABLE `patient_condition`;
DROP TABLE `patient_condition_change`;
DROP TABLE `patient_condition_relation`;
DROP TABLE `patient_map`;
DROP TABLE `patient_relation`;
DROP TABLE `patient_relation_rule`;
DROP TABLE `patient_type`;
DROP TABLE `patient_sessions`;
SET FOREIGN_KEY_CHECKS=1;