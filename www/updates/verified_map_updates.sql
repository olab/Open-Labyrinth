ALTER TABLE `maps` ADD `link_logic` INT( 11 ) NOT NULL AFTER `dev_notes` ,
ADD `node_cont` INT( 11 ) NOT NULL AFTER `link_logic` ,
ADD `clinical_acc` INT( 11 ) NOT NULL AFTER `node_cont` ,
ADD `media_cont` INT( 11 ) NOT NULL AFTER `clinical_acc` ,
ADD `media_copy` INT( 11 ) NOT NULL AFTER `media_cont` ,
ADD `inst_guide` INT( 11 ) NOT NULL AFTER `media_copy` ,
ADD `metadata_file_id` INT( 11 ) NOT NULL AFTER `inst_guide`;