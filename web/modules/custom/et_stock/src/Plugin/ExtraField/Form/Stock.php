<?php

namespace Drupal\et_stock\Plugin\ExtraField\Form;

use Drupal\commerce_price\Calculator;
use Drupal\Core\Form\FormStateInterface;
use Drupal\extra_field\Plugin\ExtraFieldFormBase;

/**
 * Display the current stock level (for use on add to cart).
 *
 * @ExtraFieldForm(
 *   id = "et_stock",
 *   label = @Translation("Current Stock"),
 *   description = @Translation("Display the current stock level (for use on add to cart)."),
 *   bundles = {
 *     "commerce_order_item.*"
 *   },
 *   visible = false
 * )
 */
class Stock extends ExtraFieldFormBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(array &$form, FormStateInterface $form_state) {
    $element = [
      '#cache' => [
      ],
    ];
    /** @var \Drupal\commerce_product\Entity\ProductVariationInterface */
    $variation = $this->entity->getPurchasedEntity();
    $stock = $variation->field_stock->value;

    $element['stock'] = [
      '#type' => 'container',
      '#plain_text' => $stock . ' yds in stock',
    ];

    return $element;
  }

}
