<?php

namespace Drupal\et_stock;

use Drupal\Core\Form\FormState;

/**
 * Provide feedback on order quantity.
 */
interface OrderQuantityInterface {

  /**
   * Alter an add to cart form.
   */
  public function alterForm(array &$form, FormState $form_state) : void;

}
