<?php

/*
 * @file
 * Contains \Drupal\recipe\Tests\RecipeIngredientSettingsTest
 */

namespace Drupal\recipe\Tests;

use Drupal\Core\Language\Language;
use Drupal\recipe\Tests\RecipeTestBase;

/**
 * Tests the functionality of the ingredient field settings.
 *
 * @group recipe
 */
class RecipeIngredientSettingsTest extends RecipeTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('recipe');

  public function setUp() {
    // Enable modules required for testing.
    parent::setUp();

    // Create a new content type for testing.
    $content_type = $this->drupalCreateContentType(array('type' => 'test_bundle'));

    // Create and log in the admin user with Recipe content permissions.
    $this->admin_user = $this->drupalCreateUser(array('create test_bundle content', 'administer site configuration'));
    $this->drupalLogin($this->admin_user);
  }

  /**
   * Tests ingredient field settings.
   */
  public function testIngredientFieldSettings() {
    // Create the field.
    $field = array(
      'cardinality' => -1,
      'field_name' => 'ingredient',
      'module' => 'recipe',
      'settings' => array(
        'ingredient_name_normalize' => 1,
      ),
      'type' => 'ingredient_reference',
    );
    field_create_field($field);

    // Create the field instance.
    $instance = array(
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
    );
    field_create_instance($instance);

    $language = Language::LANGCODE_NOT_SPECIFIED;
    $edit = array(
      'title' => $this->randomString(16),
      'ingredient[' . $language . '][0][quantity]' => 4,
      'ingredient[' . $language . '][0][unit_key]' => 'us gallon',
      'ingredient[' . $language . '][0][name]' => 'TeSt InGrEdIeNt',
      'ingredient[' . $language . '][0][note]' => '',
    );

    $this->drupalGet('node/add/test_bundle');
    // Assert that the default element, 'cup', is selected.
    $this->assertOptionSelected('edit-ingredient-und-0-unit-key', 'cup', 'The default unit was selected.');
    // Post the values to the node form.
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Assert that the normalized ingredient name can be found on the node page.
    $this->assertText('test ingredient', 'Found the normalized ingredient name.');
  }
}
