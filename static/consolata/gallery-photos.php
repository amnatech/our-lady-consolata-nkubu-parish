<?php

require "./includes/connect.php";
require "./includes/functions.php";
require "./middleware/methods.php";

$dir="./uploads/gallery/";

foreach ((new DirectoryIterator($dir)) as $fileinfo) {
   // Ignore .files (.htaccess, .DS_Store, etc)
   if (!$fileinfo->isDot()) {
      // Check if file is a PNG
      if ($fileinfo->getExtension() == 'png') {
         echo "This is a PNG image.";
      }else{
        echo ".";
      }
   }
}