CREATE TABLE groups (
  id    integer PRIMARY KEY NOT NULL,
  name  char(100) NOT NULL
);

CREATE TABLE languages (
  id     integer PRIMARY KEY NOT NULL,
  name   char(20) NOT NULL,
  "key"  char(20) NOT NULL
);

CREATE TABLE map_avatars (
  id           integer PRIMARY KEY NOT NULL,
  map_id       integer NOT NULL,
  skin_1       char(6),
  skin_2       char(6),
  cloth        char(6),
  nose         char(2),
  hair         char(2),
  environment  char(2),
  accessory_1  char(2),
  bkd          char(6),
  sex          char(2),
  mouth        char(2),
  outfit       char(2),
  bubble       char(2),
  bubble_text  char(100),
  accessory_2  char(2),
  accessory_3  char(2),
  age          char(2),
  eyes         char(2),
  weather      char(2),
  hair_color   char(6),
  /* Foreign keys */
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_chat_elements (
  id        integer PRIMARY KEY NOT NULL,
  chat_id   integer NOT NULL,
  question  text NOT NULL,
  response  text NOT NULL,
  function  char(10) NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (chat_id)
    REFERENCES map_chats(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_chats (
  id          integer PRIMARY KEY NOT NULL,
  map_id      integer NOT NULL,
  counter_id  integer,
  stem        text NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_collectionMaps (
  id             integer PRIMARY KEY NOT NULL,
  collection_id  integer NOT NULL,
  map_id         integer NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (collection_id)
    REFERENCES map_collections(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_collections (
  id    integer PRIMARY KEY NOT NULL,
  name  char(200)
);

CREATE TABLE map_contributor_roles (
  id           integer PRIMARY KEY NOT NULL,
  name         char(100) NOT NULL,
  description  text NOT NULL
);

CREATE TABLE user_bookmarks (
  id           integer PRIMARY KEY NOT NULL,
  session_id  integer NOT NULL,
  time_stamp integer NOT NULL,
  node_id  integer NOT NULL
);

CREATE TABLE map_contributors (
  id            integer PRIMARY KEY NOT NULL,
  map_id        integer NOT NULL,
  role_id       integer NOT NULL,
  name          char(200) NOT NULL,
  organization  char(200) NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_counter_rule_relations (
  id     integer PRIMARY KEY NOT NULL,
  title  char(70) NOT NULL,
  value  char(50) NOT NULL
);

CREATE TABLE map_counter_rules (
  id                integer PRIMARY KEY NOT NULL,
  counter_id        integer NOT NULL,
  relation_id       integer NOT NULL,
  value             integer NOT NULL,
  function          char(50),
  redirect_node_id  integer,
  message           text,
  counter           integer,
  counter_value     char(50),
  /* Foreign keys */
  FOREIGN KEY (counter_id)
    REFERENCES map_counters(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_counters (
  id           integer PRIMARY KEY NOT NULL,
  map_id       integer,
  name         char(200),
  description  text,
  start_value  integer NOT NULL,
  icon_id      integer,
  prefix       char(20),
  suffix       char(20),
  visible      smallint,
  out_of       integer,
  /* Foreign keys */
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_dam_elements (
  id            integer PRIMARY KEY NOT NULL,
  dam_id        integer NOT NULL,
  element_type  char(20),
  "order"       integer,
  display       char(20) NOT NULL,
  element_id    integer NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (dam_id)
    REFERENCES map_dams(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_dams (
  id      integer PRIMARY KEY NOT NULL,
  map_id  integer NOT NULL,
  name    text,
  /* Foreign keys */
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_elements (
  id           integer PRIMARY KEY NOT NULL,
  map_id       integer NOT NULL,
  mime         text,
  name         char(200) NOT NULL,
  path         text NOT NULL,
  args         char(100),
  width        integer,
  width_type   char(2) NOT NULL,
  height       integer,
  height_type  char(2) NOT NULL,
  h_align      char(20),
  v_align      char(20),
  /* Foreign keys */
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_feedback_operators (
  id     integer PRIMARY KEY NOT NULL,
  title  char(100) NOT NULL,
  value  char(50) NOT NULL
);

CREATE TABLE map_feedback_rules (
  id            integer PRIMARY KEY NOT NULL,
  map_id        integer NOT NULL,
  rule_type_id  integer NOT NULL,
  value         integer,
  operator_id   integer,
  message       text,
  counter_id    integer,
  /* Foreign keys */
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_feedback_types (
  id           integer PRIMARY KEY NOT NULL,
  name         char(100),
  description  text
);

CREATE TABLE map_keys (
  id      integer PRIMARY KEY NOT NULL,
  map_id  integer NOT NULL,
  "key"   char(50) NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_node_counters (
  id          integer PRIMARY KEY NOT NULL,
  node_id     integer NOT NULL,
  counter_id  integer NOT NULL,
  function    char(20) NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (id)
    REFERENCES map_node_counters(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (node_id)
    REFERENCES map_nodes(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_node_link_stylies (
  id           integer PRIMARY KEY NOT NULL,
  name         char(70) NOT NULL,
  description  text NOT NULL
);

CREATE TABLE map_node_link_types (
  id           integer PRIMARY KEY NOT NULL,
  name         char(50) NOT NULL,
  description  text NOT NULL
);

CREATE TABLE map_node_links (
  id           integer PRIMARY KEY NOT NULL,
  map_id       integer NOT NULL,
  node_id_1    integer NOT NULL,
  node_id_2    integer NOT NULL,
  image_id     integer,
  "text"       text,
  "order"      integer,
  probability  integer,
  /* Foreign keys */
  FOREIGN KEY (id)
    REFERENCES map_node_links(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (node_id_1)
    REFERENCES map_nodes(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (node_id_2)
    REFERENCES map_nodes(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_node_priorities (
  id           integer PRIMARY KEY NOT NULL,
  name         char(70) NOT NULL,
  description  text NOT NULL
);

CREATE TABLE map_node_section_nodes (
  id          integer PRIMARY KEY NOT NULL,
  section_id  integer NOT NULL,
  node_id     integer NOT NULL,
  "order"     integer NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (section_id)
    REFERENCES map_node_sections(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (node_id)
    REFERENCES map_nodes(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_node_sections (
  id      integer PRIMARY KEY NOT NULL,
  name    char(50) NOT NULL,
  map_id  integer NOT NULL
);

CREATE TABLE map_node_types (
  id           integer PRIMARY KEY NOT NULL,
  name         char(70) NOT NULL,
  description  text NOT NULL
);

CREATE TABLE map_nodes (
  id                   integer PRIMARY KEY NOT NULL,
  map_id               integer NOT NULL,
  title                char(200),
  "text"               text,
  content              text,
  type_id              integer,
  probability          smallint,
  conditional          text,
  conditional_message  text,
  info                 text,
  link_style_id        integer,
  link_type_id         integer,
  priority_id          integer,
  kfp                  smallint,
  undo                 smallint,
  "end"                smallint,
  x                    numeric(15),
  y                    numeric(15),
  rgb                  char(8),
  /* Foreign keys */
  FOREIGN KEY (type_id)
    REFERENCES map_node_types(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_presentation_maps (
  id               integer PRIMARY KEY NOT NULL,
  presentation_id  integer NOT NULL,
  map_id           integer NOT NULL,
  "order"          integer,
  /* Foreign keys */
  FOREIGN KEY (presentation_id)
    REFERENCES map_presentations(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_presentation_users (
  id               integer PRIMARY KEY NOT NULL,
  presentation_id  integer NOT NULL,
  user_id          integer NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (presentation_id)
    REFERENCES map_presentations(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_presentations (
  id          integer PRIMARY KEY NOT NULL,
  title       text,
  header      text,
  footer      text,
  skin_id     integer,
  access      integer,
  login       integer,
  "order"     integer,
  user_id     integer,
  start_date  numeric(15),
  end_date    numeric(15),
  tries       integer
);

CREATE TABLE map_question_responses (
  id           integer PRIMARY KEY NOT NULL,
  question_id  integer NOT NULL,
  response     char(250),
  feedback     text,
  is_correct   smallint,
  score        integer,
  /* Foreign keys */
  FOREIGN KEY (question_id)
    REFERENCES map_questions(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_question_types (
  id             integer PRIMARY KEY NOT NULL,
  title          char(70),
  value          char(20),
  template_name  char(200) NOT NULL,
  template_args  char(100)
);

CREATE TABLE map_questions (
  id             integer PRIMARY KEY NOT NULL,
  map_id         integer NOT NULL,
  stem           text,
  entry_type_id  integer NOT NULL,
  width          integer NOT NULL,
  height         integer NOT NULL,
  feedback       text,
  show_answer    smallint NOT NULL,
  counter_id     integer,
  num_tries      integer NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_sections (
  id           integer PRIMARY KEY NOT NULL,
  name         char(100) NOT NULL,
  description  text NOT NULL
);

CREATE TABLE map_securities (
  id           integer PRIMARY KEY NOT NULL,
  name         char(100) NOT NULL,
  description  text NOT NULL
);

CREATE TABLE map_skins (
  id    integer PRIMARY KEY NOT NULL,
  name  char(100) NOT NULL,
  path  char(200) NOT NULL
);

CREATE TABLE map_types (
  id           integer PRIMARY KEY NOT NULL,
  name         char(100) NOT NULL,
  description  text NOT NULL
);

CREATE TABLE map_users (
  id       integer PRIMARY KEY NOT NULL,
  map_id   integer NOT NULL,
  user_id  integer NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_vpd_elements (
  id      integer PRIMARY KEY NOT NULL,
  vpd_id  integer NOT NULL,
  "key"   char(100) NOT NULL,
  value   text NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (vpd_id)
    REFERENCES map_vpds(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE map_vpd_types (
  id     integer PRIMARY KEY NOT NULL,
  name   char(100) NOT NULL,
  label  text NOT NULL
);

CREATE TABLE map_vpds (
  id           integer PRIMARY KEY NOT NULL,
  map_id       integer NOT NULL,
  vpd_type_id  integer NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (vpd_type_id)
    REFERENCES map_vpd_types(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE maps (
  id           integer PRIMARY KEY NOT NULL,
  name         char(200) NOT NULL,
  author_id    integer NOT NULL,
  abstract     text NOT NULL,
  startScore   integer NOT NULL,
  threshold    integer NOT NULL,
  keywords     text NOT NULL,
  type_id      integer NOT NULL,
  units        char(10) NOT NULL,
  security_id  integer NOT NULL,
  guid         char(50) NOT NULL,
  timing       smallint NOT NULL,
  delta_time   integer NOT NULL,
  show_bar     smallint NOT NULL,
  show_score   smallint NOT NULL,
  skin_id      integer NOT NULL,
  enabled      smallint NOT NULL,
  section_id   integer NOT NULL,
  language_id  integer,
  feedback     text NOT NULL,
  dev_notes    text NOT NULL,
  source       char(50) NOT NULL,
  source_id    integer NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (language_id)
    REFERENCES languages(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (section_id)
    REFERENCES map_sections(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (security_id)
    REFERENCES map_securities(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (type_id)
    REFERENCES map_types(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (author_id)
    REFERENCES users(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE remoteMaps (
  id          integer PRIMARY KEY NOT NULL,
  service_id  integer NOT NULL,
  map_id      integer NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (service_id)
    REFERENCES remoteServices(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE remoteServices (
  id    integer PRIMARY KEY NOT NULL,
  name  char(100) NOT NULL,
  type  char(1) NOT NULL,
  ip    char(50) NOT NULL
);

CREATE TABLE user_groups (
  id        integer PRIMARY KEY NOT NULL,
  user_id   integer NOT NULL,
  group_id  integer NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (group_id)
    REFERENCES groups(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE user_responses (
  id           integer PRIMARY KEY NOT NULL,
  question_id  integer NOT NULL,
  session_id   integer NOT NULL,
  response     text,
  /* Foreign keys */
  FOREIGN KEY (question_id)
    REFERENCES map_questions(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (session_id)
    REFERENCES user_sessions(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE user_sessions (
  id          integer PRIMARY KEY NOT NULL,
  user_id     integer NOT NULL,
  map_id      integer NOT NULL,
  start_time  integer NOT NULL,
  user_ip     char(50) NOT NULL
);

CREATE TABLE user_sessiontraces (
  id             integer PRIMARY KEY NOT NULL,
  session_id     integer NOT NULL,
  user_id        integer NOT NULL,
  map_id         integer NOT NULL,
  node_id        integer NOT NULL,
  counters       text,
  date_stamp     numeric(15),
  confidence     smallint,
  dams           text,
  bookmark_made  integer,
  bookmark_used  integer,
  /* Foreign keys */
  FOREIGN KEY (node_id)
    REFERENCES map_nodes(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (map_id)
    REFERENCES maps(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (session_id)
    REFERENCES user_sessions(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

CREATE TABLE user_types (
  id           integer PRIMARY KEY NOT NULL,
  name         char(30) NOT NULL,
  description  char(100)
);

CREATE TABLE users (
  id           integer PRIMARY KEY NOT NULL,
  username     char(40) NOT NULL,
  password     text NOT NULL,
  email        char(250) NOT NULL,
  nickname     char(120) NOT NULL,
  language_id  integer NOT NULL,
  type_id      integer NOT NULL,
  /* Foreign keys */
  FOREIGN KEY (language_id)
    REFERENCES languages(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION, 
  FOREIGN KEY (type_id)
    REFERENCES user_types(id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

/*COMMIT;*/

BEGIN TRANSACTION;
INSERT INTO languages ("id", "name", "key") VALUES (1, 'EN', 'en-en');
INSERT INTO languages ("id", "name", "key") VALUES (2, 'FR', 'fr-fr');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_contributor_roles ("id", "name", "description") VALUES (1, 'author', '');
INSERT INTO map_contributor_roles ("id", "name", "description") VALUES (2, 'publisher', '');
INSERT INTO map_contributor_roles ("id", "name", "description") VALUES (3, 'initiator', '');
INSERT INTO map_contributor_roles ("id", "name", "description") VALUES (4, 'validator', '');
INSERT INTO map_contributor_roles ("id", "name", "description") VALUES (5, 'editor', '');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_counter_rule_relations ("id", "title", "value") VALUES (1, 'equal to', 'eq');
INSERT INTO map_counter_rule_relations ("id", "title", "value") VALUES (2, 'not equal to', 'neq');
INSERT INTO map_counter_rule_relations ("id", "title", "value") VALUES (3, 'less than or equal to', 'leq');
INSERT INTO map_counter_rule_relations ("id", "title", "value") VALUES (4, 'less than', 'lt');
INSERT INTO map_counter_rule_relations ("id", "title", "value") VALUES (5, 'greater that oe qual to', 'geq');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_feedback_operators ("id", "title", "value") VALUES (1, 'equal to', 'eq');
INSERT INTO map_feedback_operators ("id", "title", "value") VALUES (2, 'not equal to', 'neq');
INSERT INTO map_feedback_operators ("id", "title", "value") VALUES (3, 'less than equal to', 'leq');
INSERT INTO map_feedback_operators ("id", "title", "value") VALUES (4, 'less than', 'lt');
INSERT INTO map_feedback_operators ("id", "title", "value") VALUES (5, 'greater than or equal to', 'geq');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_feedback_types ("id", "name", "description") VALUES (1, 'time taken', NULL);
INSERT INTO map_feedback_types ("id", "name", "description") VALUES (2, 'counter value', NULL);
INSERT INTO map_feedback_types ("id", "name", "description") VALUES (3, 'node visit', NULL);
INSERT INTO map_feedback_types ("id", "name", "description") VALUES (4, 'must visit', NULL);
INSERT INTO map_feedback_types ("id", "name", "description") VALUES (5, 'must avoid', NULL);
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_node_link_stylies ("id", "name", "description") VALUES (1, 'text (default)', '');
INSERT INTO map_node_link_stylies ("id", "name", "description") VALUES (2, 'dropdown', '');
INSERT INTO map_node_link_stylies ("id", "name", "description") VALUES (3, 'dropdown + confidence', '');
INSERT INTO map_node_link_stylies ("id", "name", "description") VALUES (4, 'type in text', '');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_node_link_types ("id", "name", "description") VALUES (1, 'ordered', '');
INSERT INTO map_node_link_types ("id", "name", "description") VALUES (2, 'random order', '');
INSERT INTO map_node_link_types ("id", "name", "description") VALUES (3, 'random select one *', '');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_node_priorities ("id", "name", "description") VALUES (1, 'normal (default)', '');
INSERT INTO map_node_priorities ("id", "name", "description") VALUES (2, 'must avoid', '');
INSERT INTO map_node_priorities ("id", "name", "description") VALUES (3, 'must visit', '');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_node_types ("id", "name", "description") VALUES (1, 'root', '');
INSERT INTO map_node_types ("id", "name", "description") VALUES (2, 'child', '');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_question_types ("id", "title", "value", "template_name", "template_args") VALUES (1, 'single line text entry - not assessd', 'text', 'text', NULL);
INSERT INTO map_question_types ("id", "title", "value", "template_name", "template_args") VALUES (2, 'multi-line text entry - not assessed', 'area', 'area', NULL);
INSERT INTO map_question_types ("id", "title", "value", "template_name", "template_args") VALUES (3, 'multiple choice - two options', 'mcq2', 'response', '2');
INSERT INTO map_question_types ("id", "title", "value", "template_name", "template_args") VALUES (4, 'multiple choice - three options', 'mcq3', 'response', '3');
INSERT INTO map_question_types ("id", "title", "value", "template_name", "template_args") VALUES (5, 'multiple choice - five options', 'mcq5', 'response', '5');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_sections ("id", "name", "description") VALUES (1, 'don&#039;t show', '');
INSERT INTO map_sections ("id", "name", "description") VALUES (2, 'visible', '');
INSERT INTO map_sections ("id", "name", "description") VALUES (3, 'navigable', '');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_securities ("id", "name", "description") VALUES (1, 'open access', '');
INSERT INTO map_securities ("id", "name", "description") VALUES (2, 'closed (only logged in Labyrinth users can see it)', '');
INSERT INTO map_securities ("id", "name", "description") VALUES (3, 'private (only registered authors and users can see it)', '');
INSERT INTO map_securities ("id", "name", "description") VALUES (4, 'keys (a key is required to access this Labyrinth) - <a href=''editKeys''>edit</a>', '');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_skins ("id", "name", "path") VALUES (1, 'Basic', 'basic/basic');
INSERT INTO map_skins ("id", "name", "path") VALUES (2, 'Basic Exam', '');
INSERT INTO map_skins ("id", "name", "path") VALUES (3, 'NOSM', '');
INSERT INTO map_skins ("id", "name", "path") VALUES (4, 'PINE', '');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_types ("id", "name", "description") VALUES (1, 'Labyrinth Skin', '');
INSERT INTO map_types ("id", "name", "description") VALUES (2, 'Game - scores, 1 startpoint, 1 or more endpoints', '');
INSERT INTO map_types ("id", "name", "description") VALUES (3, 'Maze - no scores, 1 or more startpoints, no endpoints', '');
INSERT INTO map_types ("id", "name", "description") VALUES (4, 'Algorithm - no scores, 1 startpoint, 1 or more endpoints', '');
INSERT INTO map_types ("id", "name", "description") VALUES (5, 'Key Feature Problem', '');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO map_vpd_types ("id", "name", "label") VALUES (1, 'VPDText', 'Text');
INSERT INTO map_vpd_types ("id", "name", "label") VALUES (2, 'PatientDiagnoses', 'Patient Demographics');
INSERT INTO map_vpd_types ("id", "name", "label") VALUES (3, 'AuthorDiagnoses', 'Author Diagnosis');
INSERT INTO map_vpd_types ("id", "name", "label") VALUES (4, 'Medication', 'Medication');
INSERT INTO map_vpd_types ("id", "name", "label") VALUES (5, 'InterviewItem', 'Question');
COMMIT;
BEGIN TRANSACTION;
INSERT INTO user_types ("id", "name", "description") VALUES (1, 'learner', NULL);
INSERT INTO user_types ("id", "name", "description") VALUES (2, 'author', NULL);
INSERT INTO user_types ("id", "name", "description") VALUES (3, 'reviewer', NULL);
INSERT INTO user_types ("id", "name", "description") VALUES (4, 'superuser', NULL);
INSERT INTO user_types ("id", "name", "description") VALUES (5, 'remote service', NULL);
COMMIT;
BEGIN TRANSACTION;
INSERT INTO users ("id", "username", "password", "email", "nickname", "language_id", "type_id") VALUES (1, 'admin', 'bf7bdf17dad6154e88bf66b9768174a47658e84baa1036c3f6f0cbeae5be1db7', 'admin@admin.com', 'administrator', 1, 4);
COMMIT;
