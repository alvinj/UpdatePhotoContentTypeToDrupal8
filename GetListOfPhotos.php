#!/Applications/MAMP/bin/php/php5.6.10/bin/php

<?php
  $servername = "localhost";
  $username = "d8_user";
  $password = "d8_pass";
  $dbname = "aa_d8";


  class Photo
  {
    var $deleted;
    var $nid;
    var $vid;
    var $langcode;
    var $delta;
    var $fid;
    var $photoAlt;
    var $photoTitle;
    var $photoWidth;
    var $photoHeight;

    function Photo($deleted, $nid, $vid, $langcode, $delta, $fid, $photoAlt, $photoTitle, $photoWidth, $photoHeight)
    {
        $this->deleted = $deleted;
        $this->nid = $nid;
        $this->vid = $vid;
        $this->langcode = $langcode;
        $this->delta = $delta;
        $this->fid = $fid;
        $this->photoAlt = $photoAlt;
        $this->photoTitle = $photoTitle;
        $this->photoWidth = $photoWidth;
        $this->photoHeight = $photoHeight;
    }
    
    # for debugging
    function print_details()
    {
      printf("nid: %s, vid: %s, lang: %s, fip: %s\n", $this->nid, $this->vid, $this->langcode, $this->fid);
    }

  }

  # connect to the database
  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  # get a list of photos from `node__field_photo`
  
  # (1) get $node_id and $rev_id from node__field_photo
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

  # `$photos` is now the array of photo objects i need to loop over
  foreach($photos as $photo)
  {
    $photo->print_details();
  }
  
  $conn->close();
  
?>










