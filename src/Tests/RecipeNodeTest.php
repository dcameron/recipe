<?php

/*
 * @file
 * Contains \Drupal\recipe\Tests\RecipeNodeTest
 */

namespace Drupal\recipe\Tests;

use Drupal\Core\URL;
use Drupal\recipe\Tests\RecipeTestBase;

/**
 * Tests the functionality of the Recipe content type and Recipe blocks.
 *
 * @group recipe
 */
class RecipeNodeTest extends RecipeTestBase {

  /**
   * Creates a recipe node using the node form and tests the display.
   */
  public function testRecipeContent() {
    // Generate values for our test node.
    $title = $this->randomMachineName(16);
    $description = $this->randomMachineName(255);
    $yield_amount = 5;
    $yield_unit = $this->randomMachineName(10);
    $source = 'http://www.example.com';
    $notes = $this->randomMachineName(255);
    $instructions = $this->randomMachineName(255);
    $preptime = 60;
    $cooktime = 135;

    // Ingredient with quantity == 1 and unit tablespoon with note.
    $ing_0_quantity = 1;
    $ing_0_unit = 'tablespoon';
    $ing_0_name = $this->randomMachineName(16);
    $ing_0_note = $this->randomMachineName(16);

    $edit = array(
      'title[0][value]' => $title,
      'recipe_description[0][value]' => $description,
      'recipe_yield_amount[0][value]' => $yield_amount,
      'recipe_yield_unit[0][value]' => $yield_unit,
      'recipe_source[0][value]' => $source,
      'recipe_notes[0][value]' => $notes,
      'recipe_instructions[0][value]' => $instructions,
      'recipe_prep_time[0][value]' => $preptime,
      'recipe_cook_time[0][value]' => $cooktime,
      'recipe_ingredient[0][quantity]' => $ing_0_quantity,
      'recipe_ingredient[0][unit_key]' => $ing_0_unit,
      'recipe_ingredient[0][target_id]' => $ing_0_name,
      'recipe_ingredient[0][note]' => $ing_0_note,
    );

    // Post the values to the node form.
    $this->drupalPostForm('node/add/recipe', $edit, t('Save'));
    $this->assertText(t('Recipe @title has been created.', array('@title' => $title)));

    // Check the page for the recipe content.
    $this->assertRaw($description, 'Found the recipe description.');
    $this->assertText(format_string('@amount @unit', ['@amount' => $yield_amount, '@unit' => $yield_unit]), 'Found the recipe yield.');
    $this->assertRaw('<a href="http://www.example.com">http://www.example.com</a>', 'Found the recipe source.');
    $this->assertRaw($notes, 'Found the recipe notes.');
    $this->assertRaw($instructions, 'Found the recipe instructions');
    $this->assertText('1 hour', 'Found the recipe prep time.');
    $this->assertText('2 hours, 15 minutes', 'Found the recipe cook time.');
    $this->assertText('3 hours, 15 minutes', 'Found the recipe total time.');

    $this->assertText(t('1 T'), 'Found the ingredient quantity and abbreviation.');
    $this->assertText(format_string('@name (@note)', array('@name' => $ing_0_name, '@note' => $ing_0_note)), 'Found the ingredient name and note.');

    // Check the page HTML for the recipe RDF properties.
    $properties = array(
      'schema:Recipe',
      'schema:name',
      'schema:recipeInstructions',
      'schema:recipeIngredient',
      'schema:description',
      'schema:prepTime',
      'schema:cookTime',
      'schema:totalTime',
      'schema:recipeYield',
    );
    foreach ($properties as $property) {
      $this->assertRaw($property, format_string('Found the RDF property "@property" in the recipe node HTML.', array('@property' => $property)));
    }

    // Check the page HTML for the ISO 8601 recipe durations.
    $durations = array(
      'prep_time' => 'PT1H',
      'cook_time' => 'PT2H15M',
      'total_time' => 'PT3H15M',
    );
    foreach ($durations as $duration) {
      $this->assertRaw($duration, format_string('Found the ISO 8601 duration "@duration" in the recipe node HTML.', array('@duration' => $duration)));
    }

    // Check for the breadcrumb.
    $expected_breadcrumb = [];
    $expected_breadcrumb[] = URL::fromRoute('<front>')->toString();
    $expected_breadcrumb[] = URL::fromRoute('view.recipe_name_index.page_1')->toString();

    // Fetch links in the current breadcrumb.
    $links = $this->xpath('//nav[@class="breadcrumb"]/ol/li/a');
    $got_breadcrumb = array();
    foreach ($links as $link) {
      $got_breadcrumb[] = (string) $link['href'];
    }

    // Compare expected and got breadcrumbs.
    $this->assertIdentical($expected_breadcrumb, $got_breadcrumb, 'The breadcrumb is correctly displayed on the page.');
  }
}
