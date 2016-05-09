# Migrate Drupal Photos (D6 to D8)


## Update

The main script in this project — _MigrateAllD6Photos.php_ — now seems to be working. The code is ugly and doesn't check for SQL errors, but for my purposes it seems to be working okay.


## The problem this project solves

This project contains a PHP/MySQL project that attempts to upgrade/migrate a Drupal 6 "Photo" content type to a Drupal 8 "PhotoD8" content type. This is necessary because as it is stated under the section "Files and Images" on the drupal.org node titled, [Known issues when upgrading from Drupal 6 or 7 to Drupal 8](https://www.drupal.org/node/2167633):

>"Images attached to Drupal 6 Image nodes, and files attached with File fields do not get migrated."

This is a HUGE problem for me, as I have over 1,700 photos posted on alvinalexander.com alone. So I’ve been working to create a solution for this problem.


## My approach (solution)

### First, the migration

My attempt at a solution goes like this. First, start with a D6 to D8 migration:

* A long time ago on my D6 website I created a simple _Photo_ content type (per the "Using Drupal" book, if I remember right). It has one field, with a Label="Photo", Name="field_photo", and Type="File".
* Perform a "Standard" Drupal 6 (D6) to Drupal 8 (D8) migration.
* Confirm that `node__field_photo` and `node_revision__field_photo` database tables are in the D8 database, and the data is populated. (They are there with Drupal 8.1.0.)
* Confirm that the original image files are on disk. The file locations may be different, but they reflect what is shown in the D8 database. My files are now under this directory:

````
sites/default/files/photos/...
````

>Note that the old "imagecache" files are gone, and when I say "gone," I mean "long gone." My solution only works with the large, original files. I display those files with the PhotoD8 content type I'm about to create.

### Fix the `full_html` format

You also need to run this query:

    UPDATE `node__body` SET `body_format`='full_html' WHERE `body_format`='full_html1';

and then clear the caches:

    drush cr


### Make a backup

Once you finish the initial D6->D8 migration, this is a good time to back up your D8 database and website files. For my website it takes close to 30 minutes to run the migration, so when you need to do that again and again, you'll find that it's easier to have these backups.


### Create a new "PhotoD8" content type

Now you need to create a new "PhotoD8" content type. You need this so you can migrated the data from the old "Photo" database tables to the new "PhotoD8" tables using SQL queries.

To do this, go to the 'admin/structure/types/add' URI, and use (exact) these settings:

* Name = "PhotoD8"
* Description can be whatever you want it to be
* Leave everything at its default values, click "Save and Manage Fields"

On the "Manage fields" screen that follows, click "Add field". On the "Add field" form that follows, use these settings:

* "Add a new field": Select "Image" (under Reference)
* Label = "Photo D8"
* Click "Save and continue"

On the next form:

* "Upload destination" = "Public files"
* No "Default Image"
* "Allow number of values" = (Limited, 1)
* Click "Save field settings"

On the next form:

* Label = "Photo D8"
* Make the image field Required
* Enable Alt field.
* Don't make the Alt field required (un-check it)
* Go with other defaults
* Click "Save settings"

You should now be back to "Manage fields." On this screen you should see these two rows in the table:

* Label = "Body", Machine Name = "body", Field Type = "Text (formatted, long, with summary)"
* Label = "Photo D8", Machine Name = "field_photo_d8", Field Type = "Image"

Now you're ready for the next step.

### Make another database backup

This is another great time to backup the database.



## A PHP Script to Update the Photos

Before running my script, you should verify that the `langcode` field in the `node__field_photo` D8 database table is properly set to 'en' (or whatever your locale is). If it is set to `und`, you _MUST RUN THIS NEXT QUERY_, or you'll just make the problem worse:

    update node__field_photo set langcode='en'

The [MigrateAllD6Photos.php](MigrateAllD6Photos.php) file is now working, and it will convert all the old D6 "Photo" content types to the new D8 "Photo D8" content type.

After you run the script, run this command to flush all caches:

    drush cr

After this, your old Photo URLs should work, and should have the "Photo D8" content type.


## History: MySQL Database Diffs

The following text isn't important to anyone but me, but I want to keep it here. This is the approach I used to finally find the last queries that were necessary to get this code working.

What I did was:

* Make a backup of the MySQL D8 database
* Add a new photo using the D8 "PhotoD8" content type
* Make another backup of the D8 database
* Do a `diff` on those two backup files, weeding out the junk to find the problem(s)

The following text shows the result of that effort. (If you can understand my cryptic notes, good luck. ;)

````
Database Diffs (The manually-added "Dick Tidrow" PhotoD8 image vs the old/original D6 "Cat" photo)
--------------------------------------------------------------------------------------------------

< INSERT INTO `file_managed` VALUES (2221,'6e6fc9c2-b83b-4f10-b019-39e0ac7c2586','en',1,'dick-tidrow-cubbies.jpg','public://2016-05/dick-tidrow-cubbies.jpg','image/jpeg',30718,1,1462647881,1462647912);
    - newest FIDs are 2221, 2222, 2223
    - Dick Tidrow is 2221, nid=7550
    - Cat Cartoon is 2218 (manual effort, old image)
        - changed uid to 1 (NOTNEEDED)
        - nid=7545, vid=7735
            - title shows up at node/7545, nothing else shows up
        - filesize is correct
    - Chipotle FID is 2219
        - nid=7548, vid=7738
    - the uid for chipotle image is 3, not 1
        - changed to 1 (NOTNEEDED)
    - no content at all at Chipotle url (node/7548)

< INSERT INTO `file_usage` VALUES (2221,'file','node','7550',1);
    - Dick Tidrow at 2221/7550 looks good (added thru ui)
    - Cat at 2218/7545 looks like Dick Tidrow

< INSERT INTO `history` VALUES (1,7550,1462647914);
    - Cat and Dick Tidrow look the same

< INSERT INTO `key_value` VALUES ('state','system.cron_last','i:1462647842;');
> INSERT INTO `key_value` VALUES ('state','system.cron_last','i:1462580283;');
    - hopefully these key/value pairs aren't important

< INSERT INTO `node` VALUES (7550,7740,'photod8','c37a1d9e-a610-4052-b9e0-515764479e13','en');
    - 7550/7740 and 7545/7735 are both here
    - uuid field here
    - all fields look ok

< INSERT INTO `node__body` VALUES ('photod8',0,7550,7740,'en',0,'Dick Tidrow of the Chicago Cubbies.','','anonymous_format');
    - 7550 Dick Tidrow has no body_summary, body_format=anonymous_format
    - 7545 Cat has a body_summary, and body_format=full_html
        - deleted body_summary, changed body_format=anonymous_format  (TODO:MAYBE)
            - want to see if full_html is a problem, so get rid of it

< INSERT INTO `node__field_photo_d8` VALUES ('photod8',0,7550,7740,'en',0,2221,'Dick Tidrow','',576,250);
    - Cat looks like Dick Tidrow
    - confirmed Cat width and height

< INSERT INTO `node_field_data` VALUES (7550,7740,'photod8','en','Dick Tidrow',1,1,1462647847,1462647912,1,0,1,1);
    - title is repeated here
        - i use “” and ‘’ in title (was not a problem)
    - uid was 3, changing it to 1 to match Dick Tidrow (NOTNEEDED)
    - all else is the same

< INSERT INTO `node_field_revision` VALUES (7550,7740,'en','Dick Tidrow',1,1,1462647847,1462647912,1,0,1,1);
    - title is repeated here
        - i use “” and ‘’ in title
    - Cat langcode was 'und', changing to en (TODO:MAYBE)
    - uid was 3, changing it to 1 to match Dick Tidrow (NOTNEEDED)
    - all else is the same
    - --------------------
    - ***** WORKING *****
    - --------------------
        - run `drush cr` and the Cat is now showing up

< INSERT INTO `node_revision` VALUES (7550,7740,'en',1462647881,1,NULL);
    - Cat langcode is 'und', Dick Tidrow is 'en' (TODO:MAYBE)
        - change Cat to match
    - Cat revision_uid=3, Dick Tidrow is 1 (NOTNEEDED)
        - change Cat to match


< INSERT INTO `node_revision__body` VALUES ('photod8',0,7550,7740,'en',0,'Dick Tidrow of the Chicago Cubbies.','','anonymous_format');
    - Cat body_format is full_html, change it to anonymous_format
    - Cat has body_summary, delete it (change)


< INSERT INTO `node_revision__field_photo_d8` VALUES ('photod8',0,7550,7740,'en',0,2221,'Dick Tidrow','',576,250);
    - all is well here
````

And from my followup notes:

````
WHAT PROBLEMS/QUERIES I UNCOVERED
---------------------------------
    - node__body
        - changed body_format=anonymous_format
            - changed back to full_html, ran drush, image still shows
        - deleted body_summary
            - put a summary back in here, it still works
    - node_field_revision (PROBLEM)
        - Cat langcode was 'und', changing to en
            - CONFIRMED: this was the problem
        - ***** WORKING *****
    - node_revision
        - Cat langcode is 'und', Dick Tidrow is 'en'
````

FWIW, the Dick Tidrow images are here:

````
sites/default/files/2016-05/dick-tidrow-cubbies.jpg
sites/default/files/styles/thumbnail/public/2016-05/dick-tidrow-cubbies.jpg
````


## About me

My name is Alvin Alexander, and you can find me at [alvinalexander.com](http://alvinalexander.com).



