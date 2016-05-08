<?php

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

?>

