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
      ->set('recipe_summary_location', $values['recipe_summary_location'])
      ->set('recipe_summary_title', $values['recipe_summary_title'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}
