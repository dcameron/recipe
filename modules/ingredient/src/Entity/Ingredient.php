<?php
/**
 * @file
 * Contains \Drupal\ingredient\Entity\Ingredient.
 */

namespace Drupal\ingredient\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\ingredient\IngredientInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Ingredient entity.
 *
 * @todo Convert to a multilingual schema per the instructions at
 *   https://www.drupal.org/node/1722906.
 *
 * @ContentEntityType(
 *   id = "ingredient_ingredient",
 *   label = @Translation("Ingredient"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ingredient\Entity\Controller\IngredientListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ingredient\Form\IngredientForm",
 *       "edit" = "Drupal\ingredient\Form\IngredientForm",
 *       "delete" = "Drupal\ingredient\Form\IngredientDeleteForm",
 *     },
 *     "access" = "Drupal\ingredient\IngredientAccessControlHandler",
 *   },
 *   list_cache_contexts = { "user" },
 *   base_table = "ingredient",
 *   admin_permission = "administer ingredient",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/ingredient/{ingredient_ingredient}",
 *     "edit-form" = "/ingredient/{ingredient_ingredient}/edit",
 *     "delete-form" = "/ingredient/{ingredient_ingredient}/delete",
 *     "collection" = "/ingredient/list"
 *   },
 *   field_ui_base_route = "ingredient.ingredient_settings",
 * )
 */
class Ingredient extends ContentEntityBase implements IngredientInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Contact entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Contact entity.'))
      ->setReadOnly(TRUE);

    // Name field for the ingredient.
    // We set display options for the view as well as the form.
    // Users with correct privileges can change the view and edit configuration.
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Ingredient.'))
      ->setSettings([
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -6,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string',
        'weight' => -6,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of ContentEntityExample entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    $config = \Drupal::config('ingredient.settings');

    // Normalize the names of new ingredients to lowercase before saving.
    if ($this->isNew() && $config->get('ingredient_name_normalize')) {
      $name_field = $this->get('name');
      $values = $name_field->getValue();
      foreach ($values as $key => $value) {
        // Don't convert to lowercase if there is a &reg; (registered trademark
        // symbol).
        if (!strpos($value['value'], '®')) {
          $values[$key]['value'] = trim(strtolower($value['value']));
        }
      }
      $name_field->setValue($values, FALSE);
    }

    parent::preSave($storage);
  }

}
