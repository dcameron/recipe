<?php

namespace Drupal\Tests\ingredient\Kernel\Migrate\d7;

use Drupal\Tests\migrate_drupal\Kernel\d7\MigrateDrupal7TestBase;

/**
 * Tests migration of Recipe's ingredient variables to configuration.
 *
 * @group recipe
 */
class MigrateIngredientSettingsTest extends MigrateDrupal7TestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['ingredient'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(static::$modules);
    $this->executeMigrations(['d7_ingredient_settings']);
  }

  /**
   * Tests migration of ingredient variables to configuration.
   */
  public function testMigration() {
    $config = \Drupal::config('ingredient.settings')->get();
    $this->assertSame(1, $config['ingredient_name_normalize']);
  }

}
