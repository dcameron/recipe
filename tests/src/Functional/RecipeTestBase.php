<?php

namespace Drupal\Tests\recipe\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Provides setup and helper methods for recipe module tests.
 */
abstract class RecipeTestBase extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var string[]
   */
  public static $modules = ['block', 'recipe', 'views'];

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
    $this->drupalPlaceBlock('system_breadcrumb_block');

    // Create and log in the admin user with Recipe content permissions.
    $this->admin_user = $this->drupalCreateUser(['create recipe content', 'edit any recipe content', 'administer site configuration', 'view ingredient']);
    $this->drupalLogin($this->admin_user);
  }

}
