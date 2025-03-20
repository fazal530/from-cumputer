<?php

namespace Drupal\et_stock;

use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;

/**
 * {@inheritDoc}
 */
class OrderQuantity implements OrderQuantityInterface {

  /**
   * {@inheritDoc}
   */
  public function alterForm(array &$form, FormState $form_state) : void {
    $form['#validate'][] = [$this::class, 'addToCartCallback'];
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

    $stock = $purchased_entity->field_stock->value ?? 0;
    if ($stock < $quantity) {
      \Drupal::messenger()->addWarning(t('Quantities of @product are limited. If necessary, the product will be backordered.', [
        '@product' => $purchased_entity->getProduct()->title->value,
      ]));
    }
  }

}
