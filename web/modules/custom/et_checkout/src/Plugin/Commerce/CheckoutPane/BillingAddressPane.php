<?php

namespace Drupal\et_checkout\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a custom order comments pane.
 *
 * @CommerceCheckoutPane(
 *   id = "et_checkout_billing_address",
 *   label = @Translation("Billing Address"),
 * )
 */
class BillingAddressPane extends CheckoutPaneBase {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $pane_form['header'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => $this->t('Billing Address'),
    ];

    $pane_form['address'] = $this->order->getCustomer()->field_billing_address->view();

    $pane_form['message'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('@message', ['@message' => $this->configuration['message']]),
    ];

    return $pane_form;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'message' => 'Please contact Customer Service if your billing information has changed.',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationSummary() {
    return $this->t('Message: @message', ['@message' => $this->configuration['message']]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Message'),
      '#default_value' => $this->configuration['message'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['message'] = $values['message'];
    }
  }

}
