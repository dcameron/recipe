<?php

namespace Drupal\ingredient\Tests;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\simpletest\WebTestBase;

/**
 * Tests the functionality of the ingredient field.
 *
 * @group recipe
 */
class IngredientFieldTest extends WebTestBase {

  use IngredientTestTrait;

  /**
   * Modules to enable.
   *
   * @var string[]
   */
  public static $modules = ['field_ui', 'ingredient', 'node'];

  /**
   * A test user with administrative privileges.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $admin_user;

  /**
   * {@inheritdoc}
   */
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
   * Tests adding data with the ingredient field.
   */
  public function testIngredientField() {
    $display_settings = [
      'fraction_format' => '{%d }%d/%d',
    ];
    $this->createIngredientField([], [], [], $display_settings);

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

    foreach ($test_ingredients as $ingredient) {
      // Create a new test_bundle node with the ingredient field values.
      $title = $this->randomMachineName(16);
      $edit = [
        'title[0][value]' => $title,
        'field_ingredient[0][quantity]' => $ingredient['quantity'],
        'field_ingredient[0][unit_key]' => $ingredient['unit_key'],
        'field_ingredient[0][target_id]' => $ingredient['name'],
        'field_ingredient[0][note]' => $ingredient['note'],
      ];
      $this->drupalPostForm('node/add/test_bundle', $edit, t('Save'));

      // Check for the node title to verify redirection to the node view.
      $this->assertText($title, 'Found the node title.');

      // Check for the presence or absence of the ingredient quantity and unit
      // abbreviation.
      if ($ingredient['quantity'] === 0) {
        // Ingredients with quantities === 0 should not display the quantity or
        // units.
        $this->assertNoText(t('0 T'), 'Did not find the ingredient quantity === 0.');
      }
      elseif ($ingredient['unit_key'] == 'unit') {
        $this->assertRaw(new FormattableMarkup('<span class="quantity-unit">@quantity</span>', ['@quantity' => $ingredient['quantity']]), 'Found the ingredient quantity with no unit.');
      }
      else {
        $this->assertText(t('@quantity T', ['@quantity' => $ingredient['quantity']]), 'Found the ingredient quantity and unit abbreviation.');
      }

      // Check for the ingredient name and the presence or absence of the note.
      if ($ingredient['note'] === '') {
        $this->assertText(new FormattableMarkup('@name', ['@name' => $ingredient['name']]), 'Found the ingredient name.');
        $this->assertNoText(new FormattableMarkup('@name (@note)', ['@name' => $ingredient['name'], '@note' => $ingredient['note']]), 'Did not find ingredient name with blank note field, "()".');
      }
      else {
        $this->assertText(new FormattableMarkup('@name (@note)', ['@name' => $ingredient['name'], '@note' => $ingredient['note']]), 'Found the ingredient name and note.');
      }
    }
  }

  /**
   * Tests ingredient formatter settings.
   *
   * @todo Add assertions for singular/plural unit full names.
   */
  public function testIngredientFormatterSettings() {
    $this->createIngredientField();

    // Verify that the ingredient entity link display is turned off by default.
    $this->drupalGet('admin/structure/types/manage/test_bundle/display');
    $this->assertText('Link to ingredient: No', 'Ingredient entity link display is turned off.');

    $edit = [
      'title[0][value]' => $this->randomMachineName(16),
      'field_ingredient[0][quantity]' => 4,
      'field_ingredient[0][unit_key]' => 'tablespoon',
      'field_ingredient[0][target_id]' => 'test ingredient',
      'field_ingredient[0][note]' => '',
    ];

    $this->drupalGet('node/add/test_bundle');
    // Post the values to the node form.
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Verify that the ingredient name is not linked to its entity.
    $this->assertText('4 T', 'Found the unit abbreviation.');
    $this->assertText('test ingredient', 'Found the ingredient name.');
    $this->assertNoLink('test ingredient', 'Ingredient entity link is not displayed.');

    // Turn ingredient entity link display on.
    $this->updateIngredientField([], [], ['link' => TRUE, 'unit_display' => 1]);

    // Verify that the ingredient entity link display is turned on.
    $this->drupalGet('admin/structure/types/manage/test_bundle/display');
    $this->assertText('Link to ingredient: Yes', 'Ingredient entity link display is turned on.');

    // Verify that the ingredient name is linked to its entity.
    $this->drupalGet('node/1');
    $this->assertText('4 tablespoons', 'Found the unit full name.');
    $this->assertText('test ingredient', 'Found the ingredient name.');
    $this->assertLink('test ingredient', 0, 'Ingredient entity link is displayed.');
  }

}
