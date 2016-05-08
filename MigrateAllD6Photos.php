#!/Applications/MAMP/bin/php/php5.6.10/bin/php

<?php
  $servername = "localhost";
  $username = "d8_user";
  $password = "d8_pass";
  $dbname = "aa_d8";

  include 'Photo.php';
  include 'UpgradeQueries.php.php';

  # connect to the database
  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  # get a list of photos from `node__field_photo`
  $sql = "SELECT * FROM node__field_photo WHERE bundle='photo' and deleted=0 ORDER BY entity_id asc";
  $result = $conn->query($sql);

  # an array to store the results
  $photos = array();

  # note - i 'assume success' here
  while($row = $result->fetch_assoc()) {
      $p = new Photo($row["deleted"], $row["entity_id"], $row["revision_id"], $row["langcode"], 
                     $row["delta"], $row["field_photo_target_id"], 
                     $row["field_photo_alt"], $row["field_photo_title"], 
                     $row["field_photo_width"], $row["field_photo_height"]);
      array_push($photos, $p);
      
      # debugging
      #$p->print_details();
  }

  # loop over all D6 photos to create new PhotoD8 content types
  foreach($photos as $photo)
  {
    #$photo->print_details();
    run_all_queries($conn, $photo);
  }
  
  $conn->close();
  
?>










