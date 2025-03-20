<?php

namespace Drupal\et_search\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\facets\FacetInterface;
use Drupal\facets\FacetManager\DefaultFacetManager;
use Drupal\facets\Utility\FacetsUrlGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'FacetLinks' block.
 *
 * @Block(
 *  id = "et_search_facet_links",
 *  admin_label = @Translation("Facet Links"),
 * )
 */
class FacetLinks extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The facet manager.
   *
   * @var \Drupal\facets\FacetManager\DefaultFacetManager
   */
  protected DefaultFacetManager $facetsManager;

  /**
   * The facet manager.
   *
   * @var \Drupal\facets\Utility\FacetsUrlGenerator
   */
  protected FacetsUrlGenerator $facetsUrlGenerator;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $plugin = new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );

    $plugin->facetsManager = $container->get('facets.manager');
    $plugin->facetsUrlGenerator = $container->get('facets.utility.url_generator');

    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'active_facets' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $existing_items = [];
    foreach ($this->configuration['active_facets'] as $facet => $values) {
      foreach ($values as $value) {
        $existing_items[] = [
          'facet' => $facet,
          'value' => $value,
        ];
      }
    }

    // Gather the number of items in the form already.
    $num_items = $form_state->get('num_items');
    // We have to ensure that there is at least one field.
    if ($num_items === NULL) {
      $num_items = max(1, count($existing_items));
      $form_state->set('num_items', $num_items);
    }

    $wrapper_id = Html::getUniqueId('facets-add-more-wrapper');

    $form['#tree'] = TRUE;
    $form['facets_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Active facets'),
      '#prefix' => '<div id="' . $wrapper_id . '">',
      '#suffix' => '</div>',
    ];

    for ($i = 0; $i < $num_items; $i++) {
      $form['facets_fieldset']['facets'][$i] = [
        'facet' => [
          '#type' => 'textfield',
          '#title' => $this->t('Facet'),
          '#default_value' => $existing_items[$i]['facet'] ?? '',
        ],
        'value' => [
          '#type' => 'textfield',
          '#title' => $this->t('Value'),
          '#default_value' => $existing_items[$i]['value'] ?? '',
        ],
      ];
    }

    $form['facets_fieldset']['actions'] = [
      '#type' => 'actions',
    ];
    $form['facets_fieldset']['actions']['add_item'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add more'),
      '#attributes' => ['class' => ['field-add-more-submit']],
      '#limit_validation_errors' => [],
      '#submit' => [[static::class, 'addOne']],
      '#ajax' => [
        'callback' => [static::class, 'addmoreCallback'],
        'wrapper' => $wrapper_id,
        'effect' => 'fade',
      ],
    ];

    $form['block_settings']['facets_to_include'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Facets to include'),
      '#default_value' => isset($this->configuration['facets_to_include']) ? $this->configuration['facets_to_include'] : [],
      '#options' => $this->getAvailableFacets(),
    ];

    return $form;
  }

  /**
   * Returns a list of available facets.
   *
   * @return array
   *   An array of enabled facets.
   */
  protected function getAvailableFacets() {
    $enabled_facets = $this->facetsManager->getEnabledFacets();
    uasort($enabled_facets, [$this, 'sortFacetsByWeight']);

    $available_facets = [];

    foreach ($enabled_facets as $facet) {
      /** @var \Drupal\facets\Entity\Facet $facet */
      $available_facets[$facet->id()] = $facet->getName();
    }

    return $available_facets;
  }

  /**
   * Sorts array of objects by object weight property.
   *
   * @param \Drupal\facets\FacetInterface $a
   *   A facet.
   * @param \Drupal\facets\FacetInterface $b
   *   A facet.
   *
   * @return int
   *   Sort value.
   */
  protected function sortFacetsByWeight(FacetInterface $a, FacetInterface $b) {
    $a_weight = $a->getWeight();
    $b_weight = $b->getWeight();

    if ($a_weight == $b_weight) {
      return 0;
    }

    return ($a_weight < $b_weight) ? -1 : 1;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $active_facets = [];
    $num_items = $form_state->get('num_items');
    for ($i = 0; $i < $num_items; $i++) {
      $facet = $form_state->getValue(['facets_fieldset', 'facets', $i, 'facet']);
      $value = $form_state->getValue(['facets_fieldset', 'facets', $i, 'value']);
      if (!empty($facet) && !empty($value)) {
        $active_facets[$facet][] = $value;
      }
    }
    $this->configuration['active_facets'] = $active_facets;
    $this->configuration['facets_to_include'] = $form_state->getValue([
      'block_settings',
      'facets_to_include',
    ]);
  }

  /**
   * Callback for Ajax buttons.
   */
  public static function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['settings']['facets_fieldset'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   */
  public static function addOne(array &$form, FormStateInterface $form_state) {
    $num_items = $form_state->get('num_items');
    $form_state->set('num_items', $num_items + 1);
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $render = [];
    $options = [];

    if (!empty($this->configuration['active_facets'])) {
      $search_url = $this->facetsUrlGenerator->getUrl($this->configuration['active_facets'], FALSE);
    }
    else {
      $search_url = Url::fromUserInput('/products');
    }

    $id = Html::getUniqueId('facet-links-content');
    $facets_query = $search_url->getRouteParameters()['facets_query'];
    $options['query']['id'] = $id;

    $render[] = [
      '#type' => 'link',
      '#weight' => 3,
      '#url' => $search_url,
      '#title' => $this->t('All @type', ['@type' => $this->configuration['label']]),
      '#attributes' => ['class' => ['all-link']],
    ];

    $render[] = [
      '#type' => 'container',
      '#weight' => 10,
      '#attributes' => [
        'id' => $id,
        'class' => ['facet-links-content'],
        'data-url' => Url::fromRoute('et_search.facet_links_data', [
          'facets_to_include' => implode(',', array_filter($this->configuration['facets_to_include'])),
          'facets_query' => empty($facets_query) ? 'all' : $facets_query,
        ], $options)->toString(),
      ],
      '#attached' => [
        'library' => ['et_search/facet_links', 'rdg_ui_elements/accordion'],
      ],
    ];

    return $render;
  }

}
