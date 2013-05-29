atlas
=====

Cron based traceroute with GUI


installation
============

# Add this line to crontab:
  */1 * * * * php PATH_TO_ATLAS/cron.php 

# Import the database file
  mysql < schema.sql


usage
=====

Navigate to http://localhost/atlas/ and add hosts with "Host" -> "Add Host". Then view results or compare them.

screenshot
==========
![Using output to plot hops on Google Map and Highcharts](https://raw.github.com/betim/atlas/master/2013-05-29-110058_1280x1000_scrot.png)
