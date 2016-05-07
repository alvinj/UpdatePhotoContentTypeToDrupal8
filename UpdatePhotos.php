#!/Applications/MAMP/bin/php/php5.6.10/bin/php

<?php
  $servername = "localhost";
  $username = "d8_user";
  $password = "d8_pass";
  $dbname = "aa_d8";

  # get this from field_photo_target_id in node__field_photo, latest first
  $photo_id = 2218;
  
  # 2218 has nid=7545. body text and image do not show up at that url.

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

  # INSERT statements
  
  $deleted = $row["deleted"];
  $node_id = $row["entity_id"];
  $rev_id = $row["revision_id"];
  $langcode = $row["langcode"];
  $delta = $row["delta"];
  $photo_alt = $row["field_photo_alt"];
  $photo_title = $row["field_photo_title"];
  $photo_width = $row["field_photo_width"];
  $photo_height = $row["field_photo_height"];

  # INSERT - node__field_photo_d8
  $sql = "INSERT INTO node__field_photo_d8 (bundle, deleted, entity_id, revision_id, langcode, delta, field_photo_d8_target_id, field_photo_d8_alt, field_photo_d8_title, field_photo_d8_width, field_photo_d8_height) VALUES ('photod8', 0, $node_id, $rev_id, '$langcode', $delta, $photo_id, '$photo_alt', '$photo_title', $photo_width, $photo_height)";
  $conn->query($sql);

  # INSERT - node_revision__field_photo_d8
  $sql = "INSERT INTO node_revision__field_photo_d8 (bundle, deleted, entity_id, revision_id, langcode, delta, field_photo_d8_target_id, field_photo_d8_alt, field_photo_d8_title, field_photo_d8_width, field_photo_d8_height) VALUES ('photod8', 0, $node_id, $rev_id, '$langcode', $delta, $photo_id, '$photo_alt', '$photo_title', $photo_width, $photo_height)";
  $conn->query($sql);

  # NOTE - text is showing up at 'node/7548', but no image
  # file_managed - original file data looks ok (uuid starts with `1827295c`, search db for that. result: it's only in that table.)
  #              - 1827295c was actually the twitter file uuid, so not finding it means that drupal PhotoD8 didn't use it elsewhere
  # file_usage - original file data looks ok
  
  # NOTE - `drush cr` really converts the node view when i look at it at 'node/7548'
  #      - the node Edit link shows, "Edit PhotoD8 Chipotle (CMG) stock buybacks", but still no photo
  #      - no <img> tag in the View html
  #      - node shows up in 'admin/content'
  
  # Somewhere along the line, running `drush cr` causes the node text to disappear. still thinks its a PhotoD8 though.
  
  # Is there something else that ties together the photo_id? search for 2187, 2219

  $conn->close();
  
?>










