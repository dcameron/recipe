<?php

/**
 * @file
 * Contains \Drupal\recipe\Form\RecipeSettingsForm.
 */

namespace Drupal\recipe\Form;

use Drupal\Core\Cache\Cache;
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
    $form['#tree'] = TRUE;

    // Create form elements for configuring the Total time pseudo-field.
    $form['total_time'] = [
      '#type' => 'fieldset',
      '#title' => t('Total time pseudo-field'),
    ];
    $form['total_time']['label'] = [
      '#type' => 'textfield',
      '#title' => t('Label'),
      '#default_value' => $config->get('total_time.label'),
      '#size' => 20,
    ];
    $form['total_time']['label_display'] = [
      '#type' => 'select',
      '#title' => t('Label display'),
      '#options' => [
        'above' => t('Above'),
        'inline' => t('Inline'),
        'hidden' => '- ' . t('Hidden') . ' -',
        'visually_hidden' => '- ' . t('Visually Hidden') . ' -',
      ],
      '#default_value' => $config->get('total_time.label_display')
    ];

    // Create form elements for configuring the Yield pseudo-field.
    $form['yield'] = [
      '#type' => 'fieldset',
      '#title' => t('Yield pseudo-field'),
    ];
    $form['yield']['label'] = [
      '#type' => 'textfield',
      '#title' => t('Label'),
      '#default_value' => $config->get('yield.label'),
      '#size' => 20,
    ];
    $form['yield']['label_display'] = [
      '#type' => 'select',
      '#title' => t('Label display'),
      '#options' => [
        'above' => t('Above'),
        'inline' => t('Inline'),
        'hidden' => '- ' . t('Hidden') . ' -',
        'visually_hidden' => '- ' . t('Visually Hidden') . ' -',
      ],
      '#default_value' => $config->get('yield.label_display')
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('recipe.settings')
      ->set('total_time.label', $values['total_time']['label'])
      ->set('total_time.label_display', $values['total_time']['label_display'])
      ->set('yield.label', $values['yield']['label'])
      ->set('yield.label_display', $values['yield']['label_display'])
      ->save();

    // Invalidate the node cache so the changes will appear in node displays.
    Cache::invalidateTags(['node_view']);

    parent::submitForm($form, $form_state);
  }

}
