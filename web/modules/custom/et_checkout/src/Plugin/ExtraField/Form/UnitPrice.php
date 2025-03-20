<?php

namespace Drupal\et_checkout\Plugin\ExtraField\Form;

use Drupal\commerce\Context;
use Drupal\commerce_price\Calculator;
use Drupal\commerce_price\Resolver\ChainPriceResolverInterface;
use Drupal\commerce_store\SelectStoreTrait;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\extra_field\Plugin\ExtraFieldFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Display the unit price (for use on add to cart).
 *
 * @ExtraFieldForm(
 *   id = "et_checkout_unit_price",
 *   label = @Translation("Unit Price of Variation"),
 *   description = @Translation("Display the unit price (for use on add to cart)."),
 *   bundles = {
 *     "commerce_order_item.*"
 *   },
 *   visible = false
 * )
 */
class UnitPrice extends ExtraFieldFormBase implements ContainerFactoryPluginInterface {

  use SelectStoreTrait;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected AccountInterface $currentUser;

  /**
   * The chain base price resolver.
   *
   * @var \Drupal\commerce_price\Resolver\ChainPriceResolverInterface
   */
  protected ChainPriceResolverInterface $chainPriceResolver;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $plugin = new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );

    $plugin->currentUser = $container->get('current_user');
    $plugin->chainPriceResolver = $container->get('commerce_price.chain_price_resolver');

    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(array &$form, FormStateInterface $form_state) {
    $element = [
      '#cache' => [
        'contexts' => ['user.permissions'],
      ],
    ];
    $account = \Drupal::currentUser();
    if ($account->hasPermission('access checkout')) {
      /** @var \Drupal\commerce_order\Entity\OrderItemInterface */
      $order_item = $this->entity;

      /** @var \Drupal\commerce_product\Entity\ProductVariationInterface */
      $purchased_entity = $order_item->getPurchasedEntity();
      $store = $this->selectStore($purchased_entity);
      $context = new Context($this->currentUser, $store);
      $resolved_price = $this->chainPriceResolver->resolve($purchased_entity, $order_item->getQuantity(), $context);

      if ($resolved_price) {
        $element['price'] = [
          '#type' => 'container',
          '#markup' => '<p>$' . Calculator::trim($resolved_price->getNumber()) . '/yd</p>',
        ];
      }
    }

    return $element;
  }

}
