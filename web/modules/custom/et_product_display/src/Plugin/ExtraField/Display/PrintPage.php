<?php

namespace Drupal\et_product_display\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayBase;

/**
 * Disclaimer related to ordering products.
 *
 * @ExtraFieldDisplay(
 *   id = "et_product_display_print_page",
 *   label = @Translation("Print"),
 *   description = @Translation("Print button."),
 *   bundles = {
 *     "commerce_product.*",
 *   },
 *   weight = 0,
 *   visible = false
 * )
 */
class PrintPage extends ExtraFieldPlusDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    $element = [
      '#type' => 'html_tag',
      '#tag' => 'button',
      '#value' => '<i class="icon-printer"></i><span>' . $this->t('Print') . '</span>',
      '#attributes' => [
        'class' => ['shareit', 'shareit-print'],
        'onclick' => 'window.print();',
      ],
    ];

    return $element;
  }

}
