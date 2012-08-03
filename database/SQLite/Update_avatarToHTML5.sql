DROP TABLE IF EXISTS `map_avatars`;

CREATE TABLE map_avatars (
  id           integer PRIMARY KEY NOT NULL,
  map_id       integer NOT NULL,
  skin_1       char(6),
  skin_2       char(6),
  cloth        char(6),
  nose         char(20),
  hair         char(20),
  environment  char(20),
  accessory_1  char(20),
  bkd          char(6),
  sex          char(20),
  mouth        char(20),
  outfit       char(20),
  bubble       char(20),
  bubble_text  char(100),
  accessory_2  char(20),
  accessory_3  char(20),
  age          char(2),
  eyes         char(20),
  hair_color   char(6),
  image        char(100),
  /* Foreign keys */
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);