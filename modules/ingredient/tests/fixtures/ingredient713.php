<?php
// @codingStandardsIgnoreFile
/**
 * @file
 * Filled installation of Recipe 7.x-1.3, for test purposes.
 *
 * This file was originally generated by the dump-database-d7.sh tool, from an
 * installation of Drupal 7:
 */

use Drupal\Core\Database\Database;

$connection = Database::getConnection();

$connection->schema()->createTable('recipe_ingredient', array(
  'fields' => array(
    'id' => array(
      'type' => 'serial',
      'unsigned' => TRUE,
      'not null' => TRUE,
    ),
    'name' => array(
      'type' => 'varchar',
      'length' => 255,
    ),
    'link' => array(
      'type' => 'int',
      'unsigned' => TRUE,
      'not null' => TRUE,
    ),
  ),
  'primary key' => array(
    'id',
  ),
  'module' => 'recipe',
  'name' => 'recipe_ingredient',
));
$connection->insert('recipe_ingredient')->fields(array(
  'id',
  'name',
  'link',
))
  ->values(array(
    'id' => '1',
    'name' => 'water',
    'link' => '0',
  ))
  ->values(array(
    'id' => '2',
    'name' => 'salt',
    'link' => '0',
  ))
  ->execute();

$connection->schema()->createTable('recipe_node_ingredient', array(
  'fields' => array(
    'id' => array(
      'type' => 'serial',
      'unsigned' => TRUE,
      'not null' => TRUE,
    ),
    'nid' => array(
      'type' => 'int',
      'unsigned' => TRUE,
      'not null' => TRUE,
    ),
    'unit_key' => array(
      'type' => 'varchar',
      'length' => 255,
      'not null' => TRUE,
      'default' => '',
    ),
    'quantity' => array(
      'type' => 'float',
      'not null' => FALSE,
    ),
    'ingredient_id' => array(
      'type' => 'int',
      'unsigned' => TRUE,
      'not null' => TRUE,
    ),
    'weight' => array(
      'type' => 'int',
      'not null' => TRUE,
      'default' => 0,
    ),
    'note' => array(
      'type' => 'varchar',
      'length' => 255,
      'not null' => TRUE,
      'default' => '',
    ),
  ),
  'primary key' => array(
    'id',
  ),
  'module' => 'recipe',
  'name' => 'recipe_node_ingredient',
));
$connection->insert('recipe_node_ingredient')->fields(array(
  'id',
  'nid',
  'unit_key',
  'quantity',
  'ingredient_id',
  'weight',
  'note',
))
  ->values(array(
    'id' => '1',
    'nid' => '1',
    'unit_key' => 'cup',
    'quantity' => '2',
    'ingredient_id' => '1',
    'weight' => '0',
    'note' => 'cold',
  ))
  ->values(array(
    'id' => '2',
    'nid' => '1',
    'unit_key' => 'tablespoon',
    'quantity' => '1',
    'ingredient_id' => '2',
    'weight' => '1',
    'note' => '',
  ))
  ->execute();

$connection->schema()->createTable('system', array(
  'fields' => array(
    'filename' => array(
      'type' => 'varchar',
      'not null' => TRUE,
      'length' => '255',
      'default' => '',
    ),
    'name' => array(
      'type' => 'varchar',
      'not null' => TRUE,
      'length' => '255',
      'default' => '',
    ),
    'type' => array(
      'type' => 'varchar',
      'not null' => TRUE,
      'length' => '12',
      'default' => '',
    ),
    'owner' => array(
      'type' => 'varchar',
      'not null' => TRUE,
      'length' => '255',
      'default' => '',
    ),
    'status' => array(
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'normal',
      'default' => '0',
    ),
    'bootstrap' => array(
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'normal',
      'default' => '0',
    ),
    'schema_version' => array(
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'normal',
      'default' => '-1',
    ),
    'weight' => array(
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'normal',
      'default' => '0',
    ),
    'info' => array(
      'type' => 'blob',
      'not null' => FALSE,
      'size' => 'normal',
    ),
  ),
  'primary key' => array(
    'filename',
  ),
  'mysql_character_set' => 'utf8',
));

$connection->insert('system')->fields(array(
  'filename',
  'name',
  'type',
  'owner',
  'status',
  'bootstrap',
  'schema_version',
  'weight',
  'info',
))
  ->values(array(
    'filename' => 'sites/default/modules/recipe/recipe.module',
    'name' => 'recipe',
    'type' => 'module',
    'owner' => '',
    'status' => '1',
    'bootstrap' => '0',
    'schema_version' => '7004',
    'weight' => '0',
    'info' => 'a:11:{s:4:"name";s:6:"Recipe";s:11:"description";s:28:"Collect and display recipes.";s:7:"package";s:6:"Recipe";s:4:"core";s:3:"7.x";s:7:"version";s:7:"7.x-1.3";s:7:"project";s:6:"recipe";s:9:"datestamp";s:10:"1335415286";s:12:"dependencies";a:0:{}s:3:"php";s:5:"5.2.4";s:5:"files";a:0:{}s:9:"bootstrap";i:0;}',
  ))
  ->execute();

$connection->schema()->createTable('variable', array(
  'fields' => array(
    'name' => array(
      'type' => 'varchar',
      'not null' => TRUE,
      'length' => '128',
      'default' => '',
    ),
    'value' => array(
      'type' => 'blob',
      'not null' => TRUE,
      'size' => 'normal',
    ),
  ),
  'primary key' => array(
    'name',
  ),
  'mysql_character_set' => 'utf8',
));

$connection->insert('variable')->fields(array(
  'name',
  'value',
))
  ->values(array(
    'name' => 'recipe_default_unit',
    'value' => 's:0:"";',
  ))
  ->values(array(
    'name' => 'recipe_fraction_display',
    'value' => 's:16:"{%d }%d&frasl;%d";',
  ))
  ->values(array(
    'name' => 'recipe_ingredient_name_normalize',
    'value' => 's:1:"0";',
  ))
  ->values(array(
    'name' => 'recipe_preferred_system_of_measure',
    'value' => 's:1:"0";',
  ))
  ->values(array(
    'name' => 'recipe_preferred_system_of_measure_limit',
    'value' => 'i:0;',
  ))
  ->values(array(
    'name' => 'recipe_unit_display',
    'value' => 's:1:"0";',
  ))
  ->execute();
