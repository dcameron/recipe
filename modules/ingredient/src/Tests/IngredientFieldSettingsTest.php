<?php

/**
 * @file
 * Contains \Drupal\ingredient\Tests\IngredientFieldSettingsTest
 */

namespace Drupal\ingredient\Tests;

use Drupal\ingredient\Tests\IngredientTestTrait;
use Drupal\simpletest\WebTestBase;

/**
 * Tests the functionality of the ingredient field settings.
 *
 * @group recipe
 */
class IngredientFieldSettingsTest extends WebTestBase {

  use IngredientTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['field_ui', 'ingredient', 'node'];

  /**
   * A test user with administrative privileges.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $admin_user;

  public function setUp() {
    parent::setUp();

    // Create a new content type for testing.
    $this->ingredientCreateContentType();

    // Create and log in the admin user.
    $permissions = [
      'create test_bundle content',
      'access content',
      'administer node fields',
      'administer node display',
      'add ingredient',
      'view ingredient',
      'administer site configuration',
    ];
    $this->admin_user = $this->drupalCreateUser($permissions);
    $this->drupalLogin($this->admin_user);
  }

  /**
   * Tests ingredient field settings.
   */
  public function testIngredientFieldSettings() {
    // Create an ingredient field on the test_bundle node type.
    $field_settings = [
      'unit_sets' => [
        'us',
        'si',
        'common',
      ],
      'default_unit' => '',
    ];
    $this->createIngredientField([], $field_settings);

    // Visit the field settings page and verify that the settings are selected.
    $this->drupalGet('admin/structure/types/manage/test_bundle/fields/node.test_bundle.field_ingredient');
    $this->assertFieldChecked('edit-settings-unit-sets-us', 'The U.S. customary unit set was enabled.');
    $this->assertFieldChecked('edit-settings-unit-sets-si', 'The SI/Metric unit set was enabled.');
    $this->assertFieldChecked('edit-settings-unit-sets-common', 'The Common unit set was enabled.');
    $this->assertOptionSelected('edit-settings-default-unit', '', 'The blank default unit was selected.');

    // Visit the node edit page and verify that we can find units from each of
    // the enabled sets and that the select element shows the empty option by
    // default.
    $this->drupalGet('node/add/test_bundle');
    $this->assertFieldByXPath("//option[@value='cup']", t('cup (c)'), 'Found an option from the U.S. customary unit set.');
    $this->assertFieldByXPath("//option[@value='milliliter']", t('milliliter (ml)'), 'Found an option from the SI/Metric unit set.');
    $this->assertFieldByXPath("//option[@value='tablespoon']", t('tablespoon (T)'), 'Found an option from the Common unit set.');
    $this->assertOptionSelected('edit-field-ingredient-0-unit-key', '', 'The empty unit option was selected.');

    // Update the field settings and disable the SI/Metric unit set.  Then
    // verify that its unit cannot be found on the node edit page.  Also verify
    // that the default unit is selected.
    $field_settings = [
      'unit_sets' => [
        'us',
        'common',
      ],
      'default_unit' => 'cup',
    ];
    $this->updateIngredientField($field_settings);
    $this->drupalGet('node/add/test_bundle');
    $this->assertFieldByXPath("//option[@value='cup']", t('cup (c)'), 'Found an option from the U.S. customary unit set.');
    $this->assertNoFieldByXPath("//option[@value='milliliter']", t('milliliter (ml)'), 'Found an option from the SI/Metric unit set.');
    $this->assertFieldByXPath("//option[@value='tablespoon']", t('tablespoon (T)'), 'Found an option from the Common unit set.');
    $this->assertOptionSelected('edit-field-ingredient-0-unit-key', 'cup', 'The default unit was selected.');

    // Update the field settings and disable all unit sets to verify that all
    // units will then appear in the edit form by default.
    $field_settings = [
      'unit_sets' => [],
      'default_unit' => '',
    ];
    $this->updateIngredientField($field_settings);
    $this->drupalGet('node/add/test_bundle');
    $this->assertFieldByXPath("//option[@value='cup']", t('cup (c)'), 'Found an option from the U.S. customary unit set.');
    $this->assertFieldByXPath("//option[@value='milliliter']", t('milliliter (ml)'), 'Found an option from the SI/Metric unit set.');
    $this->assertFieldByXPath("//option[@value='tablespoon']", t('tablespoon (T)'), 'Found an option from the Common unit set.');
  }

}
