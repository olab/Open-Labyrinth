ALTER TABLE  `webinar_maps` ADD  `which` ENUM(  'labyrinth',  'section' ) NOT NULL AFTER  `webinar_id` ;
ALTER TABLE  `webinar_maps` CHANGE  `map_id`  `reference_id` INT NOT NULL ;
ALTER TABLE  `map_node_section_nodes` ADD  `node_type` ENUM(  'regular', 'in', 'out' ) NOT NULL AFTER  `order` ;
ALTER TABLE  `map_node_section_nodes` CHANGE  `node_id`  `node_id` INT( 10 ) UNSIGNED NOT NULL ;
ALTER TABLE  `map_node_section_nodes` ADD FOREIGN KEY (  `node_id` ) REFERENCES  `map_nodes` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;