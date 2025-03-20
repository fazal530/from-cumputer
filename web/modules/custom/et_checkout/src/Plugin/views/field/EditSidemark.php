<?php

namespace Drupal\et_checkout\Plugin\views\field;

use Drupal\commerce_cart\CartManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\Plugin\views\field\UncacheableFieldHandlerTrait;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a form element for editing the sidemark.
 *
 * @ViewsField("commerce_order_item_edit_sidemark")
 */
class EditSidemark extends FieldPluginBase {

  use UncacheableFieldHandlerTrait;

  /**
   * The cart manager.
   *
   * @var \Drupal\commerce_cart\CartManagerInterface
   */
  protected CartManagerInterface $cartManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a new EditSidemark object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\commerce_cart\CartManagerInterface $cart_manager
   *   The cart manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CartManagerInterface $cart_manager, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->cartManager = $cart_manager;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('commerce_cart.cart_manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function clickSortable() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(ResultRow $row, $field = NULL) {
    return '<!--form-item-' . $this->options['id'] . '--' . $row->index . '-->';
  }

  /**
   * Form constructor for the views form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function viewsForm(array &$form, FormStateInterface $form_state) {
    // Make sure we do not accidentally cache this form.
    $form['#cache']['max-age'] = 0;
    // The view is empty, abort.
    if (empty($this->view->result)) {
      unset($form['actions']);
      return;
    }

    $form[$this->options['id']]['#tree'] = TRUE;
    foreach ($this->view->result as $row_index => $row) {
      /** @var \Drupal\commerce_order\Entity\OrderItemInterface $order_item */
      $order_item = $this->getEntity($row);

      if (!$order_item->isLocked()) {
        $form[$this->options['id']][$row_index] = [
          '#type' => 'textfield',
          '#title' => $this->t('Sidemark'),
          '#title_display' => 'invisible',
          '#default_value' => $order_item->field_sidemark->value,
          '#attributes' => [
            'placeholder' => 'Add sidemark',
            'class' => [
              'sidemark-edit-input',
            ],
          ],
        ];
      }
      else {
        $form[$this->options['id']][$row_index] = [
          '#type' => 'item',
          '#plain_text' => $order_item->field_sidemark->value,
        ];
      }
    }
  }

  /**
   * Submit handler for the views form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function viewsFormSubmit(array &$form, FormStateInterface $form_state) {
    $order_storage = $this->entityTypeManager->getStorage('commerce_order');
    /** @var \Drupal\commerce_order\Entity\OrderInterface $cart */
    $cart = $order_storage->load($this->view->argument['order_id']->getValue());
    $sidemarks = $form_state->getValue($this->options['id'], []);
    $save_cart = FALSE;
    foreach ($sidemarks as $row_index => $sidemark) {
      /** @var \Drupal\commerce_order\Entity\OrderItemInterface $order_item */
      $order_item = $this->getEntity($this->view->result[$row_index]);
      if ($order_item->field_sidemark->value == $sidemark) {
        // The sidemark hasn't changed.
        continue;
      }

      $order_item->field_sidemark->value = $sidemark;
      $this->cartManager->updateOrderItem($cart, $order_item, FALSE);
      $save_cart = TRUE;
    }

    if ($save_cart) {
      $cart->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing.
  }

}
