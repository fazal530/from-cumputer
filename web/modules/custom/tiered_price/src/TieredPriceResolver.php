<?php

namespace Drupal\tiered_price;

use Drupal\commerce\Context;
use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_price\Price;
use Drupal\commerce_price\Resolver\PriceResolverInterface;

/**
 * Resolver for determining customer-specific prices.
 */
class TieredPriceResolver implements PriceResolverInterface {

  /**
   * {@inheritdoc}
   */
  public function resolve(PurchasableEntityInterface $entity, $quantity, Context $context) {
    $user = \Drupal::entityTypeManager()->getStorage('user')->load($context->getCustomer()->id());

    foreach ($entity->field_prices as $price) {
      if ($price->tier_id == $user->field_pricing_level->target_id) {
        return new Price($price->number, $price->currency_code);
      }
    }

    return NULL;
  }

}
