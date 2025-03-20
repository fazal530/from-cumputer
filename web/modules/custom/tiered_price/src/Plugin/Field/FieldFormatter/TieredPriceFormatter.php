<?php

namespace Drupal\tiered_price\Plugin\Field\FieldFormatter;

use Drupal\commerce_price\Plugin\Field\FieldFormatter\PriceDefaultFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\taxonomy\Entity\Term;

/**
 * Plugin implementation of the 'tiered_price_default' formatter.
 *
 * @FieldFormatter(
 *   id = "tiered_price_default",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "tiered_price"
 *   }
 * )
 */
class TieredPriceFormatter extends PriceDefaultFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $options = $this->getFormattingOptions();
    $elements = [];
    foreach ($items as $delta => $item) {
      $tier = Term::load($item->tier_id);
      $elements[$delta] = [
        '#markup' => $tier->name->value . ': ' . $this->currencyFormatter->format($item->number, $item->currency_code, $options),
        '#cache' => [
          'contexts' => [
            'languages:' . LanguageInterface::TYPE_INTERFACE,
            'country',
          ],
        ],
      ];
    }

    return $elements;
  }

}
