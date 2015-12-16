<?php

/*
 * @file
 * Contains \Drupal\recipe\RecipeBreadcrumbBuilder
 */

namespace Drupal\recipe;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;

class RecipeBreadcrumbBuilder implements BreadcrumbBuilderInterface {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    $node = $route_match->getParameter('node');
    $match = $node instanceof NodeInterface && ($node->getType() == 'recipe');
    return $match;
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();

    $links[] = Link::createFromRoute($this->t('Home'), '<front>');
    $links[] = Link::createFromRoute($this->t('Recipes'), 'view.recipe_name_index.page_1');
    $breadcrumb->setLinks($links);
    $breadcrumb->addCacheContexts(['route']);
    return $breadcrumb;
  }
}
