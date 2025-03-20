<?php

namespace Drupal\et_search\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a route name condition.
 *
 * @Condition(
 *   id = "et_display_route_name",
 *   label = @Translation("Route name")
 * )
 */
class RouteName extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The CurrentRouteMatch object.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected CurrentRouteMatch $routeMatch;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );

    $instance->routeMatch = $container->get('current_route_match');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['route_name'] = [
      '#title' => $this->t('Route name'),
      '#type' => 'textfield',
      '#default_value' => $this->configuration['route_name'],
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['route_name'] = $form_state->getValue('route_name');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    if (empty($this->configuration['route_name']) && !$this->isNegated()) {
      return TRUE;
    }

    if ($this->routeMatch->getRouteName() == $this->configuration['route_name']) {
      return !$this->isNegated();
    }
    else {
      return $this->isNegated();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    if (empty($this->configuration['negate'])) {
      return $this->t('Route name is @route_name', [
        'Route name' => $this->pluginDefinition['label'],
        '@route_name' => $this->configuration['route_name'],
      ]);
    }
    else {
      return $this->t('Route name is not @route_name', [
        '@route_name_type' => $this->pluginDefinition['label'],
        '@route_name' => $this->configuration['route_name'],
      ]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'route_name' => '',
    ] + parent::defaultConfiguration();
  }

}
