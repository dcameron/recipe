<?php

/**
 * @file
 * Contains \Drupal\ingredient\Form\IngredientSettingsForm.
 */

namespace Drupal\ingredient\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure ingredient settings for this site.
 */
class IngredientSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ingredient_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ingredient.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ingredient.settings');

    $form['ingredient_name_normalize'] = [
      '#type' => 'radios',
      '#title' => t('Ingredient name normalization'),
      '#default_value' => $config->get('ingredient_name_normalize'),
      '#options' => [t('Leave as entered'), t('Convert to lowercase')],
      '#description' => t('If enabled, the names of <em>new</em> ingredients will be converted to lowercase when they are entered. The names of registered trademarks, any ingredient name containing the &reg; symbol, will be excluded from normalization.'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('ingredient.settings')
      ->set('ingredient_name_normalize', $values['ingredient_name_normalize'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}
