<?php

namespace Drupal\cloudinary\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'cloudinary_public_id' widget.
 *
 * @FieldWidget(
 *   id = "cloudinary_public_id",
 *   module = "cloudinary",
 *   label = @Translation("Public ID"),
 *   field_types = {
 *     "cloudinary_image"
 *   }
 * )
 */
class PublicId extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = isset($items[$delta]->public_id) ?? '';
    $element += [
      '#type' => 'textfield',
      '#default_value' => $value,
    ];
    return ['public_id' => $element];
  }

}
