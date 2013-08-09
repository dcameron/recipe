<?php

/**
 * @file
 * Contains admin page callbacks, form validation, and form submission handlers.
 */

/**
 * Page callback: Constructs a form for configuring the Recipe module.
 */
function recipe_admin_settings() {
  // System of measurement section
  $form['system_of_measurement'] = array(
    '#type' => 'fieldset',
    '#title' => t('System of measurement'),
  );
  $form['system_of_measurement']['recipe_preferred_system_of_measure'] = array(
    '#type' => 'radios',
    '#title' => t('Preferred system of measure'),
    '#default_value' => variable_get('recipe_preferred_system_of_measure', 0),
    '#options' => array(t('U.S. customary units'), t('SI/Metric')),
    '#description' => t('Which system of measure should be preferred where it is ambiguous.'),
    '#required' => TRUE,
  );
  $form['system_of_measurement']['recipe_preferred_system_of_measure_limit'] = array(
    '#type' => 'checkbox',
    '#title' => t('Limit UI to the preferred system of measure'),
    '#default_value' => variable_get('recipe_preferred_system_of_measure_limit', 0),
    '#return_value' => 1,
    '#description' => t('Limit unit selectbox to only preferred system of measure.  Does not affect import routines.'),
  );

  // Summary Section
  $form['recipe_summary'] = array(
    '#type' => 'fieldset',
    '#title' => t('Recipe summary'),
    '#description' => t('The recipe summary contains the yield, source, and prep time values.')
  );
  $form['recipe_summary']['recipe_summary_location'] = array(
    '#type' => 'radios',
    '#title' => t('Recipe summary location'),
    '#return_value' => 1,
    '#default_value' => variable_get('recipe_summary_location', 0),
    '#options' => array(t('Node content'), t('Block'), t('Hidden')),
    '#description' => t('Where to show the recipe summary information.'),
    '#required' => TRUE
  );
  $form['recipe_summary']['recipe_summary_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Recipe summary title'),
    '#default_value' => variable_get('recipe_summary_title', t('Summary')),
    '#size' => 35,
    '#maxlength' => 255,
    '#description' => t('The title shown above the recipe summary.'),
  );


  $form['recipe_recent_box'] = array(
    '#type' => 'fieldset',
    '#title' => t('Recent recipe box'),
  );
  $form['recipe_recent_box']['recipe_recent_box_enable'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show recent recipes box'),
    '#return_value' => 1,
    '#default_value' => variable_get('recipe_recent_box_enable', 1),
    '#description' => t('Show the recent recipes box on the recipes menu page.'),
    '#required' => FALSE,
  );
  $form['recipe_recent_box']['recipe_recent_box_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Box title'),
    '#default_value' => variable_get('recipe_recent_box_title', t('Latest recipes')),
    '#size' => 35,
    '#maxlength' => 255,
    '#description' => t('Title of the recent recipes box.'),
  );
  $form['recipe_recent_box']['recipe_recent_display'] = array(
    '#type' => 'select',
    '#title' => t('Recipes to display'),
    '#default_value' => variable_get('recipe_recent_display', 5),
    '#options' => drupal_map_assoc(array(0, 5, 10, 15)),
    '#description' => t('Sets the number of recent recipes that will be displayed in the Recent Recipes box. (0 = not displayed).'),
  );

  return system_settings_form($form);
}

/**
 * Page callback: Displays recipe nodes in various formats.
 *
 * @see recipe_menu()
 */
function recipe_export_multi($type = NULL) {

  drupal_set_title(t('Recipe bulk export'));

  // load supported formats
  $formats = module_invoke_all('recipeio', 'export_multi');
  $o = t('Supported bulk output formats:');
  if ( $type === NULL ) {
    foreach ( $formats as $key => $format ) {
      $format_count = 0;
      if ($format) {
        $o .= '<br/>' . l($format['format_name'], "admin/structure/recipe/export_multi/$key");
        $format_count++;
      }
    }
    if ( $format_count == 0 ) {
      $o .= '<br/><p>' . t('You have no export formats available with the bulk export feature.') . '</p>';
    }
    return $o;
  }

  // normalize typed urls
  $type = drupal_strtolower($type);

  // If callback exists, call it, otherwise error out.
  if (isset($formats[$type]) && function_exists($formats[$type]['callback'])) {
    $o = call_user_func($formats[$type]['callback']);
    return $o;
  }
  else {
    drupal_set_message(t('Unknown export format(%the_format).', array('%the_format' => $type)), 'error');
    drupal_not_found();
  }
}

/**
 * Page callback: Constructs a form for importing a single recipe.
 *
 * @see recipe_menu()
 * @see recipe_import_form_build_preview()
 * @see recipe_import_form_validate()
 * @see recipe_import_form_submit()
 */
function recipe_import_form($form, &$form_state) {
  $formats = module_invoke_all('recipeio', 'import_single');

  $options = array();
  foreach ($formats as $format) {
    $options[$format['callback']] = $format['format_name'];
  }

  // Some special stuff when previewing a node.
  if (isset($form_state['node_preview'])) {
    $form['#prefix'] = $form_state['node_preview'];
  }

  $form['recipe_format'] = array(
    '#type' => 'select',
    '#title' => t('Recipe format'),
    '#options' => $options,
    '#default_value' => !empty($form_state['values']['recipe_format']) ? $form_state['values']['recipe_format'] : '',
    '#size' => 1,
    '#description' => t('The recipe input format.'),
  );
  $form['recipe_import_text'] = array(
    '#type' => 'textarea',
    '#title' => t('Paste import data here'),
    '#default_value' => !empty($form_state['values']['recipe_import_text']) ? $form_state['values']['recipe_import_text'] : '',
    '#cols' => 55,
    '#rows' => 8,
    '#required' => TRUE,
    '#description' => t('Use 1 blank line between sections: Description, Ingredients, Instructions, Notes. Always use preview first to avoid unintended consequences.'),
    '#attributes' => array('class' => array('recipe-import-text')),
  );
  $form['buttons']['preview'] = array(
    '#type' => 'submit',
    '#value' => t('Preview'),
    '#weight' => 1,
    '#submit' => array('recipe_import_form_build_preview'),
  );
  $form['buttons']['import'] = array(
    '#type' => 'submit',
    '#value' => t('Import'),
    '#weight' => 2,
    '#submit' => array('recipe_import_form_submit'),
  );

  return $form;
}

/**
 * Form submission handler for recipe_import_form() 'Preview' button.
 *
 * Import preview routine that allows that users to see what actually will be
 * imported before doing so.
 */
function recipe_import_form_build_preview($form, &$form_state) {
  drupal_add_css(drupal_get_path('module', 'recipe') . '/recipe.css');

  $parsed_recipe_object = recipe_import_parse($form, $form_state);
  if ($parsed_recipe_object != FALSE) {
    //$node = node_form_submit_build_node($form, $form_state);
    $node = recipe_import_get_node($parsed_recipe_object);
    $cloned_node = clone($node);
    $cloned_node->in_preview = 1;
    $form_state['node_preview'] = theme('node_preview', array('node' => $cloned_node));
    $form_state['rebuild'] = TRUE;
    drupal_set_title(t('Preview'));
  }
}

/**
 * Form validation handler for recipe_import_form().
 *
 * @see recipe_import_form_submit()
 */
function recipe_import_form_validate($form, &$form_state) {
  // Make sure that they choose an import format.
  // Otherwise the text entry is lost and the import fails with an error.
  if (empty($form_state['values']['recipe_format'])) {
    form_set_error('recipe_format', t('You must choose a recipe import format.'));
  }
}

/**
 * Form submission handler for recipe_import_form().
 *
 * @see recipe_import_form_validate()
 */
function recipe_import_form_submit($form, &$form_state) {
  global $user;
  $parsed_recipe_object = recipe_import_parse($form, $form_state);
  if (($node = recipe_import_get_node($parsed_recipe_object)) != FALSE) {
    node_save($node);
    $form_state['redirect'] = 'node/' . $node->nid . '/edit';
    drupal_set_message(t('Recipe Imported'));
  }
}

/**
 * Page callback: Displays bulk recipe import forms.
 *
 * @see recipe_menu()
 */
function recipe_import_multi($type = NULL) {
  drupal_set_title(t('Recipe bulk import'));

  // load supported formats
  $formats = module_invoke_all('recipeio', 'import_multi');
  $o = t('Supported bulk input formats:');
  if ( $type === NULL ) {
    $format_count = 0;
    foreach ( $formats as $key => $format ) {
      if ($format) {
        $o .= '<br/>' . l($format['format_name'], "admin/structure/recipe/import_multi/$key");
        $format_count++;
      }
    }
    if ( $format_count == 0 ) {
      $o .= '<br/><p>' . t('You have no import formats available with the bulk export feature.') . '</p>';
    }
    return $o;
  }

  // normalize typed urls
  $type = drupal_strtolower($type);

  // If callback exists, call it, otherwise error out.
  if (isset($formats[$type]) && function_exists($formats[$type]['callback'])) {
    $o = call_user_func($formats[$type]['callback']);
    return $o;
  }
  else {
    drupal_set_message(t('Unknown export format(%the_format).', array('%the_format' => $type)), 'error');
    drupal_not_found();
  }
}

/**
 * Returns a node-like stdClass object suitable for node_save and preview.
 */
function recipe_import_get_node($parsed_recipe_object = FALSE) {
  global $user;
  if ($parsed_recipe_object) {
    //node stuff
    $node = new stdClass();
    $node->type = 'recipe';
    $node->nid = NULL;
    node_object_prepare($node);
    $node->title = $parsed_recipe_object['title'];
    $node->language = LANGUAGE_NONE;

    //recipe stuff

    $node->recipe_description[LANGUAGE_NONE][0] = array(
      'value' => $parsed_recipe_object['title'] . ' imported from Recipe Import',
      'format' => 'filtered_html',
    );
    $node->recipe_instructions[LANGUAGE_NONE][0] = array(
      'value' => $parsed_recipe_object['instructions'],
      'format' => 'filtered_html',
    );
    $node->recipe_notes[LANGUAGE_NONE][0] = array(
      'value' => isset($parsed_recipe_object['notes']) ? $parsed_recipe_object['notes'] : '',
      'format' => 'filtered_html',
    );
    $node->recipe_source[LANGUAGE_NONE][0] = array(
      'value' => $parsed_recipe_object['source'] != '' ? $parsed_recipe_object['source'] : $user->name,
      'format' => 'filtered_html',
    );
    $node->recipe_prep_time[LANGUAGE_NONE][0] = array(
      'value' => $parsed_recipe_object['preptime'] != '' ? $parsed_recipe_object['preptime'] : 30,
    );
    $node->recipe_cook_time[LANGUAGE_NONE][0] = array(
      'value' => $parsed_recipe_object['cooktime'] != '' ? $parsed_recipe_object['cooktime'] : 30,
    );

    $node->recipe_yield = $parsed_recipe_object['yield'];
    $node->recipe_yield_unit = $parsed_recipe_object['yield_unit'];

    $delta = 0;
    $node->recipe_ingredient = array();
    foreach ($parsed_recipe_object['ingredients'] as $i) {
      $node->recipe_ingredient[LANGUAGE_NONE][$delta] = array(
        'iid' => recipe_ingredient_id_from_name($i['ingredient_name']),
        'quantity' => $i['quantity'],
        'unit_key' => $i['unit_key'],
        'note' => isset($i['ingredient_note']) ? $i['ingredient_note'] : '',
      );
      $delta++;
    }

    return $node;
  }
  return FALSE;
}

/**
 * Returns a parsed imported recipe based on the recipe_format.
 *
 * All parser instances should return a $recipe object that looks like this:
 *
 * $recipe = array(
 *   'title' => 'recipe title string',
 *   'ingredients' => array of ingredients items(below);
 *   'instructions' => 'string of instructions'
 * );
 *
 * ingredients items = array(
 *    'quantity' =>
 *    'ingredient_name' =>
 *    'unit_name' =>
 *    'unit_key' => see recipe_unit_fuzzymatch().  ==FALSE if no-match
 *    'ingre_obj' => comes from database lookup: see recipe_ingredient_match().  ==FALSE if no-match
 * );
 */
function recipe_import_parse($form, &$form_state) {
  $import_function = $form_state['values']['recipe_format'];
  $text = $form_state['values']['recipe_import_text'];

  $recipe = array();
  if (function_exists($import_function)) {
    $recipe = call_user_func($import_function, $text);
    return $recipe;
  }
  else {
    drupal_set_message(t('Recipe import function does not exist(%the_function)', array('%the_function' => $import_function)), 'error');
    return FALSE;
  }
}

/**
 * Returns a best-guess matched unit key for a unit of measure.
 *
 * Used by the various import plugins.
 *
 * @param string $recipe_name_or_abbrev
 *   A unit of measure abbreviation or a unit name.
 *
 * @return
 *   A recipe unit key as from recipe_get_units or FALSE if no match.
 */
function recipe_unit_fuzzymatch($unit_name_or_abbrev) {
  $units = recipe_get_units();

  // Empty strings should use the default non-printing 'unit'.
  if (empty($unit_name_or_abbrev)) {
    $unit_name_or_abbrev = 'unit';
  }

  // First pass unit case must match exactly( T=Tbsp, t=tsp ).
  foreach ($units as $unit_key => $u) {
    $pats = array();
    // Add name pattern.
    $pats[] = '^' . $u['name'] . 's{0,1}$';
    // Add plural name pattern.
    $pats[] = '^' . $u['plural'] . 's{0,1}$';
    // Add abbreviation pattern.
    $pats[] = '^' . $u['abbreviation'] . 's{0,1}\.{0,1}$';

    foreach ($u['aliases'] as $alias) {
      $pats[] = '^' . trim($alias) . 's{0,1}\.{0,1}$';
    }
    $search_pat = implode('|', $pats);
    if (preg_match("/$search_pat/", $unit_name_or_abbrev)) {
      return $unit_key;
    }
  }

  // Second pass unit case doesn't matter.
  foreach ($units as $unit_key => $u) {
    $pats = array();
    // Add name pattern.
    $pats[] = '^' . $u['name'] . 's{0,1}$';
    // Add plural name pattern.
    $pats[] = '^' . $u['plural'] . 's{0,1}$';
    // Add abbreviation pattern.
    $pats[] = '^' . $u['abbreviation'] . 's{0,1}\.{0,1}$';

    foreach ($u['aliases'] as $alias) {
      $pats[] = '^' . trim($alias) . 's{0,1}\.{0,1}$';
    }
    $search_pat = implode('|', $pats);
    if (preg_match("/$search_pat/i", $unit_name_or_abbrev)) {
      return $unit_key;
    }
  }

  return FALSE;
}

/**
 * Returns the ID and name of an existing ingredient.
 *
 * @param string $recipe_ingredient_name
 *   A recipe_ingredient_name.
 *
 * @return array
 *   A recipe_ingredient array upon successful load or FALSE
 */
function recipe_ingredient_match($recipe_ingredient_name) {
  $result = db_query("SELECT id, name FROM {recipe_ingredient} where name = :name", array(':name' => $recipe_ingredient_name));
  foreach ($result as $row) {
    return array(
      'id' => $row->id,
      'name' => $row->name
    );
  }
  return FALSE;
}

/**
 * Page callback: Outputs JSON for ingredient autocomplete suggestions.
 */
function recipe_autocomplete_page($string = "", $limit = 10) {
  $matches = array();
  $result = db_query_range("SELECT name FROM {recipe_ingredient} WHERE LOWER(name) LIKE :name ORDER BY name", 0, 10, array(':name' => strtolower($string) . '%'));
  foreach ($result as $record) {
    $matches[$record->name] = check_plain($record->name);
  }
  drupal_json_output($matches);
  exit();
}
