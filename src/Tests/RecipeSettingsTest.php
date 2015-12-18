<?php

/*
 * @file
 * Contains \Drupal\recipe\Tests\RecipeSettingsTest
 */

namespace Drupal\recipe\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the functionality of the Recipe module settings.
 *
 * @group recipe
 */
class RecipeSettingsTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('recipe');

  /**
   * A test user with administrative privileges.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $admin_user;

  public function setUp() {
    parent::setUp();

    // Create and log in the admin user with Recipe content permissions.
    $this->admin_user = $this->drupalCreateUser(array('create recipe content', 'edit any recipe content', 'administer content types'));
    $this->drupalLogin($this->admin_user);
  }

  /**
   * Tests the pseudo-field label settings.
   */
  public function testPseudoFieldLabels() {
    $title = $this->randomMachineName(16);
    $yield_amount = 5;
    $yield_unit = $this->randomMachineName(10);
    $preptime = 60;
    $cooktime = 135;

    $edit = array(
      'title[0][value]' => $title,
      'recipe_yield_amount[0][value]' => $yield_amount,
      'recipe_yield_unit[0][value]' => $yield_unit,
      'recipe_prep_time[0][value]' => $preptime,
      'recipe_cook_time[0][value]' => $cooktime,
    );

    // Post the values to the node form.
    $this->drupalPostForm('node/add/recipe', $edit, t('Save'));
    $this->assertText(t('Recipe @title has been created.', ['@title' => $title]));

    // Check for the default pseudo-field labels.
    $this->assertText(t('Total time'), 'Found the default Total time label.');
    $this->assertText(t('Yield'), 'Found the default Yield label.');

    // Alter the pseudo-field labels.
    $total_time_label = $this->randomMachineName(20);
    $yield_label = $this->randomMachineName(20);
    $edit = [
      'recipe_total_time_label' => $total_time_label,
      'recipe_yield_label' => $yield_label,
    ];

    // Post the values to the settings form.
    $this->drupalPostForm('admin/structure/types/manage/recipe', $edit, t('Save content type'));
    $this->assertText(t('The content type Recipe has been updated.'));

    // Check the node display for the new labels.
    $this->drupalGet('node/1');
    $this->assertText($total_time_label, 'Found the new Total time label.');
    $this->assertText($yield_label, 'Found the new Yield label.');

    // Alter the pseudo-field label displays.
    $edit = [
      'recipe_total_time_label_display' => 'hidden',
      'recipe_yield_label_display' => 'hidden',
    ];

    // Post the values to the settings form.
    $this->drupalPostForm('admin/structure/types/manage/recipe', $edit, t('Save content type'));
    $this->assertText(t('The content type Recipe has been updated.'));

    // Check the node display for the new labels.
    $this->drupalGet('node/1');
    $this->assertNoText($total_time_label, 'The Total time label display was set to hidden.');
    $this->assertNoText($yield_label, 'The Yield label display was set to hidden.');
  }

}
