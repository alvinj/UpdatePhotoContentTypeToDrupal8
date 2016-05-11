<?php

  function run_query($conn, $query) {
    #echo "running query: $query\n";
    if (!$conn->query($query)) {
        printf("error: %s\n", $conn->error);
    }
  }

  # runs all sql queries necessary to create a new "PhotoD8" content type from the given $photo, which is assumed
  # to contain data for a "PhotoD6" content type.
  function run_all_queries($conn, $photo) {
    #$photo->print_all_details();
    run_query($conn, "UPDATE node SET type='photod8', langcode='$photo->langcode' WHERE vid=$photo->vid");
    run_query($conn, "UPDATE node__body SET bundle='photod8', langcode='$photo->langcode' WHERE revision_id=$photo->vid");
    run_query($conn, "UPDATE node__comment SET bundle = 'photod8' WHERE revision_id = $photo->vid");
    run_query($conn, "UPDATE node__tags SET bundle = 'photod8' WHERE revision_id = $photo->vid");
    run_query($conn, "UPDATE node_field_data set type = 'photod8', langcode='$photo->langcode' WHERE vid = $photo->vid");
    run_query($conn, "UPDATE node_revision__body set bundle = 'photod8', langcode='$photo->langcode' WHERE revision_id = $photo->vid");
    run_query($conn, "UPDATE node_revision__body set body_format = 'full_html' WHERE revision_id = $photo->vid");
    run_query($conn, "UPDATE node_revision__comment set bundle = 'photod8' WHERE revision_id = $photo->vid");
    run_query($conn, "UPDATE node_revision__tags set bundle = 'photod8' WHERE revision_id = $photo->vid");
    run_query($conn, "UPDATE node_field_revision SET langcode='$photo->langcode' WHERE nid=$photo->nid");
    run_query($conn, "UPDATE node_revision SET langcode='$photo->langcode' WHERE nid=$photo->nid");

    # INSERT - node__field_photo_d8
    $sql = "INSERT INTO node__field_photo_d8 (bundle, deleted, entity_id, revision_id, langcode, delta, field_photo_d8_target_id, field_photo_d8_alt, field_photo_d8_title, field_photo_d8_width, field_photo_d8_height) VALUES ('photod8', 0, $photo->nid, $photo->vid, '$photo->langcode', $photo->delta, $photo->fid, '$photo->photoAlt', '$photo->photoTitle', $photo->photoWidth, $photo->photoHeight)";
    run_query($conn, $sql);

    # INSERT - node_revision__field_photo_d8
    $sql = "INSERT INTO node_revision__field_photo_d8 (bundle, deleted, entity_id, revision_id, langcode, delta, field_photo_d8_target_id, field_photo_d8_alt, field_photo_d8_title, field_photo_d8_width, field_photo_d8_height) VALUES ('photod8', 0, $photo->nid, $photo->vid, '$photo->langcode', $photo->delta, $photo->fid, '$photo->photoAlt', '$photo->photoTitle', $photo->photoWidth, $photo->photoHeight)";
    run_query($conn, $sql);
    
  }  

?>










