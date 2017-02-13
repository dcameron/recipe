<?php

/**
 * @file
 * Contains Drupal\ingredient\Plugin\Field\FieldFormatter\IngredientFormatter.
 */

namespace Drupal\ingredient\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ingredient\IngredientUnitTrait;

/**
 * Plugin implementation of the 'ingredient_default' formatter.
 *
 * @FieldFormatter(
 *   id = "ingredient_default",
 *   module = "ingredient",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "ingredient"
 *   }
 * )
 */
class IngredientFormatter extends EntityReferenceFormatterBase {

  use IngredientUnitTrait;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'fraction_format' => t('{%d }%d&frasl;%d'),
      'unit_display' => 0,
      'link' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['fraction_format'] = array(
      '#type' => 'textfield',
      '#title' => t('Fractions display string'),
      '#default_value' => $this->getSetting('fraction_format'),
      '#size' => 35,
      '#maxlength' => 255,
      '#description' => t('How fractions should be displayed. Leave blank to display as decimals.<br />Each incidence of %d will be replaced by the whole number, the numerator, and the denominator in that order.<br />Anything between curly braces will not be displayed when the whole number is equal to 0.<br />Recommended settings are "{%d }%d&amp;frasl;%d" or "{%d }&lt;sup&gt;%d&lt;/sup&gt;/&lt;sub&gt;%d&lt;/sub&gt;"'),
    );
    $element['unit_display'] = array(
      '#type' => 'radios',
      '#title' => t('Ingredient unit display'),
      '#default_value' => $this->getSetting('unit_display'),
      '#options' => $this->getUnitDisplayOptions(),
      '#description' => t('Display ingredient units like Tbsp or Tablespoon.'),
      '#required' => TRUE,
    );
    $element['link'] = array(
      '#title' => t('Link name to the referenced ingredient'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('link'),
    );
    return $element;
  }

  /**
   * Returns an array of ingredient quantity unit display options.
   *
   * @return array
   *   The array of display options.
   */
  protected function getUnitDisplayOptions() {
    return [
      0 => t('Abbreviation'),
      1 => t('Full name'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary[] = t('Fractions display string: @fraction_format', array('@fraction_format' => $this->getSetting('fraction_format')));
    $unit_display_options = $this->getUnitDisplayOptions();
    $unit_display_text = $unit_display_options[$this->getSetting('unit_display')];
    $summary[] = t('Ingredient unit display: @unit_display_text', ['@unit_display_text' => $unit_display_text]);
    $link_display_text = $this->getSetting('link') ? t('Yes') : t('No');
    $summary[] = t('Link to ingredient: @link_display_text', ['@link_display_text' => $link_display_text]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $fraction_format = $this->getSetting('fraction_format');
    $output_as_link = $this->getSetting('link');
    $unit_list = $this->getConfiguredUnits();
    $elements = array();

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      // Sanitize the name and note.
      $name = Xss::filter($entity->label(), array());
      $note = Xss::filter($items[$delta]->note, array());

      // If the link should be displayed and the entity has a URI, display the
      // link.
      if ($output_as_link && !$entity->isNew()) {
        $url = $entity->toUrl();
        $name = \Drupal::l($name, $url);
      }

      if ($items[$delta]->quantity > 0) {
        $formatted_quantity = ingredient_quantity_from_decimal($items[$delta]->quantity, $fraction_format);
      }
      else {
        $formatted_quantity = '&nbsp;';
      }

      // Print the unit unless it has no abbreviation. Those units do not get
      // printed in any case.
      $unit_name = '';
      $unit_abbreviation = '';
      $unit = isset($unit_list[$items[$delta]->unit_key]) ? $unit_list[$items[$delta]->unit_key] : array();
      if (!empty($unit['abbreviation'])) {
        $unit_name = $items[$delta]->quantity > 1 ? $unit['plural'] : $unit['name'];
        $unit_abbreviation = $unit['abbreviation'];
      }

      $elements[$delta] = array(
        '#theme' => 'ingredient_formatter',
        '#name' => $name,
        '#quantity' => $formatted_quantity,
        '#unit_name' => $unit_name,
        '#unit_abbreviation' => $unit_abbreviation,
        '#unit_display' => $this->getSetting('unit_display'),
        '#note' => $note,
      );
    }
    return $elements;
  }

}
