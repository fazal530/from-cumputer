<?php

namespace Drupal\et_product_display\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayBase;

/**
 * Disclaimer related to ordering products.
 *
 * @ExtraFieldDisplay(
 *   id = "et_product_display_disclaimer",
 *   label = @Translation("Disclaimer"),
 *   description = @Translation("Disclaimer related to ordering products."),
 *   bundles = {
 *     "commerce_product.*",
 *   },
 *   weight = 0,
 *   visible = false
 * )
 */
class Disclaimer extends ExtraFieldPlusDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    $element = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ['product-disclaimer'],
      ],
      'disclaimer' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Items are not refundable. Actual dye lots may vary and photos may differ from actual fabric. Please request sample cutting if you need exact color match. All sale prices are for full bolts only.'),
      ],
    ];

    if (!empty($entity->field_minimum_order->value)) {
      $element['minimum'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Minimum Order Qty: %minimum.', [
          '%minimum' => $entity->field_minimum_order->value,
        ]),
      ];
    }

    return $element;
  }

}
