<?php

namespace Drupal\ajaxcart;

/**
 * Modifications to add to cart forms.
 */
interface AjaxCartInterface {

  /**
   * Alter an add to cart form.
   */
  public function alterForm(array &$form) : void;

}
