<?php

namespace Drupal\et_search;

use Drupal\Component\Utility\Html;
use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * MakeCollapsible pre-render callback.
 */
class MakeCollapsible implements TrustedCallbackInterface {

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['preRender'];
  }

  /**
   * Pre-render callback to make a block collapsible.
   */
  public static function preRender($elements) {
    $accordion = [
      '#type' => 'rdg_accordion',
      '#exclusive' => FALSE,
      '#sections' => [],
    ];
    foreach ($elements['content']['#facets'] as $facet_info) {
      /** @var \Drupal\facets\Entity\Facet */
      $facet = $facet_info['content'][0]['#facet'] ?? FALSE;
      if ($facet) {
        $active_facets = [];
        foreach ($facet->getActiveItems() as $active_item) {
          if (!empty($facet->getResultsKeyedByRawValue()[$active_item])) {
            $active_facets[] = [
              '#type' => 'html_tag',
              '#tag' => 'span',
              '#value' => $facet->getResultsKeyedByRawValue()[$active_item]->getDisplayValue(),
            ];
          }
        }
        $accordion['#sections'][] = [
          'id' => 'facet-' . $facet->id(),
          'title' => [
            'facet_name' => [
              '#type' => 'html_tag',
              '#tag' => 'span',
              '#attributes' => ['class' => ['facet-name']],
              '#value' => $facet->getName(),
            ],
            'active_facets' => [
              '#type' => 'container',
              '#attributes' => ['class' => ['active-facets']],
              'items' => $active_facets,
            ],
          ],
          'content' => $facet_info['content'],
          'attributes' => $facet_info['attributes'],
        ];
      }
    }

    $elements['content'] = $accordion;

    return $elements;
  }

}
