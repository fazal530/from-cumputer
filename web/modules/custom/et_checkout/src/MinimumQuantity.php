<?php

namespace Drupal\et_checkout;

use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;

/**
 * {@inheritDoc}
 */
class MinimumQuantity implements MinimumQuantityInterface {

  /**
   * {@inheritDoc}
   */
  public function alterForm(array &$form, FormState $form_state) : void {
    $product = $form_state->get('product');
    if (!empty($product->field_minimum_order->value)) {
      $form['#validate'][] = [$this::class, 'addToCartCallback'];
      $widget =& $form['quantity']['widget'][0]['value'];
      $widget['#min'] = $product->field_minimum_order->value;
      if ($widget['#default_value'] < $product->field_minimum_order->value) {
        $widget['#default_value'] = $product->field_minimum_order->value;
      }
    }
  }

  /**
   * Validate the order quantity.
   */
  public static function addToCartCallback(array &$form, FormStateInterface $form_state) {
    // Get add to cart quantity.
    $values = $form_state->getValues();
    if (isset($values['quantity'])) {
      $quantity = $values['quantity'][0]['value'];
    }
    else {
      $quantity = 1;
    }

    // Load the product variation.
    if (isset($values['purchased_entity'][0]['variation'])) {
      $variation_id = $values['purchased_entity'][0]['variation'];
      /** @var \Drupal\commerce\PurchasableEntityInterface $purchased_entity */
      $purchased_entity = ProductVariation::load($variation_id);
    }
    else {
      $purchased_entity = ProductVariation::load($values['purchased_entity'][0]['target_id']);
    }

    $product = $purchased_entity->getProduct();

    if (empty($product->field_minimum_order->value)) {
      return;
    }
    if ($product->field_minimum_order->value <= $quantity) {
      return;
    }

    $form_state->setError($form, t('The minimum order quantity for this product is %min.', [
      '%min' => $product->field_minimum_order->value,
    ]));
  }

}
