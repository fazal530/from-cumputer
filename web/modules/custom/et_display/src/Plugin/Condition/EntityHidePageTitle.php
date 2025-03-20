<?php

namespace Drupal\et_display\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;

/**
 * Provides a 'Entity Hide Page Title' condition.
 *
 * @Condition(
 *   id = "et_display_hide_page_title",
 *   label = @Translation("Entity Hide Page Title")
 * )
 */
class EntityHidePageTitle extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The CurrentRouteMatch object.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $routeMatch;

  /**
   * Creates a new NotNodeType instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $route_match
   *   The route match.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    CurrentRouteMatch $route_match
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['negate']['#weight'] = 100;

    $form['enabled'] = [
      '#title' => $this->t('Hides page title on Commerce Product and Node detail pages'),
      '#type' => 'checkbox',
      '#default_value' => $this->configuration['enabled'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['negate'] = $form_state->getValue('negate');
    $this->configuration['enabled'] = $form_state->getValue('enabled');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    return $this->t('The node @status the ad block', [
      '@status' => $this->configuration['negate'] ? 'does not hide ' : 'hides',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    // Check if a setting has been set.
    if (!$this->configuration['enabled']) {
      return TRUE;
    }

    // Check commerce product page.
    $entity = $this->routeMatch->getParameter('commerce_product');
    if (is_object($entity)) {
      return FALSE;
    }

    // Check node page.
    $node = $this->routeMatch->getParameter('node');
    if (is_object($node)) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['enabled' => FALSE] + parent::defaultConfiguration();
  }

}
