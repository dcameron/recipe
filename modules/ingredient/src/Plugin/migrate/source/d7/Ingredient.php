<?php

namespace Drupal\ingredient\Plugin\migrate\source\d7;

use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Drupal 7 ingredient source from database.
 *
 * @MigrateSource(
 *   id = "d7_ingredient",
 *   source_module = "recipe"
 * )
 */
class Ingredient extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('recipe_ingredient', 'i')
      ->fields('i', ['id', 'name'])
      ->orderBy('i.id');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'id' => $this->t('Ingredient ID'),
      'name' => $this->t('Ingredient name'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return ['id' => ['type' => 'integer']];
  }

}
