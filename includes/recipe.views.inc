<?php

/**
 * @file
 * recipe.views.inc - Views2 support for Recipe.
 *
 * Views support for the recipe module includes recipe lists, and ingredient lists.
 *
 * Added by Scott Prive Dec 2009
 */


/* Recipe Schema: This will probably get out of date(it already is!), but once this works better let's remove this.

node.nid +--------------+     +------------------+     +-------------+
     ^   | recipe       |     | _node_ingredient |     | _ingredient |
     |   +--------------+     +------------------+     +-------------+
     +---| nid          |<--  | id               |   +-| id          |
         | source       |  +--| nid              |   | | name        |
         | yield        |     | unit_key         |<-+| | link        |
         | yield_unit   |     | weight           |     +-------------+
         | description  |     | note             |
         | instructions |     | quantity         |
         | notes        |     | ingredient_id    |
         | preptime     |     +------------------+
         | cooktime     |
         +--------------+

 TODO:
  1) Impliment a  pre_render_list based handler function, to get all recipe ingredients based on a recipe nid.
  The idea is to have that fetch the ingredients, so I avoid the duplicate fields.
  (Anything that's not an ingredient should appear once, but gets duplicated).
  views_handler_field_amazon_participant.inc contains an example solution.

*/

/**
 * Implements hook_views_data().
 */
function recipe_views_data() {
  /*
   * Recipe table section.
   */

  // Group
  $data['recipe']['table']['group'] = t('Recipe');

  /* Base
   * It doesn't really make sense to include the recipe table by itself.  It doesn't even include the recipe title.
   * Since we include the recipe table joined to node you get all of the recipe fields anyway.
   */

  // Joins
  $data['recipe']['table']['join']['node'] = array(
    'left_field'    => 'nid',
    'field'         => 'nid',
  );

  // Fields
  $data['recipe']['yield'] = array(
    'title' => t('Yield'),
    'help' => t("The number of servings for this recipe."),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  $data['recipe']['yield_unit'] = array(
    'title' => t('Yield unit'),
    'help' => t("The yield unit for this recipe."),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  /*
   * Ingredient table section.
   */

  // Group
  $data['recipe_ingredient']['table']['group'] = t('Recipe');

  // Fields
  $data['recipe_ingredient']['id'] = array(
    'title' => t('Ingredient ID'),
    'help' => t("An ingredient id."),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  $data['recipe_ingredient']['name'] = array(
    'title' => t('Ingredient Name'),
    'help' => t("An ingredient name."),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  return $data;
}
