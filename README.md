OpenLabyrinth
==============
OpenLabyrinth is an open-source (GNU-GPL3 licence) platform for creating and playing virtual patients. 

For more information about OpenLabyrinth generally, see http://openlabyrinth.ca

You do not need any software to play a case. Any modern web browser will work. If you want to try authoring your own cases,
OpenLabryinth can be installed on a basic web server running Apache, MySQL, and PHP (eg a LAMP, WAMP setup etc.) 

If you are comfortable using Git to clone, pull etc, this will work fine. For less experienced users, an alternative is to 
download version 3.1 as a zip file (see the Releases tab). Expand that zip file into a set of folders accessible to your 
Apache server. 

The install routines are still a bit glitchy - we apologize for this. Linux experts should have no trouble. The rest of us 
mortals might have a hiccup or two. 

Version 3.1 is very stable and recommended for production settings. 

If you want to see some of the latest development features, send us a note via info AT openlabyrinth DOT ca and we can set 
you up with a test account at http://demo.openlabyrinth.ca/
===================================================================

Open Labyrinth, Basic LTI Tool Provider
---------------------------------------------

Extends Open Labyrinth v3.1 platform by:

-Modified index.php
-Added file: lti, path:  www/application/classes

File ims-bli (IMS-LTI client libraries) was downloaded from:
http://developers.imsglobal.org/phpcode.html
and is distributed under the MIT licence - 

Copyright (c) 2007 Andy Smith

-----------------------------------------------


Installation:

1. Install Open Labyrinth 3.1
2. Create a new entry to the table oauth_providers as following:

id: 9
name: consumer
verson: 1.0
icon: -
appId: insert the value of the "key"
secret: insert the value of the "secret"

3. In database.php adjust the database details (lines 5-8)

4. Replace the index.php with the one in this branch and copy the "lti" files

------------------------------------------------

In the "Tool Consumer's" side:

1. Use the same credentials (key, secret)

2. Include the URL pointing to Open Labyrinth (launch URL) in the form: [public_ip]/index.php?oauth=consumer 
