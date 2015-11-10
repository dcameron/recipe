<?php

/*
 * @file
 * Contains \Drupal\recipe\Tests\RecipeTestBase
 */

namespace Drupal\recipe\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides setup and helper methods for recipe module tests.
 */
class RecipeTestBase extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('block', 'recipe');

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

    // Create and log in the admin user with Recipe content permissions.
    $this->admin_user = $this->drupalCreateUser(array('create recipe content', 'edit any recipe content', 'administer site configuration', 'administer blocks'));
    $this->drupalLogin($this->admin_user);

    // Populate the unit list.
    $this->unit_list = recipe_get_units();
  }
}
