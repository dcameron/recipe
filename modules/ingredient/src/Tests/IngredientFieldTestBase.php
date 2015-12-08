<?php

/*
 * @file
 * Contains \Drupal\ingredient\Tests\IngredientFieldTestBase
 */

namespace Drupal\ingredient\Tests;

use Drupal\field\Entity\FieldConfig;
use Drupal\ingredient\IngredientUnitTrait;
use Drupal\simpletest\WebTestBase;

/**
 * Provides setup and helper methods for Ingredient module tests.
 */
class IngredientFieldTestBase extends WebTestBase {

  use IngredientUnitTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('field_ui', 'ingredient', 'node');

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

    // Create a new content type for testing.
    $content_type = $this->drupalCreateContentType(array('type' => 'test_bundle'));

    // Create and log in the admin user.
    $this->admin_user = $this->drupalCreateUser(array('create test_bundle content', 'access content', 'administer node display', 'add ingredient', 'view ingredient', 'administer site configuration'));
    $this->drupalLogin($this->admin_user);

    // Populate the unit list.
    $this->unit_list = $this->getConfiguredUnits();
  }

  /**
   * Creates a new ingredient field.
   *
   * @param string $name
   *   The name of the new field (all lowercase), exclude the "field_" prefix.
   * @param string $entity_type
   *   The entity type.
   * @param string $bundle
   *   The bundle that this field will be added to.
   * @param array $storage_settings
   *   A list of field storage settings that will be added to the defaults.
   * @param array $field_settings
   *   A list of instance settings that will be added to the instance defaults.
   * @param array $widget_settings
   *   A list of widget settings that will be added to the widget defaults.
   * @param array $display_settings
   *   A list of display settings that will be added to the display defaults.
   */
  protected function createIngredientField($name, $entity_type, $bundle, $storage_settings = array(), $field_settings = array(), $widget_settings = array(), $display_settings = array()) {
    $field_storage = entity_create('field_storage_config', array(
      'entity_type' => $entity_type,
      'field_name' => $name,
      'type' => 'ingredient',
      'settings' => $storage_settings,
      'cardinality' => !empty($storage_settings['cardinality']) ? $storage_settings['cardinality'] : 1,
    ));
    $field_storage->save();

    $this->attachIngredientField($name, $entity_type, $bundle, $field_settings, $widget_settings, $display_settings);
    return $field_storage;
  }

  /**
   * Attaches an ingredient field to an entity.
   *
   * @param string $name
   *   The name of the new field (all lowercase), exclude the "field_" prefix.
   * @param string $entity_type
   *   The entity type this field will be added to.
   * @param string $bundle
   *   The bundle this field will be added to.
   * @param array $field_settings
   *   A list of field settings that will be added to the defaults.
   * @param array $widget_settings
   *   A list of widget settings that will be added to the widget defaults.
   * @param array $display_settings
   *   A list of display settings that will be added to the display defaults.
   */
  protected function attachIngredientField($name, $entity_type, $bundle, $field_settings = array(), $widget_settings = array(), $display_settings = array()) {
    $field = array(
      'field_name' => $name,
      'label' => $name,
      'entity_type' => $entity_type,
      'bundle' => $bundle,
      'required' => !empty($field_settings['required']),
      'settings' => $field_settings,
    );
    entity_create('field_config', $field)->save();

    $form_display = \Drupal::entityManager()->getStorage('entity_form_display')->load($entity_type . '.' . $bundle . '.default');
    $form_display->setComponent($name, array(
        'type' => 'ingredient_autocomplete',
        'settings' => $widget_settings,
      ))
      ->save();
    // Assign display settings.
    $view_display = \Drupal::entityManager()->getStorage('entity_view_display')->load($entity_type . '.' . $bundle . '.default');
    $view_display->setComponent($name, array(
        'label' => 'hidden',
        'type' => 'ingredient_default',
        'settings' => $display_settings,
      ))
      ->save();
  }

  /**
   * Updates an existing ingredient field with new settings.
   */
  function updateIngredientField($name, $entity_type, $bundle, $field_settings = array(), $widget_settings = array(), $display_settings = array()) {
    $field = FieldConfig::loadByName($entity_type, $bundle, $name);
    $field->setSettings(array_merge($field->getSettings(), $field_settings));
    $field->save();

    $form_display = \Drupal::entityManager()->getStorage('entity_form_display')->load($entity_type . '.' . $bundle . '.default');
    $form_display->setComponent($name, ['settings' => $widget_settings])
      ->save();

    $view_display = \Drupal::entityManager()->getStorage('entity_view_display')->load($entity_type . '.' . $bundle . '.default');
    $view_display->setComponent($name, ['settings' => $display_settings])
      ->save();
  }

}
