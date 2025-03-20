<?php

namespace Drupal\et_checkout;

use Drupal\Core\Form\FormState;

/**
 * Enforce minimum quantity when ordering.
 */
interface MinimumQuantityInterface {

  /**
   * Alter an add to cart form.
   */
  public function alterForm(array &$form, FormState $form_state) : void;

}
