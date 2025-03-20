<?php

namespace Drupal\tiered_price\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'tiered_price_default' widget.
 *
 * @FieldWidget(
 *   id = "tiered_price_default",
 *   label = @Translation("Price"),
 *   field_types = {
 *     "tiered_price"
 *   },
 *   multiple_values = TRUE
 * )
 */
class TieredPriceWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $termStorage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

    $existing_prices = [];
    foreach ($items as $item) {
      $existing_prices[$item->tier_id] = [
        'number' => $item->number,
        'currency_code' => $item->currency_code,
      ];
    }

    foreach ($termStorage->loadTree('pricing_level', 0, 1) as $tier) {
      $element[$tier->tid] = [
        '#title' => $tier->name,
        '#type' => 'commerce_price',
        '#allow_negative' => $this->getFieldSetting('allow_negative'),
        '#available_currencies' => array_filter($this->getFieldSetting('available_currencies')),
        '#default_value' => $existing_prices[$tier->tid] ?? NULL,
      ];

    }


    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $new_values = [];
    foreach ($values as $tid => $price) {
      if (is_numeric($tid)) {
        $new_values[] = [
          'tier_id' => $tid,
          'number' => $price['number'],
          'currency_code' => $price['currency_code'],
        ];
      }
    }
    return $new_values;
  }

}
