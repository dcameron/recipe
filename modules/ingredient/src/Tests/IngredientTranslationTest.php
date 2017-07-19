<?php

namespace Drupal\ingredient\Tests;

use Drupal\ingredient\Entity\Ingredient;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\node\Entity\Node;
use Drupal\simpletest\WebTestBase;

/**
 * Tests the translatability of Ingredient entities.
 *
 * @group recipe
 */
class IngredientTranslationTest extends WebTestBase {

  use IngredientTestTrait;

  /**
   * The langcode of the source language.
   *
   * @var string
   */
  protected $baseLangcode = 'en';

  /**
   * Target langcode for translation.
   *
   * @var string
   */
  protected $translateToLangcode = 'fr';
  /**
   * The node to check the translated value on.
   *
   * @var \Drupal\node\Entity\Node
   */
  protected $node;

  /**
   * The ingredient that should be translated.
   *
   * @var \Drupal\ingredient\Entity\Ingredient
   */
  protected $ingredient;

  /**
   * The ingredient in the source language.
   *
   * @var string
   */
  protected $baseIngredientName = 'OriginalIngredientName';

  /**
   * The translated value for the ingredient.
   *
   * @var string
   */
  protected $translatedIngredientName = 'TranslatedIngredientName';

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['content_translation', 'ingredient', 'node'];

  /**
   * A test user with administrative privileges.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $admin_user;

  public function setUp() {
    parent::setUp();
    $this->ingredientCreateContentType();
    $this->setUpLanguages();
    $this->enableTranslation();
    $this->setUpIngredient();
    $this->createIngredientField();
    $this->setUpNode();

    // Create and log in the admin user.
    $permissions = [
      'access content',
      'view ingredient',
    ];
    $this->admin_user = $this->drupalCreateUser($permissions);
    $this->drupalLogin($this->admin_user);
  }

  /**
   * Tests Ingredient translation.
   */
  public function testTranslatedIngredientDisplay() {
    $ingredient_path = 'ingredient/' . $this->ingredient->id();
    $ingredient_translation_path = $this->translateToLangcode . '/' . $ingredient_path;
    $node_path = 'node/' . $this->node->id();
    $node_translation_path = $this->translateToLangcode . '/' . $node_path;

    // Check the Ingredient entity display views for the names.
    $this->drupalGet($ingredient_path);
    $this->assertNoText($this->translatedIngredientName);
    $this->assertText($this->baseIngredientName);
    $this->drupalGet($ingredient_translation_path);
    $this->assertText($this->translatedIngredientName);
    $this->assertNoText($this->baseIngredientName);

    // Check the Node display views for the names.
    $this->drupalGet($node_path);
    $this->assertNoText($this->translatedIngredientName);
    $this->assertText($this->baseIngredientName);
    $this->drupalGet($node_translation_path);
    $this->assertText($this->translatedIngredientName);
    $this->assertNoText($this->baseIngredientName);
  }

  /**
   * Adds additional languages.
   */
  protected function setUpLanguages() {
    ConfigurableLanguage::createFromLangcode($this->translateToLangcode)->save();
    $this->rebuildContainer();
  }

  /**
   * Enables translations where it needed.
   */
  protected function enableTranslation() {
    // Enable translation for the current entity type and ensure the change is
    // picked up.
    \Drupal::service('content_translation.manager')->setEnabled('node', 'test_bundle', TRUE);
    \Drupal::service('content_translation.manager')->setEnabled('ingredient', 'ingredient', TRUE);
    drupal_static_reset();
    \Drupal::entityManager()->clearCachedDefinitions();
    \Drupal::service('router.builder')->rebuild();
    \Drupal::service('entity.definition_update_manager')->applyUpdates();
  }

  /**
   * Creates a test subject node, with translation.
   */
  protected function setUpNode() {
    $this->node = Node::create([
      'title' => $this->randomMachineName(),
      'type' => 'test_bundle',
      'field_ingredient' => [
        'quantity' => 1,
        'unit_key' => 'cup',
        'target_id' => $this->ingredient->id(),
        'note' => '',
      ],
      'langcode' => $this->baseLangcode,
    ]);
    $this->node->save();

    $this->node->addTranslation($this->translateToLangcode, $this->node->toArray());
    $this->node->save();
  }

  /**
   * Creates a test subject ingredient, with translation.
   */
  protected function setUpIngredient() {
    $this->ingredient = Ingredient::create(['name' => $this->baseIngredientName]);
    $this->ingredient->save();

    $this->ingredient->addTranslation($this->translateToLangcode, ['name' => $this->translatedIngredientName]);
    $this->ingredient->save();
  }

}
