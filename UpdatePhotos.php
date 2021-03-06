#!/Applications/MAMP/bin/php/php5.6.10/bin/php

<?php
  $servername = "localhost";
  $username = "d8_user";
  $password = "d8_pass";
  $dbname = "aa_d8";

  # start the process by looking in `node__field_photo` to get a `field_photo_target_id`
  # value. in my case, start with the latest first (newest entity_id first). use that 
  # `field_photo_target_id` as $photo_id here. Ex: 'node/7545' is 'photo_id=2218', and
  # 'node/7533' is 'photo_id=2210' (ClojureScript image):
  $photo_id = 2210;
  
  
  # TODO: You need to run `drush cr` after running this script.
  #       I just confirmed that this works with different images.
  

  #-----------------------------------------------
  # custom data above, don't change anything below
  #-----------------------------------------------

  # connect to the database
  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  # (1) get $node_id and $rev_id from node__field_photo
  $sql = "SELECT * FROM node__field_photo WHERE field_photo_target_id=$photo_id";
  $result = $conn->query($sql);

  # note - i 'assume success' here
  if($row = $result->fetch_assoc()) {
      $deleted = $row["deleted"];
      $node_id = $row["entity_id"];
      $rev_id = $row["revision_id"];
      $langcode = $row["langcode"];
      $delta = $row["delta"];
      $photo_alt = $row["field_photo_alt"];
      $photo_title = $row["field_photo_title"];
      $photo_width = $row["field_photo_width"];
      $photo_height = $row["field_photo_height"];
  }
  #echo "nid = $node_id, rev_id = $rev_id, photo_id = $photo_id";
  echo "deleted=$deleted, entity_id=$node_id, revision_id=$rev_id, langcode=$langcode, delta=$delta, field_photo_target_id=$photo_id, field_photo_alt=$photo_alt, field_photo_title=$photo_title, field_photo_width=$photo_width, field_photo_height=$photo_height\n";

  # TODO - test to make sure `$deleted == 0`

  # (2) update `node`
  $sql = "UPDATE `node` SET type='photod8', langcode='$langcode' WHERE vid=$rev_id;";
  if ($conn->query($sql) === TRUE) {
      echo "NODE updated successfully\n";
  } else {
      echo "NODE *not* updated. Error: " . $conn->error . "\n";
  }

  # (3) update `node__body`
  $sql = "UPDATE `node__body` SET bundle='photod8', langcode='$langcode' WHERE revision_id=$rev_id;";
  if ($conn->query($sql) === TRUE) {
      echo "NODE__BODY updated successfully\n";
  } else {
      echo "NODE__BODY *not* updated. Error: " . $conn->error . "\n";
  }

  # (4) later queries (assuming success)
  $sql = "UPDATE node__comment SET bundle = 'photod8' WHERE revision_id = $rev_id";
  $conn->query($sql);
  $sql = "UPDATE node__tags1 SET bundle = 'photod8' WHERE revision_id = $rev_id";
  $conn->query($sql);
  $sql = "UPDATE node_field_data set type = 'photod8', langcode='$langcode' WHERE vid = $rev_id";
  $conn->query($sql);
  $sql = "UPDATE node_revision__body set bundle = 'photod8', langcode='$langcode' WHERE revision_id = $rev_id";
  $conn->query($sql);
  $sql = "UPDATE node_revision__body set body_format = 'full_html' WHERE revision_id = $rev_id";
  $conn->query($sql);
  $sql = "UPDATE node_revision__comment set bundle = 'photod8' WHERE revision_id = $rev_id";
  $conn->query($sql);
  $sql = "UPDATE node_revision__tags1 set bundle = 'photod8' WHERE revision_id = $rev_id";
  $conn->query($sql);
  $sql = "UPDATE node_field_revision SET langcode='en' WHERE nid=$node_id";
  $conn->query($sql);
  $sql = "UPDATE node_revision SET langcode='en' WHERE nid=$node_id";
  $conn->query($sql);

  # INSERT statements
  
  #TODO: i don't think i meant to leave these here
#  $deleted = $row["deleted"];
#  $node_id = $row["entity_id"];
#  $rev_id = $row["revision_id"];
#  $langcode = $row["langcode"];
#  $delta = $row["delta"];
#  $photo_alt = $row["field_photo_alt"];
#  $photo_title = $row["field_photo_title"];
#  $photo_width = $row["field_photo_width"];
#  $photo_height = $row["field_photo_height"];

  # INSERT - node__field_photo_d8
  $sql = "INSERT INTO node__field_photo_d8 (bundle, deleted, entity_id, revision_id, langcode, delta, field_photo_d8_target_id, field_photo_d8_alt, field_photo_d8_title, field_photo_d8_width, field_photo_d8_height) VALUES ('photod8', 0, $node_id, $rev_id, '$langcode', $delta, $photo_id, '$photo_alt', '$photo_title', $photo_width, $photo_height)";
  $conn->query($sql);

  # INSERT - node_revision__field_photo_d8
  $sql = "INSERT INTO node_revision__field_photo_d8 (bundle, deleted, entity_id, revision_id, langcode, delta, field_photo_d8_target_id, field_photo_d8_alt, field_photo_d8_title, field_photo_d8_width, field_photo_d8_height) VALUES ('photod8', 0, $node_id, $rev_id, '$langcode', $delta, $photo_id, '$photo_alt', '$photo_title', $photo_width, $photo_height)";
  $conn->query($sql);

  $conn->close();
  
?>










