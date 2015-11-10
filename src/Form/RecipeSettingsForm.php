<?php

/**
 * @file
 * Contains \Drupal\recipe\Form\RecipeSettingsForm.
 */

namespace Drupal\recipe\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure recipe settings for this site.
 */
class RecipeSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'recipe_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['recipe.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('recipe.settings');

    // System of measurement section
    $form['system_of_measurement'] = array(
      '#type' => 'fieldset',
      '#title' => t('System of measurement'),
    );
    $form['system_of_measurement']['recipe_preferred_system_of_measure'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Preferred system of measure'),
      '#default_value' => $config->get('recipe_preferred_system_of_measure'),
      '#options' => array($this->t('U.S. customary units'), $this->t('SI/Metric')),
      '#description' => $this->t('Which system of measure should be preferred where it is ambiguous.'),
      '#required' => TRUE,
    );
    $form['system_of_measurement']['recipe_preferred_system_of_measure_limit'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Limit UI to the preferred system of measure'),
      '#default_value' => $config->get('recipe_preferred_system_of_measure_limit'),
      '#return_value' => 1,
      '#description' => $this->t('Limit unit selectbox to only preferred system of measure.  Does not affect import routines.'),
    );

    // Summary Section
    $form['recipe_summary'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Recipe summary'),
      '#description' => $this->t('The recipe summary contains the yield, source, and prep time values.')
    );
    $form['recipe_summary']['recipe_summary_location'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Recipe summary location'),
      '#return_value' => 1,
      '#default_value' => $config->get('recipe_summary_location'),
      '#options' => array($this->t('Node content'), $this->t('Block'), $this->t('Hidden')),
      '#description' => $this->t('Where to show the recipe summary information.'),
      '#required' => TRUE
    );
    $form['recipe_summary']['recipe_summary_title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Recipe summary title'),
      '#default_value' => $this->t($config->get('recipe_summary_title')),
      '#size' => 35,
      '#maxlength' => 255,
      '#description' => $this->t('The title shown above the recipe summary.'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('recipe.settings')
      ->set('recipe_preferred_system_of_measure', $values['recipe_preferred_system_of_measure'])
      ->set('recipe_preferred_system_of_measure_limit', $values['recipe_preferred_system_of_measure_limit'])
      ->set('recipe_summary_location', $values['recipe_summary_location'])
      ->set('recipe_summary_title', $values['recipe_summary_title'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}
