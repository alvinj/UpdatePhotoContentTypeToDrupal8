# Update Drupal Photos (D6 to D8)


## Introduction

This is a little PHP/MySQL project that attempts to upgrade a Drupal 6 "Photo" content type to a Drupal 8 "PhotoD8" content type.


## Discussion

Under the section "Files and Images," the Drupal.org node titled [Known issues when upgrading from Drupal 6 or 7 to Drupal 8](https://www.drupal.org/node/2167633) states:

>"Images attached to Drupal 6 Image nodes, and files attached with File fields do not get migrated."

This is a huge problem for me, as I have over 1,700 photos posted on alvinalexander.com alone. So I'm trying to create a solution for this problem.


## Possible Solution (not working yet)

My attempt at a solution goes like this. First, start with a D6 to D8 migration:

* On my D6 website I created a simple _Photo_ content type (per the "Using Drupal" book, if I remember right). It has one field, with a Label of "Photo", Name of "field_photo", and Type of "File".
* Perform a Standard Drupal 6 (D6) to Drupal 8 (D8) migration, keeping the original Photo content type and data intact.
* Confirm that `node__field_photo` and `node_revision__field_photo` are in the D8 database, and the data is populated.
* Confirm that the image files are on disk. The file locations may be different, but they reflect what is shown in the D8 database.

Now work on the solution:

* Create a new "PhotoD8" content type in the D8 installation.
* Migrate the data from the old "Photo" database tables to the new "PhotoD8" tables using SQL queries.

The PhotoD8 content type has these fields:

* Label = "Body", Machine Name = "body", Field Type = "Text (formatted, long, with summary)"
* Label = "Photo D8", Machine Name = "field_photo_d8", Field Type = "Image"


## Can't Get the Solution to Work

I've been trying this, and so far I've been able to get it to this point:

* The node body text shows up at the old D6 URL on the D8 site
* The image does not show up
* When I click the "Edit" link on that node, the Edit form shows "Edit PhotoD8 ...", but it does not show the image

At some point I also do something that makes the body text go away, but I don't know what that is. It may happen when I run `drush cr`, or something else.


## A PHP Script to Update the Photos

The [UpdatePhotos.php](UpdatePhotos.php) file in this project contains the source code for a PHP script I've been trying to write to automate the migration of the D6 photos to the D8 PhotoD8 content type, but so far it's not working. 

The basic idea of that script is that I start with a value from `field_photo_target_id` in the `node__field_photo` table, and then run the script using that idea. For instance, the last attempt at the time of this writing started with a `field_photo_target_id` value of `2218`, and the script goes from there.


## The Raw SQL Queries

While the UpdatePhotos.php file shows my latest attempt(s), this code gives you an idea of the raw MySQL queries I was running during my initial tests. In these queries, `7681` was the revision_id of the original D6 node:

````
UPDATE `node` SET type='photod8' WHERE vid=7681;

UPDATE `node__body` SET bundle='photod8' WHERE revision_id=7681;

INSERT INTO node__field_photo_d8 (bundle, deleted, entity_id, revision_id, langcode, delta, field_photo_d8_target_id, field_photo_d8_alt, field_photo_d8_title, field_photo_d8_width, field_photo_d8_height) VALUES ('photod8', 0, 7493, 7681, 'en', 0, 2187, 'alt', 'title', 420, 462);

INSERT INTO node_revision__field_photo_d8
  (bundle, deleted, entity_id, revision_id, langcode, delta, field_photo_d8_target_id,
  field_photo_d8_alt, field_photo_d8_title, field_photo_d8_width, field_photo_d8_height)
  VALUES ('photod8', 0, 7493, 7681, 'en', 0, 2187, 'alt', 'title', 420, 462)

UPDATE node__comment SET bundle = 'photod8' WHERE revision_id = 7681;
UPDATE node__tags1 SET bundle = 'photod8' WHERE revision_id = 7681;
UPDATE node_field_data set type = 'photod8' WHERE vid = 7681;
UPDATE node_revision__body set bundle = 'photod8' WHERE revision_id = 7681;
UPDATE node_revision__comment set bundle = 'photod8' WHERE revision_id = 7681;
UPDATE node_revision__tags1 set bundle = 'photod8' WHERE revision_id = 7681;
````

FWIW, I came up with these queries by running a series of queries against the entire D8 database using PhpMyAdmin.


## Help!

If anyone can see why this isn't working, I'd appreciate your help. I don't know if I'm just missing something, or if this isn't possible through database queries alone for some reason.



## Latest Effort: MySQL Database Diffs

My latest effort was to: 

* Take a snapshot of the MySQL D8 database
* Add a new photo using the D8 "PhotoD8" content type
* Take another snapshot of the D8 database
* Weed out the junk and take a look at the diffs of the database snapshots

This shows the result of that effort. I'm currently trying to compare my queries against this:

````
< INSERT INTO `file_managed` VALUES (2221,'6e6fc9c2-b83b-4f10-b019-39e0ac7c2586','en',1,'dick-tidrow-cubbies.jpg','public://2016-05/dick-tidrow-cubbies.jpg','image/jpeg',30718,1,1462647881,1462647912);
< INSERT INTO `file_usage` VALUES (2221,'file','node','7550',1);
< INSERT INTO `history` VALUES (1,7550,1462647914);
< INSERT INTO `key_value` VALUES ('state','system.cron_last','i:1462647842;');
> INSERT INTO `key_value` VALUES ('state','system.cron_last','i:1462580283;');
< INSERT INTO `node` VALUES (7550,7740,'photod8','c37a1d9e-a610-4052-b9e0-515764479e13','en');
< INSERT INTO `node__body` VALUES ('photod8',0,7550,7740,'en',0,'Dick Tidrow of the Chicago Cubbies.','','anonymous_format');
< INSERT INTO `node__field_photo_d8` VALUES ('photod8',0,7550,7740,'en',0,2221,'Dick Tidrow','',576,250);
< INSERT INTO `node_field_data` VALUES (7550,7740,'photod8','en','Dick Tidrow',1,1,1462647847,1462647912,1,0,1,1);
< INSERT INTO `node_field_revision` VALUES (7550,7740,'en','Dick Tidrow',1,1,1462647847,1462647912,1,0,1,1);
< INSERT INTO `node_revision` VALUES (7550,7740,'en',1462647881,1,NULL);
< INSERT INTO `node_revision__body` VALUES ('photod8',0,7550,7740,'en',0,'Dick Tidrow of the Chicago Cubbies.','','anonymous_format');
< INSERT INTO `node_revision__field_photo_d8` VALUES ('photod8',0,7550,7740,'en',0,2221,'Dick Tidrow','',576,250);
< INSERT INTO `sessions` VALUES (1,'Lz8XFh_YLwOyWnReQwV5C0UMzy0ZuKVihvAQg-wskHw','127.0.0.1',1462647913,'_sf2_attributes|a:1:{s:3:\"uid\";s:1:\"1\";}_sf2_flashes|a:0:{}_sf2_meta|a:4:{s:1:\"u\";i:1462647843;s:1:\"c\";i:1462482674;s:1:\"l\";s:7:\"2000000\";s:1:\"s\";s:43:\"4BwMl1ZaoN3_htnJn5cKC9C5fe10ZMC11XLH_oKLtBo\";}dblog_overview_filter|a:1:{s:4:\"type\";a:1:{s:17:\"migrate_drupal_ui\";s:17:\"migrate_drupal_ui\";}}');
> INSERT INTO `sessions` VALUES (1,'Lz8XFh_YLwOyWnReQwV5C0UMzy0ZuKVihvAQg-wskHw','127.0.0.1',1462590516,'_sf2_attributes|a:1:{s:3:\"uid\";s:1:\"1\";}_sf2_flashes|a:0:{}_sf2_meta|a:4:{s:1:\"u\";i:1462590516;s:1:\"c\";i:1462482674;s:1:\"l\";s:7:\"2000000\";s:1:\"s\";s:43:\"4BwMl1ZaoN3_htnJn5cKC9C5fe10ZMC11XLH_oKLtBo\";}dblog_overview_filter|a:1:{s:4:\"type\";a:1:{s:17:\"migrate_drupal_ui\";s:17:\"migrate_drupal_ui\";}}');
< INSERT INTO `url_alias` VALUES (18107,'/node/7550','/photos/dick-tidrow-cubbies','en');
< INSERT INTO `users_field_data` VALUES (1,'en','',NULL,'ddadmin911','$S$EicglgEJRpruyvcw6RQ.vTq4zN3SuZ/XOKLWxGOu55VCdIoboM8g','devdaily@gmail.com','Europe/London',1,1247936247,1462482938,1462647842,1462482027,'devdaily@gmail.com',1);
> INSERT INTO `users_field_data` VALUES (1,'en','',NULL,'ddadmin911','$S$EicglgEJRpruyvcw6RQ.vTq4zN3SuZ/XOKLWxGOu55VCdIoboM8g','devdaily@gmail.com','Europe/London',1,1247936247,1462482938,1462590516,1462482027,'devdaily@gmail.com',1);
< INSERT INTO `watchdog` VALUES (234,0,'filter','Disabled text format: %format.','a:1:{s:7:\"%format\";s:10:\"full_html1\";}',1,'','http://aad8:8888/node/add','http://aad8:8888/admin/content?status=All&type=photod8&title=&langcode=All','127.0.0.1',1462647844);
< INSERT INTO `watchdog` VALUES (235,0,'cron','Attempting to re-run cron while it is already running.','a:0:{}',4,'','http://aad8:8888/node/add/photod8','http://aad8:8888/node/add','127.0.0.1',1462647848);
< INSERT INTO `watchdog` VALUES (236,0,'filter','Disabled text format: %format.','a:1:{s:7:\"%format\";s:10:\"full_html1\";}',1,'','http://aad8:8888/node/add','http://aad8:8888/admin/content?status=All&type=photod8&title=&langcode=All','127.0.0.1',1462647849);
< INSERT INTO `watchdog` VALUES (237,0,'filter','Disabled text format: %format.','a:1:{s:7:\"%format\";s:10:\"full_html1\";}',1,'','http://aad8:8888/node/add','http://aad8:8888/admin/content?status=All&type=photod8&title=&langcode=All','127.0.0.1',1462647853);
< INSERT INTO `watchdog` VALUES (238,0,'filter','Disabled text format: %format.','a:1:{s:7:\"%format\";s:10:\"full_html1\";}',1,'','http://aad8:8888/node/add','http://aad8:8888/admin/content?status=All&type=photod8&title=&langcode=All','127.0.0.1',1462647854);
< INSERT INTO `watchdog` VALUES (239,0,'filter','Disabled text format: %format.','a:1:{s:7:\"%format\";s:10:\"full_html1\";}',1,'','http://aad8:8888/node/add','http://aad8:8888/admin/content?status=All&type=photod8&title=&langcode=All','127.0.0.1',1462647854);
< INSERT INTO `watchdog` VALUES (240,0,'filter','Disabled text format: %format.','a:1:{s:7:\"%format\";s:10:\"full_html1\";}',1,'','http://aad8:8888/node/add','http://aad8:8888/admin/content?status=All&type=photod8&title=&langcode=All','127.0.0.1',1462647855);
< INSERT INTO `watchdog` VALUES (241,0,'filter','Disabled text format: %format.','a:1:{s:7:\"%format\";s:10:\"full_html1\";}',1,'','http://aad8:8888/node/add','http://aad8:8888/admin/content?status=All&type=photod8&title=&langcode=All','127.0.0.1',1462647855);
< INSERT INTO `watchdog` VALUES (242,0,'filter','Disabled text format: %format.','a:1:{s:7:\"%format\";s:10:\"full_html1\";}',1,'','http://aad8:8888/node/add','http://aad8:8888/admin/content?status=All&type=photod8&title=&langcode=All','127.0.0.1',1462647858);
< INSERT INTO `watchdog` VALUES (243,0,'filter','Disabled text format: %format.','a:1:{s:7:\"%format\";s:10:\"full_html1\";}',1,'','http://aad8:8888/node/add','http://aad8:8888/admin/content?status=All&type=photod8&title=&langcode=All','127.0.0.1',1462647858);
< INSERT INTO `watchdog` VALUES (244,0,'cron','Cron run completed.','a:0:{}',5,'','http://aad8:8888/node/add','http://aad8:8888/admin/content?status=All&type=photod8&title=&langcode=All','127.0.0.1',1462647871);
< INSERT INTO `watchdog` VALUES (245,1,'content','@type: added %title.','a:2:{s:5:\"@type\";s:7:\"photod8\";s:6:\"%title\";s:11:\"Dick Tidrow\";}',5,'<a href=\"/photos/dick-tidrow-cubbies\" hreflang=\"en\">View</a>','http://aad8:8888/node/add/photod8','http://aad8:8888/node/add/photod8','127.0.0.1',1462647913);
````









