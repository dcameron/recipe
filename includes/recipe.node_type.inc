<?php
/**
 * @file
 * Contains functions to install and uninstall the recipe node type.
 */

/**
 * Installs the recipe node type.
 */
function _recipe_install_node_type() {
  $field_bases = array();
  $field_instances = array();

  // field_base: 'recipe_description'
  $field_bases['recipe_description'] = array(
    'active' => 1,
    'cardinality' => 1,
    'deleted' => 0,
    'entity_types' => array(),
    'field_name' => 'recipe_description',
    'foreign keys' => array(
      'format' => array(
        'columns' => array(
          'format' => 'format',
        ),
        'table' => 'filter_format',
      ),
    ),
    'indexes' => array(
      'format' => array(
        0 => 'format',
      ),
    ),
    'locked' => 0,
    'module' => 'text',
    'settings' => array(),
    'translatable' => 0,
    'type' => 'text_with_summary',
  );

  // field_instance: 'node-recipe-recipe_description'
  $field_instances['node-recipe-recipe_description'] = array(
    'bundle' => 'recipe',
    'default_value' => NULL,
    'deleted' => 0,
    'description' => 'A short description or "teaser" for the recipe.',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'text',
        'settings' => array(),
        'type' => 'text_default',
        'weight' => -4,
      ),
      'teaser' => array(
        'label' => 'hidden',
        'module' => 'text',
        'settings' => array(
          'trim_length' => 600,
        ),
        'type' => 'text_summary_or_trimmed',
        'weight' => 1,
      ),
    ),
    'entity_type' => 'node',
    'field_name' => 'recipe_description',
    'label' => 'Description',
    'required' => 0,
    'settings' => array(
      'display_summary' => 0,
      'text_processing' => 1,
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'text',
      'settings' => array(
        'rows' => 1,
        'summary_rows' => 5,
      ),
      'type' => 'text_textarea_with_summary',
      'weight' => -5,
    ),
  );

  // Create the fields.
  foreach ($field_bases as $field) {
    field_create_field($field);
  }
  // Create the field instances.
  foreach ($field_instances as $instance) {
    field_create_instance($instance);
  }
}

/**
 * Uninstalls the recipe node type.
 */
function _recipe_uninstall_node_type() {
  $fields = array(
    'recipe_description',
  );
  foreach ($fields as $field) {
    field_delete_field($field);
    watchdog('recipe', 'Deleted the :field_name field from all content type instances.', array(':field_name' => $field));
  }
}
