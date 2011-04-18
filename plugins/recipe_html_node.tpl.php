<?php
/**
 * @file
 * Default theme implementation for html version of recipe nodes.
 */
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php print $title; ?></title>
    <link type="text/css" rel="stylesheet" href="<?php print url(drupal_get_path('module', 'recipe')) ."/recipe.css";?>" />
  </head>
  <body>
    <h2><?php print $title; ?></h2>
    <hr/>
    <?php print $contents; ?>
  </body>
</html>
