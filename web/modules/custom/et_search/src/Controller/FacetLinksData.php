<?php

namespace Drupal\et_search\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\facets\FacetManager\DefaultFacetManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller routines for AJAX routes.
 */
class FacetLinksData extends ControllerBase {

  /**
   * The facet manager.
   *
   * @var \Drupal\facets\FacetManager\DefaultFacetManager
   */
  protected DefaultFacetManager $facetsManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $controller = parent::create($container);

    $controller->facetsManager = $container->get('facets.manager');

    return $controller;
  }

  /**
   * Returns all the facets for certain given values.
   *
   * @return array
   *   Form API array.
   *
   * @ingroup ajax_example
   */
  public function data(Request $request, string $facets_to_include) {
    $render = [];

    $facetsource_id = 'search_api:views_page__products__page_1';
    $facets = $this->facetsManager->getFacetsByFacetSourceId($facetsource_id);
    $this->facetsManager->updateResults($facetsource_id);

    $processed_facets = [];
    foreach ($facets as $facet) {
      if (!empty($facet->getActiveItems())) {
        continue;
      }
      if (!in_array($facet->id(), explode(',', $facets_to_include))) {
        continue;
      }

      $facet->setWidget('array_relative');
      $processed_facet = $this->facetsManager->build($facet);
      // @todo Cleaner way of finding item two levels deep with unknown keys?
      $value_data = [];
      foreach ($processed_facet as $level1) {
        foreach ($level1 as $level2) {
          $value_data = $level2;
        }
      }
      $values = [];
      foreach ($value_data as $value_datum) {
        $values[] = [
          'url' => $value_datum['url'] ?? '',
          'title' => $value_datum['values']['value'] ?? '',
        ];
      }
      $processed_facets[] = [
        'title' => $facet->getName(),
        'values' => $values,
      ];
    }

    $render = [
      '#theme' => 'et_search_facet_links',
      '#facets' => $processed_facets,
    ];

    $response = new AjaxResponse();
    $response->addCommand(new AppendCommand('#' . $request->query->get('id'), $render));

    return $response;
  }

}
