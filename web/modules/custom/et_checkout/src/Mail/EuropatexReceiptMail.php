<?php

namespace Drupal\et_checkout\Mail;

use Drupal\commerce\MailHandlerInterface;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\Mail\OrderReceiptMail;
use Drupal\commerce_order\OrderTotalSummaryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Utility\Token;

class EuropatexReceiptMail extends OrderReceiptMail {

  /**
   * {@inheritdoc}
   */
  public function send(OrderInterface $order, $to = NULL, $bcc = NULL) {
    $to = $to ?? $order->getEmail();
    if (!$to) {
      // The email should not be empty.
      return FALSE;
    }

    /** @var \Drupal\commerce_order\Entity\OrderTypeInterface $order_type */
    $order_type = $this->orderTypeStorage->load($order->bundle());
    $subject = $this->token->replace($order_type->getReceiptSubject(), [
      'commerce_order' => $order,
    ]);
    $subject = $subject ?: $this->t('Order #@number confirmed', ['@number' => $order->getOrderNumber()]);
    $body = [
      '#theme' => 'commerce_order_receipt',
      '#order_entity' => $order,
      '#totals' => $this->orderTotalSummary->buildTotals($order),
    ];
    if ($billing_profile = $order->getBillingProfile()) {
      $body['#billing_information'] = $this->profileViewBuilder->view($billing_profile);
    }

    $shipping_profile = $order->field_shipping_address->entity;
    $body['#shipping_information'] = $this->profileViewBuilder->view($shipping_profile);

    $params = [
      'id' => 'order_receipt',
      'from' => $order->getStore()->getEmailFromHeader(),
      'bcc' => $bcc,
      'order' => $order,
    ];
    $customer = $order->getCustomer();
    if ($customer->isAuthenticated()) {
      $params['langcode'] = $customer->getPreferredLangcode();
    }

    return $this->mailHandler->sendMail($to, $subject, $body, $params);
  }

}
