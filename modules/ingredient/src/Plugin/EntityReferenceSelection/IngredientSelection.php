<?php

/**
 * @file
 * Contains \Drupal\ingredient\Plugin\EntityReferenceSelection\NodeSelection.
 */

namespace Drupal\ingredient\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides specific access control for the ingredient entity type.
 *
 * @EntityReferenceSelection(
 *   id = "default:ingredient",
 *   label = @Translation("Ingredient selection"),
 *   entity_types = {"ingredient"},
 *   group = "default",
 *   weight = 1
 * )
 */
class IngredientSelection extends DefaultSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $query = parent::buildEntityQuery($match, $match_operator);
    return $query;
  }

}
