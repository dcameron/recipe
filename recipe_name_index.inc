<?php

/**
 * @file
 * recipe_name_index.inc - This is an include file containing most all of the recipe name index page functionality.
 */

function recipe_name_index_page() {
  drupal_add_css(drupal_get_path('module', 'recipe') . '/recipe_name_index.css', 'module');

  $node_list = recipe_get_alpha_nodes();
  $alpha_list = recipe_get_node_alpha_list($node_list);
  return theme('recipe_name_index_page', array('alpha_list' => $alpha_list, 'node_list' => $node_list));
}


function theme_recipe_name_index_page($variables) {

  $alpha_list = $variables['alpha_list'];
  $node_list = $variables['node_list'];

  // Render the alpha pager.
  $output = '<div class="recipe_name_alpha_list">';
  $list = array();
  foreach (range('a', 'z') as $letter ) {
    if (isset($alpha_list[$letter])) {
      $list[] = l(strtoupper($letter), 'recipe/byname', array('fragment' => 'alpha_' . $letter));
    }
    else {
      $list[] = $letter;
    }

  }

  if (isset($alpha_list['0-9'])) {
    $list[] = l('[0-9]', 'recipe/byname', array('fragment' => 'alpha_0-9'));
  }
  else {
    $list[] = '[0-9]';
  }



  $output .= implode(" - ", $list);
  $output .=  '</div>';

  $output .= '<div class="recipe_name_list">';

  // Render the node_list next.
  if ($node_list != NULL) {
    if ( count($node_list) > 0 ) {
      $output1 = '';
      $last_alpha = '-';
      foreach ($node_list as $node) {
        if ( $node['letter'] != $last_alpha ) {
          if ( $last_alpha != '-' ) {
            $output1 .= "</p></fieldset>";
          }
          $output1 .= '<fieldset><legend><a name="alpha_' . $node['letter'] . '">' . strtoupper($node['letter']) . '</a></legend><p>';
          $last_alpha = $node['letter'];
        }
        $output1 .= l($node['title'], 'node/' . $node['nid']) . '<br/>';
      }
      $output1 .= "</p></fieldset>";
      $output .= $output1;
    }
    else {
      $output .= theme('recipe_box', array('content' => t('No matching recipes')));
    }
  }

  $output .= "</div>";

  drupal_set_title(t('Find by recipe name'));
  return $output;
}


/**
 * Get recipes that have these ingredients.
 *
 * @return
 *   A database query result suitable for use the node_title_list.
 */
function recipe_get_alpha_nodes() {
  $select = db_select('node', 'n');
  $select->addField('n', 'nid');
  $select->addField('n', 'title');
  $select->addField('n', 'sticky');
  $select->condition('n.type', 'recipe');
  $select->condition('n.status', 1);
  $select->orderBy('n.title', 'ASC');
  $select->orderBy('n.sticky', 'DESC');
  return $select->execute()->fetchAll(PDO::FETCH_ASSOC);
}


function recipe_get_node_alpha_list(&$node_list = NULL) {
  $alpha_list = array();
  foreach ($node_list as &$n) {
    $letter = '';
    if ( preg_match('/([A-Za-z0-9])/', $n['title'], $matches) ) {
      $letter = strtolower($matches[1]);
    }
    else {
      $letter = strtolower(drupal_substr($n['title'], 0, 1));
    }

    if ( is_numeric($letter) ) {
      $letter = '0-9';
    }

    $n['letter'] = $letter;
    $alpha_list[$letter] = 1;
  }

  usort($node_list, 'node_list_cmp');
  return $alpha_list;
}


function node_list_cmp($a, $b) {
  // Sort first by letter.
  if ( $a['letter'] !=  $b['letter'] ) {
    return ($a['letter'] < $b['letter']) ? -1 : 1;
  }

  // Next by title
  if ( $a['title'] !=  $b['title'] ) {
    return ($a['title'] < $b['title']) ? -1 : 1;
  }

  // They are the same.
  return 0;
}
