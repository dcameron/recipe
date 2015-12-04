<?php

/**
 * @file
 * Contains \Drupal\ingredient\Plugin\Field\FieldType\IngredientItem.
 */

namespace Drupal\ingredient\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\ingredient\IngredientUnitTrait;

/**
 * Plugin implementation of the 'ingredient' field type.
 *
 * @FieldType(
 *   id = "ingredient",
 *   label = @Translation("Ingredient"),
 *   description = @Translation("This field stores the ID of an ingredient as an integer value."),
 *   category = @Translation("Reference"),
 *   default_widget = "ingredient_autocomplete",
 *   default_formatter = "ingredient_default",
 *   list_class = "\Drupal\ingredient\Plugin\Field\FieldType\IngredientFieldItemList",
 * )
 */
class IngredientItem extends EntityReferenceItem {

  use IngredientUnitTrait;

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'target_type' => 'ingredient_ingredient',
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'default_unit' => '',
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'target_id' => array(
          'description' => 'The ID of the ingredient entity.',
          'type' => 'int',
          'unsigned' => TRUE,
        ),
        'quantity' => array(
          'type' => 'float',
          'not null' => FALSE,
        ),
        'unit_key' => array(
          'description' => 'Untranslated unit key from the units array.',
          'type' => 'varchar',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ),
        'note' => array(
          'description' => 'Ingredient processing or notes related to recipe.',
          'type' => 'varchar',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ),
      ),
      'indexes' => array(
        'target_id' => array('target_id'),
      ),
      'foreign keys' => array(
        'target_id' => array(
          'table' => 'ingredient',
          'columns' => array('target_id' => 'id'),
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['quantity'] = DataDefinition::create('')
      ->setLabel(t('Quantity'));

    $properties['unit_key'] = DataDefinition::create('string')
      ->setLabel(t('Unit key'));

    $properties['note'] = DataDefinition::create('string')
      ->setLabel(t('Note'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   *
   * @todo: Migrate the default_unit setting to the defaultValuesForm().
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = [];

    // Create the unit options.
    $units = $this->getConfiguredUnits();
    $units = $this->sortUnitsByName($units);

    $element['default_unit'] = [
      '#type' => 'select',
      '#title' => t('Default unit type for ingredients'),
      '#default_value' => $this->getSetting('default_unit'),
      '#options' => $this->createUnitSelectOptions($units),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $settings = $field_definition->getSettings();

    // Get the ingredient unit keys.
    $unit_keys = array_keys($this->getConfiguredUnits());
    $random_unit_key = mt_rand(0, count($unit_keys) - 1);

    // Generate an ingredient entity.
    $ingredient = entity_create('ingredient_ingredient', ['name' => $random->name(10, TRUE)]);
    $values = [
      'target_id' => $ingredient->id(),
      'quantity' => mt_rand(1, 5),
      'unit_key' => $unit_keys[$random_unit_key],
      'note' => $random->word(15),
    ];
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public static function getPreconfiguredOptions() {
    return [];
  }

}
