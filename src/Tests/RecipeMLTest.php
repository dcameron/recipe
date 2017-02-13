<?php

/*
 * @file
 * Contains \Drupal\recipe\Tests\RecipeMLTest
 */

namespace Drupal\recipe\Tests;

use Drupal\views\Views;

/**
 * Tests the RecipeML Views style plugin.
 *
 * @group recipe
 */
class RecipeMLTest extends RecipeTestBase {

  /**
   * Tests the display of Recipe nodes using the RecipeML Views style plugin.
   */
  public function testViewsStyle() {
    // Generate values for our test node.
    $title = $this->randomMachineName(16);
    $description = $this->randomMachineName(255);
    $yield_amount = 5;
    $yield_unit = $this->randomMachineName(10);
    $source = $this->randomMachineName(255);
    $notes = $this->randomMachineName(255);
    $instructions = $this->randomMachineName(255);
    $preptime = 60;
    $cooktime = 135;

    // Ingredient with quantity == 1 and unit tablespoon with note.
    $ing_0_quantity = 1;
    $ing_0_unit = 'tablespoon';
    $ing_0_name = $this->randomMachineName(16);
    $ing_0_note = $this->randomMachineName(16);

    $edit = [
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
    ];

    // Post the values to the node form.
    $this->drupalPostForm('node/add/recipe', $edit, t('Save'));
    $this->assertText(t('Recipe @title has been created.', ['@title' => $title]));

    // Enable the RecipeML view.
    $view = Views::getView('recipeml');
    $view->initDisplay('recipeml');
    $view->storage->enable()->save();
    $this->container->get('router.builder')->rebuildIfNeeded();

    // Check the page for the recipe content.
    $this->drupalGet('node/1/recipeml');
    $result = current($this->xpath("//recipe/@*[name()='xml:lang']"));
    $this->assertEqual($result, 'en', 'Found the xml:lang attribute.');
    $result = current($this->xpath("//recipe/title"));
    $this->assertEqual($result, $title, 'Found the recipe title.');
    $result = current($this->xpath("//recipe/source"));
    $this->assertEqual($result->p, $source, 'Found the recipe source.');
    $result = current($this->xpath("//recipe/preptime[@type='Preparation time']"));
    $this->assertEqual($result->time->qty, 60,  'Found the recipe preparation time.');
    $result = current($this->xpath("//recipe/preptime[@type='Cooking time']"));
    $this->assertEqual($result->time->qty, 135,  'Found the recipe cooking time.');
    $result = current($this->xpath("//recipe/preptime[@type='Total time']"));
    $this->assertEqual($result->time->qty, 195,  'Found the recipe total time.');
    $result = current($this->xpath("//recipe/yield"));
    $this->assertEqual($result->qty, $yield_amount, 'Found the recipe yield.');
    $this->assertEqual($result->unit, $yield_unit, 'Found the recipe yield unit.');
    $result = current($this->xpath("//recipe/description"));
    $this->assertEqual($result->p, $description, 'Found the recipe description.');
    $result = current($this->xpath("//recipe/ingredients/ing"));
    $this->assertEqual($result->amt->qty, $ing_0_quantity, 'Found the ingredient 0 quantity');
    $this->assertEqual($result->amt->unit, 'T', 'Found the ingredient 0 unit');
    $this->assertEqual($result->item, $ing_0_name, 'Found the ingredient 0 name');
    $this->assertEqual($result->prep, $ing_0_note, 'Found the ingredient 0 note');
    $result = current($this->xpath("//recipe/directions"));
    $this->assertEqual($result->p, $instructions, 'Found the recipe instructions');
  }

}
