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

  // field_base: 'recipe_instructions'
  $field_bases['recipe_instructions'] = array(
    'active' => 1,
    'cardinality' => 1,
    'deleted' => 0,
    'entity_types' => array(),
    'field_name' => 'recipe_instructions',
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
    'type' => 'text_long',
  );

  // field_instance: 'node-recipe-recipe_instructions'
  $field_instances['node-recipe-recipe_instructions'] = array(
    'bundle' => 'recipe',
    'default_value' => NULL,
    'deleted' => 0,
    'description' => 'Step by step instructions on how to prepare and cook the recipe.',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'text',
        'settings' => array(),
        'type' => 'text_default',
        'weight' => -1,
      ),
      'teaser' => array(
        'label' => 'above',
        'settings' => array(),
        'type' => 'hidden',
        'weight' => 3,
      ),
    ),
    'entity_type' => 'node',
    'field_name' => 'recipe_instructions',
    'label' => 'Instructions',
    'required' => 0,
    'settings' => array(
      'text_processing' => 1,
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'text',
      'settings' => array(
        'rows' => 10,
      ),
      'type' => 'text_textarea',
      'weight' => -1,
    ),
  );

  // field_base: 'recipe_notes'
  $field_bases['recipe_notes'] = array(
    'active' => 1,
    'cardinality' => 1,
    'deleted' => 0,
    'entity_types' => array(),
    'field_name' => 'recipe_notes',
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
    'type' => 'text_long',
  );

  // field_instance: 'node-recipe-recipe_notes'
  $field_instances['node-recipe-recipe_notes'] = array(
    'bundle' => 'recipe',
    'default_value' => NULL,
    'deleted' => 0,
    'description' => 'Optional. Describe a great dining experience relating to this recipe, or note which wine or other dishes complement this recipe.',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'text',
        'settings' => array(),
        'type' => 'text_default',
        'weight' => -1,
      ),
      'teaser' => array(
        'label' => 'above',
        'settings' => array(),
        'type' => 'hidden',
        'weight' => 2,
      ),
    ),
    'entity_type' => 'node',
    'field_name' => 'recipe_notes',
    'label' => 'Notes',
    'required' => 0,
    'settings' => array(
      'text_processing' => 1,
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'text',
      'settings' => array(
        'rows' => 5,
      ),
      'type' => 'text_textarea',
      'weight' => -1,
    ),
  );

  // field_base: 'recipe_source'
  $field_bases['recipe_source'] = array(
    'active' => 1,
    'cardinality' => 1,
    'deleted' => 0,
    'entity_types' => array(),
    'field_name' => 'recipe_source',
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
    'type' => 'text_long',
  );

  // field_instance: 'node-recipe-recipe_source'
  $field_instances['node-recipe-recipe_source'] = array(
    'bundle' => 'recipe',
    'default_value' => NULL,
    'deleted' => 0,
    'description' => 'Does anyone else deserve credit for this recipe?',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'text',
        'settings' => array(),
        'type' => 'text_default',
        'weight' => 0,
      ),
      'teaser' => array(
        'label' => 'above',
        'settings' => array(),
        'type' => 'hidden',
        'weight' => 0,
      ),
    ),
    'entity_type' => 'node',
    'field_name' => 'recipe_source',
    'label' => 'Source',
    'required' => 0,
    'settings' => array(
      'text_processing' => 1,
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'text',
      'settings' => array(
        'rows' => 3,
      ),
      'type' => 'text_textarea',
      'weight' => -1,
    ),
  );

  // field_base: 'recipe_preparation_time'
  $field_bases['recipe_prep_time'] = array(
    'active' => 1,
    'cardinality' => 1,
    'deleted' => 0,
    'entity_types' => array(),
    'field_name' => 'recipe_prep_time',
    'foreign keys' => array(),
    'indexes' => array(),
    'locked' => 0,
    'module' => 'number',
    'settings' => array(),
    'translatable' => 0,
    'type' => 'number_integer',
  );

  // field_instance: 'node-recipe-recipe_prep_time'
  $field_instances['node-recipe-recipe_prep_time'] = array(
    'bundle' => 'recipe',
    'default_value' => NULL,
    'deleted' => 0,
    'description' => 'How long does this recipe take to prepare, in <strong>minutes</strong>?',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'recipe',
        'type' => 'recipe_duration',
        'weight' => -3,
      ),
      'teaser' => array(
        'label' => 'above',
        'settings' => array(),
        'type' => 'hidden',
        'weight' => 0,
      ),
    ),
    'entity_type' => 'node',
    'field_name' => 'recipe_prep_time',
    'label' => 'Preparation time',
    'required' => 0,
    'settings' => array(
      'max' => '',
      'min' => 0,
      'prefix' => '',
      'suffix' => '',
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 0,
      'module' => 'number',
      'settings' => array(),
      'type' => 'number',
      'weight' => 0,
    ),
  );

  // field_base: 'recipe_cook_time'
  $field_bases['recipe_cook_time'] = array(
    'active' => 1,
    'cardinality' => 1,
    'deleted' => 0,
    'entity_types' => array(),
    'field_name' => 'recipe_cook_time',
    'foreign keys' => array(),
    'indexes' => array(),
    'locked' => 0,
    'module' => 'number',
    'settings' => array(),
    'translatable' => 0,
    'type' => 'number_integer',
  );

  // field_instance: 'node-recipe-recipe_cook_time'
  $field_instances['node-recipe-recipe_cook_time'] = array(
    'bundle' => 'recipe',
    'default_value' => NULL,
    'deleted' => 0,
    'description' => 'How long does this recipe take to cook, in <strong>minutes</strong>?',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'recipe',
        'type' => 'recipe_duration',
        'weight' => -2,
      ),
      'teaser' => array(
        'label' => 'above',
        'settings' => array(),
        'type' => 'hidden',
        'weight' => 0,
      ),
    ),
    'entity_type' => 'node',
    'field_name' => 'recipe_cook_time',
    'label' => 'Cooking time',
    'required' => 0,
    'settings' => array(
      'max' => '',
      'min' => 0,
      'prefix' => '',
      'suffix' => '',
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 0,
      'module' => 'number',
      'settings' => array(),
      'type' => 'number',
      'weight' => 0,
    ),
  );

  // field_base: 'recipe_ingredient'
  $field_bases['recipe_ingredient'] = array(
    'active' => 1,
    'cardinality' => -1,
    'deleted' => 0,
    'entity_types' => array(),
    'field_name' => 'recipe_ingredient',
    'foreign keys' => array(
      'iid' => array(
        'columns' => array(
          'iid' => 'id',
        ),
        'table' => 'recipe_ingredient',
      ),
    ),
    'indexes' => array(
      'iid' => array(
        0 => 'iid',
      ),
    ),
    'locked' => 0,
    'module' => 'recipe',
    'settings' => array(
      'ingredient_name_normalize' => variable_get('recipe_ingredient_name_normalize', 0),
    ),
    'translatable' => 0,
    'type' => 'ingredient_reference',
  );

  // field_instance: 'node-page-recipe_ingredient'
  $field_instances['node-page-recipe_ingredient'] = array(
    'bundle' => 'recipe',
    'default_value' => NULL,
    'deleted' => 0,
    'description' => '',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'module' => 'recipe',
        'settings' => array(
          'fraction_format' => variable_get('recipe_fraction_display', '{%d }%d&frasl;%d'),
          'unit_abbreviation' => variable_get('recipe_unit_display', 0),
        ),
        'type' => 'recipe_ingredient_default',
        'weight' => -2,
      ),
      'teaser' => array(
        'label' => 'above',
        'settings' => array(),
        'type' => 'hidden',
        'weight' => 0,
      ),
    ),
    'entity_type' => 'node',
    'field_name' => 'recipe_ingredient',
    'label' => 'Ingredients',
    'required' => 0,
    'settings' => array(
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 0,
      'module' => 'recipe',
      'settings' => array(
        'default_unit' => variable_get('recipe_default_unit', ''),
      ),
      'type' => 'recipe_ingredient_autocomplete',
      'weight' => -2,
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
    'recipe_instructions',
    'recipe_notes',
    'recipe_source',
    'recipe_prep_time',
    'recipe_cook_time',
    'recipe_ingredient',
  );
  foreach ($fields as $field) {
    field_delete_field($field);
    watchdog('recipe', 'Deleted the :field_name field from all content type instances.', array(':field_name' => $field));
  }
}
