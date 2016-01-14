<?php

/**
 * @file
 * Contains \Drupal\recipe\Plugin\views\display\Recipe.
 */

namespace Drupal\recipe\Plugin\views\display;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\CacheableResponse;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\display\PathPluginBase;
use Drupal\views\Plugin\views\display\ResponseDisplayPluginInterface;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * The plugin that handles a recipe format, such as RecipeML.
 *
 * @ingroup views_display_plugins
 *
 * @ViewsDisplay(
 *   id = "recipe",
 *   title = @Translation("Recipe"),
 *   help = @Translation("Display the view in a recipe format."),
 *   uses_route = TRUE,
 *   admin = @Translation("Recipe"),
 *   returns_response = TRUE
 * )
 */
class Recipe extends PathPluginBase implements ResponseDisplayPluginInterface {

  /**
   * Whether the display allows the use of AJAX or not.
   *
   * @var bool
   */
  protected $ajaxEnabled = FALSE;

  /**
   * Whether the display allows the use of a pager or not.
   *
   * @var bool
   */
  protected $usesPager = FALSE;

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return 'recipe';
  }

  /**
   * {@inheritdoc}
   */
  public static function buildResponse($view_id, $display_id, array $args = []) {
    $build = static::buildBasicRenderable($view_id, $display_id, $args);

    // Set up an empty response, so the style plugins can set the proper
    // Content-Type header.
    $response = new CacheableResponse('', 200);
    $build['#response'] = $response;

    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = \Drupal::service('renderer');

    $output = (string) $renderer->renderRoot($build);

    if (empty($output)) {
      throw new NotFoundHttpException();
    }

    $response->setContent($output);
    $cache_metadata = CacheableMetadata::createFromRenderArray($build);
    $response->addCacheableDependency($cache_metadata);

    return $response;
  }


  /**
   * {@inheritdoc}
   */
  public function execute() {
    parent::execute();

    return $this->view->render();
  }

  /**
   * {@inheritdoc}
   */
  public function preview() {
    $output = $this->view->render();

    if (!empty($this->view->live_preview)) {
      $output = array(
        '#prefix' => '<pre>',
        '#plain_text' => drupal_render_root($output),
        '#suffix' => '</pre>',
      );
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = $this->view->style_plugin->render($this->view->result);

    $this->applyDisplayCachablityMetadata($build);

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['displays'] = array('default' => array());

    // Overrides for standard stuff.
    $options['style']['contains']['type']['default'] = 'recipeml';
    $options['row'] = FALSE;
    $options['defaults']['default']['style'] = FALSE;
    $options['defaults']['default']['row'] = FALSE;

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function optionsSummary(&$categories, &$options) {
    parent::optionsSummary($categories, $options);

    // Since we're childing off the 'path' type, we'll still *call* our
    // category 'page' but let's override it so it says feed settings.
    $categories['page'] = array(
      'title' => $this->t('Recipe settings'),
      'column' => 'second',
      'build' => array(
        '#weight' => -10,
      ),
    );

    $displays = array_filter($this->getOption('displays'));
    if (count($displays) > 1) {
      $attach_to = $this->t('Multiple displays');
    }
    elseif (count($displays) == 1) {
      $display = array_shift($displays);
      $displays = $this->view->storage->get('display');
      if (!empty($displays[$display])) {
        $attach_to = $displays[$display]['display_title'];
      }
    }

    if (!isset($attach_to)) {
      $attach_to = $this->t('None');
    }

    $options['displays'] = array(
      'category' => 'page',
      'title' => $this->t('Attach to'),
      'value' => $attach_to,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    // It is very important to call the parent function here.
    parent::buildOptionsForm($form, $form_state);

    switch ($form_state->get('section')) {
      case 'displays':
        $form['#title'] .= $this->t('Attach to');
        $displays = array();
        foreach ($this->view->storage->get('display') as $display_id => $display) {
          // @todo The display plugin should have display_title and id as well.
          if ($this->view->displayHandlers->has($display_id) && $this->view->displayHandlers->get($display_id)->acceptAttachments()) {
            $displays[$display_id] = $display['display_title'];
          }
        }
        $form['displays'] = array(
          '#title' => $this->t('Displays'),
          '#type' => 'checkboxes',
          '#description' => $this->t('The format link will be available only to the selected displays.'),
          '#options' => array_map('\Drupal\Component\Utility\Html::escape', $displays),
          '#default_value' => $this->getOption('displays'),
        );
        break;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state) {
    parent::submitOptionsForm($form, $form_state);
    $section = $form_state->get('section');
    switch ($section) {
      case 'displays':
        $this->setOption($section, $form_state->getValue($section));
        break;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function attachTo(ViewExecutable $clone, $display_id, array &$build) {
    $displays = $this->getOption('displays');
    if (empty($displays[$display_id])) {
      return;
    }

    // Defer to the feed style; it may put in meta information, and/or
    // attach a feed icon.
    $clone->setArguments($this->view->args);
    $clone->setDisplay($this->display['id']);
    $clone->buildTitle();
    if ($plugin = $clone->display_handler->getPlugin('style')) {
      $plugin->attachTo($build, $display_id, $clone->getUrl(), $clone->getTitle());
    }

    // Clean up.
    $clone->destroy();
    unset($clone);
  }

  /**
   * {@inheritdoc}
   */
  public function usesLinkDisplay() {
    return FALSE;
  }

}