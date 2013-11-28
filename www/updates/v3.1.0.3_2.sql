ALTER TABLE  `map_popups_styles` 
ADD  `background_transparent` VARCHAR( 4 ) NOT NULL ,
ADD  `border_transparent` VARCHAR( 4 ) NOT NULL ;

ALTER TABLE  `map_popups` 
ADD  `title_hide` INT( 11 ) NOT NULL ,
ADD  `annotation` VARCHAR( 50 ) NOT NULL ;