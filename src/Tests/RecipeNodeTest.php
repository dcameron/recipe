<?php

/*
 * @file
 * Contains \Drupal\recipe\Tests\RecipeNodeTest
 */

namespace Drupal\recipe\Tests;

use Drupal\recipe\Tests\RecipeTestBase;

/**
 * Tests the functionality of the Recipe content type and Recipe blocks.
 *
 * @group recipe
 */
class RecipeNodeTest extends RecipeTestBase {

  /**
   * Creates a recipe node using the node form and test the module settings.
   */
  public function testRecipeContent() {
    // Generate values for our test node.
    $title = $this->randomMachineName(16);
    $description = $this->randomMachineName(255);
    $yield_unit = $this->randomMachineName(10);
    $yield = 5;
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
/*      'recipe_yield_unit' => $yield_unit,
      'recipe_yield' => $yield,*/
      'recipe_source[0][value]' => $source,
      'recipe_notes[0][value]' => $notes,
      'recipe_instructions[0][value]' => $instructions,
/*      'recipe_prep_time[0][value]' => $preptime,
      'recipe_cook_time[0][value]' => $cooktime,*/
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
    $this->assertFieldById('edit-custom-yield', $yield, 'Found the recipe yield in the custom yield form.');
    $this->assertText($yield_unit, 'Found the recipe yield unit.');
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
      'schema:instructions',
      'schema:summary',
      'schema:prepTime',
      'schema:cookTime',
      'schema:totalTime',
      // @todo 'schema:yield' is defined in recipe_rdf_mapping(), but is not
      // currently implemented in any theme function.
      //'schema:yield',
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

    // Change the Recipe module settings.
    $summary_title = $this->randomMachineName(16);
    $edit = array(
      // Hide the recipe summary.
      // @todo The recipe summary location setting currently does nothing.
      //'recipe_summary_location' => 2,
      // Change the Summary block title.
      'recipe_summary_title' => $summary_title,
    );

    // Post the values to the settings form.
    $this->drupalPostForm('admin/config/content/recipe', $edit, t('Save configuration'));

    // Check the recipe node display again.
    $this->drupalGet('node/1');

    //$this->assertNoText(t('Summary'), 'Did not find the recipe summary.');

    // Enable the Newest Recipes and Recipe Summary blocks.
    // Check for it and the node link.
    $edit = array(
      "blocks[recipe_recent][region]" => 'sidebar_first',
      "blocks[recipe_summary][region]" => 'sidebar_first',
    );
    $this->drupalPostForm('admin/structure/block', $edit, t('Save blocks'));
    $this->assertText(t('Newest recipes'), 'Found the Newest recipes block.');
    $this->assertLink($title, 0);
    // Make sure the Summary block doesn't appear on a non-recipe-node page.
    $this->assertNoText($summary_title, 'Did not find the altered Summary block title.');

    // Check for the Summary block on the recipe node page.
    $this->drupalGet('node/1');
    $this->assertText($summary_title, 'Found the altered Summary block title.');

    // Check for the description in the teaser view at /node.
    $this->drupalGet('node');
    $this->assertRaw($description, 'Found the recipe description.');
  }
}
