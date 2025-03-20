<?php

namespace Drupal\et_checkout\Plugin\views\field;

use Drupal\commerce_cart\Plugin\views\field\EditQuantity;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form element for editing the order item quantity.
 *
 * @ViewsField("commerce_order_item_edit_quantity_minimum")
 */
class EditQuantityWithMinimum extends EditQuantity {

  /**
   * {@inheritDoc}
   */
  public function viewsForm(array &$form, FormStateInterface $form_state) {
    parent::viewsForm($form, $form_state);

    foreach ($this->view->result as $row_index => $row) {
      /** @var \Drupal\commerce_order\Entity\OrderItemInterface $order_item */
      $order_item = $this->getEntity($row);
      $product = $order_item->getPurchasedEntity()->getProduct();
      $minimum = $product->field_minimum_order->value;
      $widget =& $form[$this->options['id']][$row_index];
      $widget['#min'] = $minimum;
      if ($widget['#default_value'] < $minimum) {
        $widget['#default_value'] = $minimum;
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  public function viewsFormValidate(array &$form, FormStateInterface $form_state) {
    parent::viewsFormValidate($form, $form_state);

    $quantities = $form_state->getValue($this->options['id'], []);

    foreach ($this->view->result as $row_index => $row) {
      /** @var \Drupal\commerce_order\Entity\OrderItemInterface $order_item */
      $order_item = $this->getEntity($row);
      $product = $order_item->getPurchasedEntity()->getProduct();
      $minimum = $product->field_minimum_order->value;
      $widget =& $form[$this->options['id']][$row_index];
      $quantity = $quantities[$row_index];

      if ($quantity < $minimum) {
        $form_state->setError($widget, $this->t('The minimum order quantity for this product is %min.', [
          '%min' => $minimum,
        ]));
      }
    }

  }

}
