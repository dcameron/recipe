<?php

/**
 * @file
 * Contains \Drupal\ingredient\IngredientViewsData.
 */

namespace Drupal\ingredient;

use Drupal\views\EntityViewsData;

/**
 * Provides the views data for the ingredient entity type.
 */
class IngredientViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['ingredient_field_data']['table']['base']['access query tag'] = 'ingredient_access';

    return $data;
  }

}
