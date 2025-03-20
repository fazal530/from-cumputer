<?php

namespace Drupal\et_search\Plugin\facets\widget;

use Drupal\facets\Plugin\facets\widget\ArrayWidget;
use Drupal\facets\FacetInterface;
use Drupal\facets\Result\ResultInterface;

/**
 * A simple widget class that returns a simple array of the facet results.
 *
 * @FacetsWidget(
 *   id = "array_relative",
 *   label = @Translation("Array with relative links"),
 *   description = @Translation("A widget that builds an array with results. This widget is not supposed to display any results, but it is needed for rest integration."),
 * )
 */
class ArrayRelativeWidget extends ArrayWidget {

  /**
   * Prepares the URL and values for the facet.
   *
   * @param \Drupal\facets\Result\ResultInterface $result
   *   A result item.
   *
   * @return array
   *   The results.
   */
  protected function prepare(ResultInterface $result) {
    return [
      'url' => $result->getUrl()->setAbsolute(FALSE)->setOption('query', [])->toString(),
      'raw_value' => $result->getRawValue(),
      'values' => $this->generateValues($result),
    ];
  }

}
