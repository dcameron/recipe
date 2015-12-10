<?php

/*
 * @file
 * Contains \Drupal\ingredient\Tests\IngredientTestTrait
 */

namespace Drupal\ingredient\Tests;

use Drupal\field\Entity\FieldConfig;

/**
 * Provides common helper methods for Ingredient field tests.
 */
trait IngredientTestTrait {

  /**
   * Sets up a node bundle for Ingredient field testing.
   */
  protected function ingredientCreateContentType() {
    $this->drupalCreateContentType(['type' => 'test_bundle']);
  }

  /**
   * Creates a new ingredient field.
   *
   * @param array $storage_settings
   *   A list of field storage settings that will be added to the defaults.
   * @param array $field_settings
   *   A list of instance settings that will be added to the instance defaults.
   * @param array $widget_settings
   *   A list of widget settings that will be added to the widget defaults.
   * @param array $display_settings
   *   A list of display settings that will be added to the display defaults.
   */
  protected function createIngredientField($storage_settings = [], $field_settings = [], $widget_settings = [], $display_settings = []) {
    $field_storage = entity_create('field_storage_config', array(
      'entity_type' => 'node',
      'field_name' => 'field_ingredient',
      'type' => 'ingredient',
      'settings' => $storage_settings,
      'cardinality' => !empty($storage_settings['cardinality']) ? $storage_settings['cardinality'] : 1,
    ));
    $field_storage->save();

    $this->attachIngredientField($field_settings, $widget_settings, $display_settings);
    return $field_storage;
  }

  /**
   * Attaches an ingredient field to an entity.
   *
   * @param array $field_settings
   *   A list of field settings that will be added to the defaults.
   * @param array $widget_settings
   *   A list of widget settings that will be added to the widget defaults.
   * @param array $display_settings
   *   A list of display settings that will be added to the display defaults.
   */
  protected function attachIngredientField($field_settings = [], $widget_settings = [], $display_settings = []) {
    $field = array(
      'field_name' => 'field_ingredient',
      'label' => $this->randomMachineName(16),
      'entity_type' => 'node',
      'bundle' => 'test_bundle',
      'required' => !empty($field_settings['required']),
      'settings' => $field_settings,
    );
    entity_create('field_config', $field)->save();

    $form_display = \Drupal::entityManager()->getStorage('entity_form_display')->load('node.test_bundle.default');
    $form_display->setComponent('field_ingredient', [
        'type' => 'ingredient_autocomplete',
        'settings' => $widget_settings,
      ])
      ->save();
    // Assign display settings.
    $view_display = \Drupal::entityManager()->getStorage('entity_view_display')->load('node.test_bundle.default');
    $view_display->setComponent('field_ingredient', [
        'label' => 'hidden',
        'type' => 'ingredient_default',
        'settings' => $display_settings,
      ])
      ->save();
  }

  /**
   * Updates an existing ingredient field with new settings.
   */
  function updateIngredientField($field_settings = [], $widget_settings = [], $display_settings = []) {
    $field = FieldConfig::loadByName('node', 'test_bundle', 'field_ingredient');
    $field->setSettings(array_merge($field->getSettings(), $field_settings));
    $field->save();

    $form_display = \Drupal::entityManager()->getStorage('entity_form_display')->load('node.test_bundle.default');
    $form_display->setComponent('field_ingredient', ['settings' => $widget_settings])
      ->save();

    $view_display = \Drupal::entityManager()->getStorage('entity_view_display')->load('node.test_bundle.default');
    $view_display->setComponent('field_ingredient', ['settings' => $display_settings])
      ->save();
  }

}
