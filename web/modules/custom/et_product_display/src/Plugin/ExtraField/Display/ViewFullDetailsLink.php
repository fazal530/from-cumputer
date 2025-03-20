<?php

namespace Drupal\et_product_display\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayBase;

/**
 * Disclaimer related to ordering products.
 *
 * @ExtraFieldDisplay(
 *   id = "et_product_display_view_full_details",
 *   label = @Translation("View Full Details"),
 *   description = @Translation("Link to view full page."),
 *   bundles = {
 *     "commerce_product.*",
 *   },
 *   weight = 0,
 *   visible = false
 * )
 */
class ViewFullDetailsLink extends ExtraFieldPlusDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    $element = [];

    $element['container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['more-link'],
      ],
      'link' => [
        '#type' => 'link',
        '#title' => $this->t('View Full Details'),
        '#url' => $entity->toUrl(),
      ],
    ];

    return $element;
  }

}
