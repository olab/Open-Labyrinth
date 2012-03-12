USE openlabyrinth;

-- Insert Kohana default users roles
INSERT INTO `roles` (`id`, `name`, `description`) VALUES(1, 'login', 'Login privileges, granted after account confirmation');
INSERT INTO `roles` (`id`, `name`, `description`) VALUES(2, 'admin', 'Administrative user, has access to everything.');

INSERT INTO `languages` (`id`, `name`) VALUES(1, 'en-en');
INSERT INTO `languages` (`id`, `name`) VALUES(2, 'fr-fr');

-- Create default user with login 'admin' and password 'admin'
INSERT INTO `users` (`id`, `name`, `password`, `email`, `language_id`, `nickname`) VALUES(1, 'admin', 'fc4b8662c3f4e840fb29c671134b03dfe2dcc02456e327e275064f35267445f4', 'admin@admin.com', 1, 'administrator');
INSERT INTO `roles_users` (`user_id` , `role_id`) VALUES ('1', '1'), ('1', '2');