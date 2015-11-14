<?php

/*
 * @file
 * Contains \Drupal\ingredient\Tests\IngredientFieldTest
 */

namespace Drupal\ingredient\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the functionality of the ingredient field.
 *
 * @group recipe
 */
class IngredientFieldTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('ingredient', 'node');

  /**
   * A test user with administrative privileges.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $admin_user;

  /**
   * A list of units available for ingredient amounts.
   *
   * @var array
   */
  protected $unit_list;

  public function setUp() {
    parent::setUp();

    // Create a new content type for testing.
    $content_type = $this->drupalCreateContentType(array('type' => 'test_bundle'));

    // Create and log in the admin user.
    $this->admin_user = $this->drupalCreateUser(array('create test_bundle content', 'add ingredient', 'view ingredient', 'administer site configuration'));
    $this->drupalLogin($this->admin_user);

    // Populate the unit list.
    $this->unit_list = ingredient_get_units();
  }

  /**
   * Tests adding data with the ingredient field.
   */
  public function testIngredientField() {
    $field_name = strtolower($this->randomMachineName());
    $this->createIngredientField($field_name, 'node', 'test_bundle');

    $test_ingredients = [];

    // Ingredient with quantity == 1 and unit tablespoon with note.
    $test_ingredients[] = [
      'quantity' => 1,
      'unit_key' => 'tablespoon',
      'name' => $this->randomMachineName(16),
      'note' => $this->randomMachineName(16),
    ];
    // Ingredient with quantity > 1 and unit tablespoon with note.
    $test_ingredients[] = [
      'quantity' => 2,
      'unit_key' => 'tablespoon',
      'name' => $this->randomMachineName(16),
      'note' => $this->randomMachineName(16),
    ];
    // Ingredient with quantity == 0 and unit tablespoon with note.
    $test_ingredients[] = [
      'quantity' => 0,
      'unit_key' => 'tablespoon',
      'name' => $this->randomMachineName(16),
      'note' => $this->randomMachineName(16),
    ];
    // Ingredient without note.
    $test_ingredients[] = [
      'quantity' => 1,
      'unit_key' => 'tablespoon',
      'name' => $this->randomMachineName(16),
      'note' => '',
    ];
    // Ingredient with unit that has no abbreviation.
    $test_ingredients[] = [
      'quantity' => 10,
      'unit_key' => 'unit',
      'name' => $this->randomMachineName(16),
      'note' => $this->randomMachineName(16),
    ];
    // Ingredient with fractional quantity and unit tablespoon.
    $test_ingredients[] = [
      'quantity' => '1/4',
      'unit_key' => 'tablespoon',
      'name' => $this->randomMachineName(16),
      'note' => '',
    ];
    // Ingredient with mixed fractional quantity and unit tablespoon.
    $test_ingredients[] = [
      'quantity' => '2 2/3',
      'unit_key' => 'tablespoon',
      'name' => $this->randomMachineName(16),
      'note' => '',
    ];

    foreach ($test_ingredients as $_ingredient) {
      // Create a new test_bundle node with the ingredient field values.
      $edit = [
        'title[0][value]' => $this->randomMachineName(16),
        'ingredient[0][quantity]' => $ingredient['quantity'],
        'ingredient[0][unit_key]' => $ingredient['unit_key'],
        'ingredient[0][name]' => $ingredient['name'],
        'ingredient[0][note]' => $ingredient['note'],
      ];
      $this->drupalPostForm('node/add/test_bundle', $edit, t('Save'));

      $formatted_quantity = str_replace('/', '&frasl;', $ingredient['quantity']);

      // Check for the presence or absence of the ingredient quantity and unit
      // abbreviation.
      if ($ingredient['quantity'] === 0) {
        // Ingredients with quantities === 0 should not display the quantity or
        // units.
        $this->assertNoText(t('@quantity @unit', array('@quantity' => $formatted_quantity, '@unit' => $this->unit_list[$ingredient['unit_key']]['abbreviation'])), 'Did not find the ingredient quantity === 0.');
      }
      elseif ($ingredient['unit_key'] == 'unit') {
        $this->assertRaw(format_string('<span class="quantity-unit" property="schema:amount"> @quantity </span>', array('@quantity' => $formatted_quantity)), 'Found the ingredient quantity with no unit.');
      }
      else {
        $unit_abbreviation = $this->unit_list[$ingredient['unit_key']]['abbreviation'];
        $this->assertText(t('@quantity @unit', array('@quantity' => $formatted_quantity, '@unit' => $unit_abbreviation)), 'Found the ingredient quantity and unit abbreviation.');
      }

      // Check for the ingredient name and the presence or absence of the note.
      if ($ingredient['note'] === '') {
        $this->assertText(t('@name'), ['@name' => $ingredient['name']], 'Found the ingredient name.');
        $this->assertNoText(format_string('@name (@note)', array('@name' => $ingredient['name'], '@note' => $ingredient['note'])), 'Did not find ingredient name with blank note field, "()".');
      }
      else {
        $this->assertText(format_string('@name (@note)', array('@name' => $ingredient['name'], '@note' => $ingredient['note'])), 'Found the ingredient name and note.');
      }
    }
  }

  /**
   * Tests ingredient field settings.
   */
  public function testIngredientFieldSettings() {
    // Create an ingredient field on the test_bundle node type.
    $field_name = strtolower($this->randomMachineName());
    $storage_settings = [
      'ingredient_name_normalize' => 1,
    ];
    $field_settings = [
      'default_unit' => 'cup',
    ];
    $this->createIngredientField($field_name, 'node', 'test_bundle', $storage_settings, $field_settings);

    // 7.x field instance settings retained for display settings.
    /*$instance = array(
      'bundle' => 'test_bundle',
      'display' => array(
        'default' => array(
          'label' => 'above',
          'module' => 'recipe',
          'settings' => array(
            'fraction_format' => '{%d }%d&frasl;%d',
            'unit_abbreviation' => 0,
          ),
          'type' => 'recipe_ingredient_default',
          'weight' => 0,
        ),
      ),
      'entity_type' => 'node',
      'field_name' => 'ingredient',
      'label' => 'Ingredients',
      'widget' => array(
        'active' => 0,
        'module' => 'recipe',
        'settings' => array(
          'default_unit' => 'cup',
        ),
        'type' => 'recipe_ingredient_autocomplete',
        'weight' => 0,
      ),
    );*/

    $edit = array(
      'title[0][value]' => $this->randomMachineName(16),
      'ingredient[0][quantity]' => 4,
      'ingredient[0][unit_key]' => 'us gallon',
      'ingredient[0][name]' => 'TeSt InGrEdIeNt',
      'ingredient[0][note]' => '',
    );

    $this->drupalGet('node/add/test_bundle');
    // Assert that the default element, 'cup', is selected.
    $this->assertOptionSelected('edit-ingredient-und-0-unit-key', 'cup', 'The default unit was selected.');
    // Post the values to the node form.
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Assert that the normalized ingredient name can be found on the node page.
    $this->assertText('test ingredient', 'Found the normalized ingredient name.');
  }

  /**
   * Creates a new ingredient field.
   *
   * @param string $name
   *   The name of the new field (all lowercase), exclude the "field_" prefix.
   * @param string $entity_type
   *   The entity type.
   * @param string $bundle
   *   The bundle that this field will be added to.
   * @param array $storage_settings
   *   A list of field storage settings that will be added to the defaults.
   * @param array $field_settings
   *   A list of instance settings that will be added to the instance defaults.
   * @param array $widget_settings
   *   A list of widget settings that will be added to the widget defaults.
   */
  protected function createIngredientField($name, $entity_type, $bundle, $storage_settings = array(), $field_settings = array(), $widget_settings = array()) {
    $field_storage = entity_create('field_storage_config', array(
      'entity_type' => $entity_type,
      'field_name' => $name,
      'type' => 'ingredient',
      'settings' => $storage_settings,
      'cardinality' => !empty($storage_settings['cardinality']) ? $storage_settings['cardinality'] : 1,
    ));
    $field_storage->save();

    $this->attachIngredientField($name, $entity_type, $bundle, $field_settings, $widget_settings);
    return $field_storage;
  }

  /**
   * Attaches an ingredient field to an entity.
   *
   * @param string $name
   *   The name of the new field (all lowercase), exclude the "field_" prefix.
   * @param string $entity_type
   *   The entity type this field will be added to.
   * @param string $bundle
   *   The bundle this field will be added to.
   * @param array $field_settings
   *   A list of field settings that will be added to the defaults.
   * @param array $widget_settings
   *   A list of widget settings that will be added to the widget defaults.
   */
  protected function attachIngredientField($name, $entity_type, $bundle, $field_settings = array(), $widget_settings = array()) {
    $field = array(
      'field_name' => $name,
      'label' => $name,
      'entity_type' => $entity_type,
      'bundle' => $bundle,
      'required' => !empty($field_settings['required']),
      'settings' => $field_settings,
    );
    entity_create('field_config', $field)->save();

    entity_get_form_display($entity_type, $bundle, 'default')
      ->setComponent($name, array(
        'type' => 'ingredient_autocomplete',
        'settings' => $widget_settings,
      ))
      ->save();
    // Assign display settings.
    entity_get_display($entity_type, $bundle, 'default')
      ->setComponent($name, array(
        'label' => 'hidden',
        'type' => 'ingredient_default',
      ))
      ->save();
  }

}
