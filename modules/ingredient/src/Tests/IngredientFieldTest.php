<?php

/*
 * @file
 * Contains \Drupal\ingredient\Tests\IngredientFieldTest
 */

namespace Drupal\ingredient\Tests;

use Drupal\ingredient\Tests\IngredientFieldTestBase;

/**
 * Tests the functionality of the ingredient field.
 *
 * @group recipe
 */
class IngredientFieldTest extends IngredientFieldTestBase {

  /**
   * Tests adding data with the ingredient field.
   */
  public function testIngredientField() {
    $field_name = strtolower($this->randomMachineName());
    $display_settings = [
      'fraction_format' => '{%d }%d/%d',
    ];
    $this->createIngredientField($field_name, 'node', 'test_bundle', [], [], [], $display_settings);

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
        $field_name . '[0][quantity]' => $ingredient['quantity'],
        $field_name . '[0][unit_key]' => $ingredient['unit_key'],
        $field_name . '[0][target_id]' => $ingredient['name'],
        $field_name . '[0][note]' => $ingredient['note'],
      ];
      $this->drupalPostForm('node/add/test_bundle', $edit, t('Save'));

      // Check for the node title to verify redirection to the node view.
      $this->assertText($title, 'Found the node title.');

      // Check for the presence or absence of the ingredient quantity and unit
      // abbreviation.
      if ($ingredient['quantity'] === 0) {
        // Ingredients with quantities === 0 should not display the quantity or
        // units.
        $this->assertNoText(t('@quantity @unit', array('@quantity' => $ingredient['quantity'], '@unit' => $this->unit_list[$ingredient['unit_key']]['abbreviation'])), 'Did not find the ingredient quantity === 0.');
      }
      elseif ($ingredient['unit_key'] == 'unit') {
        $this->assertRaw(format_string('<span class="quantity-unit" property="schema:amount">@quantity</span>', array('@quantity' => $ingredient['quantity'])), 'Found the ingredient quantity with no unit.');
      }
      else {
        $unit_abbreviation = $this->unit_list[$ingredient['unit_key']]['abbreviation'];
        $this->assertText(t('@quantity @unit', array('@quantity' => $ingredient['quantity'], '@unit' => $unit_abbreviation)), 'Found the ingredient quantity and unit abbreviation.');
      }

      // Check for the ingredient name and the presence or absence of the note.
      if ($ingredient['note'] === '') {
        $this->assertText(format_string('@name', ['@name' => $ingredient['name']]), 'Found the ingredient name.');
        $this->assertNoText(format_string('@name (@note)', array('@name' => $ingredient['name'], '@note' => $ingredient['note'])), 'Did not find ingredient name with blank note field, "()".');
      }
      else {
        $this->assertText(format_string('@name (@note)', array('@name' => $ingredient['name'], '@note' => $ingredient['note'])), 'Found the ingredient name and note.');
      }
    }
  }

  /**
   * Tests ingredient formatter settings.
   *
   * todo: Add assertions for singular/plural unit full names.
   */
  public function testIngredientFormatterSettings() {
    // Create an ingredient field on the test_bundle node type.
    $field_name = strtolower($this->randomMachineName());
    $this->createIngredientField($field_name, 'node', 'test_bundle');

    // Verify that the ingredient entity link display is turned off by default.
    $this->drupalGet('admin/structure/types/manage/test_bundle/display');
    $this->assertText('Link to ingredient: No', 'Ingredient entity link display is turned off.');

    $edit = array(
      'title[0][value]' => $this->randomMachineName(16),
      $field_name . '[0][quantity]' => 4,
      $field_name . '[0][unit_key]' => 'us gallon',
      $field_name . '[0][target_id]' => 'test ingredient',
      $field_name . '[0][note]' => '',
    );

    $this->drupalGet('node/add/test_bundle');
    // Post the values to the node form.
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Verify that the ingredient name is not linked to its entity.
    $this->assertText('test ingredient', 'Found the ingredient name.');
    $this->assertNoLink('test ingredient', 'Ingredient entity link is not displayed.');

    // Turn ingredient entity link display on.
    $this->updateIngredientField($field_name, 'node', 'test_bundle', [], [], ['link' => TRUE]);

    // Verify that the ingredient entity link display is turned on.
    $this->drupalGet('admin/structure/types/manage/test_bundle/display');
    $this->assertText('Link to ingredient: Yes', 'Ingredient entity link display is turned on.');

    // Verify that the ingredient name is linked to its entity.
    $this->drupalGet('node/1');
    $this->assertText('test ingredient', 'Found the ingredient name.');
    $this->assertLink('test ingredient', 0, 'Ingredient entity link is displayed.');
  }

}
