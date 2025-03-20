<?php

namespace Drupal\et_checkout\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\Entity\EntityFormDisplay;

/**
 * Provides a custom order comments pane.
 *
 * @CommerceCheckoutPane(
 *   id = "et_checkout_order_comments",
 *   label = @Translation("Order Comments"),
 * )
 */
class OrderCommentsPane extends CheckoutPaneBase {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $form_display = EntityFormDisplay::collectRenderDisplay($this->order, 'checkout');
    $form_display->buildForm($this->order, $pane_form, $form_state);

    return $pane_form;
  }

  /**
   * {@inheritdoc}
   */
  public function validatePaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    $form_display = EntityFormDisplay::collectRenderDisplay($this->order, 'checkout');
    $form_display->extractFormValues($this->order, $pane_form, $form_state);
    $form_display->validateFormValues($this->order, $pane_form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    $form_display = EntityFormDisplay::collectRenderDisplay($this->order, 'checkout');
    $form_display->extractFormValues($this->order, $pane_form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneSummary() {
    if ($order_comment = $this->order->field_order_comments->value) {
      return [
        '#plain_text' => $order_comment,
      ];
    }
    return [];
  }

}
