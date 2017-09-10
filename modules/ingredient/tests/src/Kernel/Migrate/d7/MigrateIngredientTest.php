<?php

namespace Drupal\Tests\ingredient\Kernel;

use Drupal\Core\Database\Database;
use Drupal\ingredient\Entity\Ingredient;
use Drupal\ingredient\IngredientInterface;
use Drupal\Tests\migrate_drupal\Kernel\d7\MigrateDrupal7TestBase;

/**
 * Ingredients migration.
 *
 * @group recipe
 */
class MigrateIngredientTest extends MigrateDrupal7TestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'ingredient',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('ingredient');
    $this->executeMigrations([
      'd7_ingredient',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  protected function getFixtureFilePath() {
    return __DIR__ . '/../../../fixtures/ingredient713.php';
  }

  /**
   * Asserts various aspects of an ingredient.
   *
   * @param string $id
   *   The ingredient ID.
   * @param string $label
   *   The ingredient name.
   */
  protected function assertEntity($id, $label) {
    /** @var \Drupal\ingredient\IngredientInterface $ingredient */
    $ingredient = Ingredient::load($id);
    $this->assertTrue($ingredient instanceof IngredientInterface);
    $this->assertSame($label, $ingredient->label());
  }

  /**
   * Tests the Drupal 7 ingredient to Drupal 8 migration.
   */
  public function testUser() {
    $ingredients = Database::getConnection('default', 'migrate')
      ->select('recipe_ingredient', 'i')
      ->fields('i')
      ->execute()
      ->fetchAll();

    foreach ($ingredients as $source) {
      $this->assertEntity($source->id, $source->name);
    }
  }

}
