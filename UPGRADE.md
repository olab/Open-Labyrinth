# Upgrading Instructions for OpenLabyrinth

The following upgrading instructions are cumulative. That is, if you want to upgrade from version A to version C and there is version B between A and C, you need to follow the instructions for both A and B.

## General upgrade instructions
1. General upgrade instructions available on https://github.com/olab/Open-Labyrinth/wiki/Updating-Open-Labyrinth

## Upgrade from v3.4.0 to v3.5.0 (currently this is a release-candidate)
1. Check system requirements:
    * PHP 5.5.0 or higher
2. Install Composer (https://getcomposer.org). When Composer has been installed, run `composer install` in the root 
directory of the project (not in the `path_to_project/www/`, but in the `path_to_project/`).
3. Run `php vendor/bin/phinx migrate` in the same directory.

## Upgrade from v3.3.x to v3.4.0
1. Check system requirements:
    * PHP 5.4.0 or higher
    * MySQL server 5.5.3 or higher
    * MySQL client libraries are version 5.5.3 or higher. If you’re using mysqlnd, 5.0.9 or higher.
2. Please copy www/installation/h5p folder and paste it to www/files/
3. Make sure that folder `www/files/h5p` has mode 0775. Execute `chmod -R 775 www/files/h5p` if required.