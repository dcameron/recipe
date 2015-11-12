<?php

/**
 * @file
 * Contains \Drupal\ingredient\Entity\Controller\IngredientListBuilder.
 */

namespace Drupal\ingredient\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Provides a list controller for ingredient.
 */
class IngredientListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Ingredient ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\ingredient\Entity\Ingredient */
    $row['id'] = $entity->id();
    $row['name'] = $entity->link();
    return $row + parent::buildRow($entity);
  }

}
