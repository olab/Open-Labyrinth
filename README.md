OpenLabyrinth relaese candidate PHP7
==============
OpenLabyrinth is an open-source (GNU-GPL3 licence) platform for creating and playing virtual patients. 

For more information about OpenLabyrinth generally, see http://openlabyrinth.ca

Those are notes about installing this release candidate

## System Requirements

* Tested ONLY under Ubuntu 18.04 with PHP 7.2

LAMP with PHP 7

composer (if you have sudo privileges, install global: 
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer 

Otherwise see Requirements [here](https://github.com/olab/Open-Labyrinth/wiki/System-Requirements)


## Installation instructions

almost as in [here](https://github.com/olab/Open-Labyrinth/wiki/Installing-Open-Labyrinth):

download and unzip this branch, cd into it

run "composer install" (and ust to make sure, run "composer update" too)

point your browser to your Olab instance. run the installer

feel free to report errors and submit patches...
