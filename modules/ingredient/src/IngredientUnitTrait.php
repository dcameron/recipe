<?php

/**
 * @file
 * Contains Drupal\ingredient\IngredientUnitTrait.
 */

namespace Drupal\ingredient;

/**
 * Provides functions for accessing Ingredient unit configuration.
 */
trait IngredientUnitTrait {

  /**
   * Returns an array of units from configuration.
   *
   * @param array $sets_to_get
   *   An array of set id strings.
   *
   * @return array
   *   An array of units.
   */
  protected function getConfiguredUnits($sets_to_get = []) {
    $unit_sets = \Drupal::config('ingredient.units')->get('unit_sets');

    $units = [];
    foreach ($unit_sets as $set_id => $set) {
      // Verify that the set contains an array of units.
      if (empty($set['units']) || !is_array($set['units'])) {
        continue;
      }
      // Skip the set if it's not in the $sets_to_get.
      if (!empty($sets_to_get) && is_array($sets_to_get) && !in_array($set_id, $sets_to_get)) {
        continue;
      }

      $units = array_merge($units, $set['units']);
    }
    return $units;
  }

  /**
   * Sorts an array of units by the name element.
   *
   * @param array $units
   *   An array containing a 'name' element.
   *
   * @return array
   *   The sorted array of units.
   */
  protected function sortUnitsByName($units) {
    uasort($units, function ($a, $b) {
      return strcmp($a['name'], $b['name']);
    });
    return $units;
  }

  /**
   * Returns options for a unit select form element.
   *
   * @param array $units
   *   An array of units.
   *
   * @return array
   *   An array of unit key/value pairs for use as select form element options.
   */
  protected function createUnitSelectOptions($units = []) {
    // Put in a blank so non-matching units will not validate and save.
    $options = ['' => ''];

    foreach ($units as $unit_key => $unit) {
      $text = $unit['name'];
      if (!empty($unit['abbreviation'])) {
        $text .= ' (' . $unit['abbreviation'] . ')';
      }
      $options[$unit_key] = $text;
    }
    return $options;
  }

}
