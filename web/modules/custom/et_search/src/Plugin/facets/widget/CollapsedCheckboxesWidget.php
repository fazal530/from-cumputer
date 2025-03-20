<?php

namespace Drupal\et_search\Plugin\facets\widget;

use Drupal\facets\Plugin\facets\widget\CheckboxWidget;
use Drupal\facets\Plugin\facets\widget\LinksWidget;
use Drupal\facets\Result\ResultInterface;

/**
 * The links widget.
 *
 * @FacetsWidget(
 *   id = "et_search_checkboxes",
 *   label = @Translation("Collapsed checkboxes"),
 *   description = @Translation("Checkboxes, hidden in a collapsed element"),
 * )
 */
class CollapsedCheckboxesWidget extends CheckboxWidget {

  /**
   * {@inheritdoc}
   */
  protected function appendWidgetLibrary(array &$build) {
    parent::appendWidgetLibrary($build);
    // $build['#attributes']['class'][] = 'js-facets-collapsed-checkboxes';
    // $build['#attached']['library'][] = 'et_search/collapsed_checkboxes';
  }

}
