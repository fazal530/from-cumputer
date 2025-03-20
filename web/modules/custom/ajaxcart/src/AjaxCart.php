<?php

namespace Drupal\ajaxcart;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * {@inheritDoc}
 */
class AjaxCart implements AjaxCartInterface {

  use StringTranslationTrait;

  /**
   * {@inheritDoc}
   */
  public function alterForm(array &$form) : void {
    $id = Html::getUniqueId('add-to-cart-messages');
    $form['actions']['messages'] = [
      '#type' => 'container',
      '#id' => $id,
      '#weight' => 52,
    ];
    $form['actions']['submit']['#ajax'] = [
      'callback' => [$this::class, 'addToCartCallback'],
      'disable-refocus' => TRUE,
      'wrapper' => $id,
      'method' => 'html',
      'effect' => 'fade',
      'progress' => [
        'type' => 'throbber',
        'message' => $this->t('Adding to Cart'),
      ],
    ];
  }

  /**
   * Callback for adding items to cart.
   *
   * Process the addition, and fetch the messages.
   *
   * @return array
   *   Renderable array (result messages).
   */
  public static function addToCartCallback(array &$form, FormStateInterface $form_state) {
    return [
      '#type' => 'status_messages',
    ];
  }

}
