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

    // System of measurement section
    $form['system_of_measurement'] = array(
      '#type' => 'fieldset',
      '#title' => t('System of measurement'),
    );
    $form['system_of_measurement']['ingredient_preferred_system_of_measure'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Preferred system of measure'),
      '#default_value' => $config->get('ingredient_preferred_system_of_measure'),
      '#options' => array($this->t('U.S. customary units'), $this->t('SI/Metric')),
      '#description' => $this->t('Which system of measure should be preferred where it is ambiguous.'),
      '#required' => TRUE,
    );
    $form['system_of_measurement']['ingredient_preferred_system_of_measure_limit'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Limit UI to the preferred system of measure'),
      '#default_value' => $config->get('ingredient_preferred_system_of_measure_limit'),
      '#return_value' => 1,
      '#description' => $this->t('Limit unit selectbox to only preferred system of measure.  Does not affect import routines.'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('ingredient.settings')
      ->set('ingredient_preferred_system_of_measure', $values['ingredient_preferred_system_of_measure'])
      ->set('ingredient_preferred_system_of_measure_limit', $values['ingredient_preferred_system_of_measure_limit'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}
