<?php

namespace Drupal\et_checkout\Plugin\Commerce\CheckoutPane;

use Drupal\commerce\AjaxFormTrait;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Psr\Container\ContainerInterface;

/**
 * Shipping method and address pane.
 *
 * @CommerceCheckoutPane(
 *   id = "et_checkout_shipping",
 *   label = @Translation("Shipping"),
 * )
 */
class ShippingPane extends CheckoutPaneBase implements ContainerFactoryPluginInterface {

  use AjaxFormTrait;

  /**
   * The entity type bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfo
   */
  protected $entityTypeBundleInfo;

  /**
   * The inline form manager.
   *
   * @var \Drupal\commerce\InlineFormManager
   */
  protected $inlineFormManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, CheckoutFlowInterface $checkout_flow = NULL) {
    $plugin = parent::create($container, $configuration, $plugin_id, $plugin_definition, $checkout_flow);
    $plugin->entityTypeBundleInfo = $container->get('entity_type.bundle.info');
    $plugin->inlineFormManager = $container->get('plugin.manager.commerce_inline_form');

    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $pane_form['header'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => $this->t('Shipping'),
    ];

    /** @var \Drupal\commerce\Plugin\Commerce\InlineForm\EntityInlineFormInterface $inline_form */
    $inline_form = $this->inlineFormManager->createInstance('customer_profile', [
      'profile_scope' => 'shipping',
      'address_book_uid' => $this->order->getCustomerId(),
      'copy_on_save' => TRUE,
    ], $this->getShippingProfile());

    // Prepare the form for ajax.
    // Not using Html::getUniqueId() on the wrapper ID to avoid #2675688.
    $pane_form['#wrapper_id'] = 'shipping-information-wrapper';
    $pane_form['#prefix'] = '<div id="' . $pane_form['#wrapper_id'] . '">';
    $pane_form['#suffix'] = '</div>';

    $pane_form['shipping_profile'] = [
      '#parents' => array_merge($pane_form['#parents'], ['shipping_profile']),
      '#inline_form' => $inline_form,
    ];
    $pane_form['shipping_profile'] = $inline_form->buildInlineForm($pane_form['shipping_profile'], $form_state);

    $class = get_class($this);
    // Ensure selecting a different address refreshes the entire form.
    if (isset($pane_form['shipping_profile']['select_address'])) {
      $pane_form['shipping_profile']['select_address']['#ajax'] = [
        'callback' => [$class, 'ajaxRefreshForm'],
        'element' => $pane_form['#parents'],
      ];
      // Selecting a different address should trigger a recalculation.
      $pane_form['shipping_profile']['select_address']['#recalculate'] = TRUE;
    }

    $form_display = EntityFormDisplay::collectRenderDisplay($this->order, 'shipping');
    $form_display->buildForm($this->order, $pane_form, $form_state);
    $pane_form['field_shipping_method']['widget']['#default_value'] = 'Standard';

    return $pane_form;
  }

  /**
   * {@inheritdoc}
   */
  public function validatePaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    $form_display = EntityFormDisplay::collectRenderDisplay($this->order, 'shipping');
    $form_display->extractFormValues($this->order, $pane_form, $form_state);
    $form_display->validateFormValues($this->order, $pane_form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    $form_display = EntityFormDisplay::collectRenderDisplay($this->order, 'shipping');
    $form_display->extractFormValues($this->order, $pane_form, $form_state);

    /** @var \Drupal\commerce\Plugin\Commerce\InlineForm\EntityInlineFormInterface $inline_form */
    $inline_form = $pane_form['shipping_profile']['#inline_form'];
    /** @var \Drupal\profile\Entity\ProfileInterface $profile */
    $profile = $inline_form->getEntity();

    $this->order->field_shipping_address->target_id = $profile->id();
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneSummary() {
    $render = [];

    $view_builder = $this->entityTypeManager->getViewBuilder('profile');
    $render['address'] = $view_builder->view($this->order->field_shipping_address->entity);

    return $render;
  }

  /**
   * Gets the shipping profile.
   *
   * @return \Drupal\profile\Entity\ProfileInterface
   *   The shipping profile.
   */
  protected function getShippingProfile() {
    $shipping_profile = $this->order->field_shipping_address->entity;
    if (!$shipping_profile) {
      $profile_type_id = 'customer';
      // Check whether the order type has another profile type ID specified.
      $order_type_id = $this->order->bundle();
      $order_bundle_info = $this->entityTypeBundleInfo->getBundleInfo('commerce_order');
      if (!empty($order_bundle_info[$order_type_id]['shipping_profile_type'])) {
        $profile_type_id = $order_bundle_info[$order_type_id]['shipping_profile_type'];
      }

      $shipping_profile = $this->entityTypeManager->getStorage('profile')->create([
        'type' => $profile_type_id,
        'uid' => 0,
      ]);
    }

    return $shipping_profile;
  }

}
