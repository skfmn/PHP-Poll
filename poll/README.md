# PHP-Poll

Installation Instructions

Before you start you must create an MySQL Database, if your not sure how ask your hosting provider.

Be sure to copy the information they give you as you will need it during installation.

Once you have created the Database, Upload the "poll" folder and all of it contents to the Root folder of your website.

:IMPORTANT: Make sure you have 'write' permissions on the poll folder after you upload it! :IMPORTANT:

Navigate to http://www.yourwebsite.com/poll/install/install.php and follow the instructions.

Once installation is done you can login using "admin" for both user name and password

Be sure to change your password once you login.

Add the code below where you want the Poll to appear on your webpage.

<?php include "/poll/poll.php" ?>

You can wrap the code above in a span or div tag and use CSS to define the objects in the code.
You can define most attributes like fonts, alignments and such!

The Poll HTML is located in includes/functions.com file and can be modified to fit your website.