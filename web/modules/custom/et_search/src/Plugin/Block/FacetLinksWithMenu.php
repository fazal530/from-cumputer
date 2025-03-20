<?php

namespace Drupal\et_search\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;

/**
 * Provides a 'Facet Links with Menu Block' block.
 *
 * @Block(
 *  id = "et_search_facet_links_menu",
 *  admin_label = @Translation("Facet Links with Menu"),
 * )
 */
class FacetLinksWithMenu extends FacetLinks {

  /**
   * The menu tree service.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected MenuLinkTreeInterface $menuLinkTree;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'menu_name' => [],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $plugin = parent::create($container, $configuration, $plugin_id, $plugin_definition);

    $plugin->entityTypeManager = $container->get('entity_type.manager');
    $plugin->menuLinkTree = $container->get('menu.link_tree');

    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $form['menu_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Menu machine name'),
      '#default_value' => $this->configuration['menu_name'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);

    $this->configuration['menu_name'] = $form_state->getValue('menu_name');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $render = parent::build();
    $render['menu'] = [];
    if (!empty($this->configuration['menu_name'])) {
      $menu_name = $this->configuration['menu_name'];
      $menu = $this->entityTypeManager->getStorage('menu')->load($menu_name);
      if (empty($menu)) {
        return $render;
      }
      $menu_tree = $this->menuLinkTree;
      $parameters = $menu_tree->getCurrentRouteMenuTreeParameters($menu_name);
      $tree = $menu_tree->load($menu_name, $parameters);
      $manipulators = [
        ['callable' => 'menu.default_tree_manipulators:checkAccess'],
        ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
      ];
      $tree = $menu_tree->transform($tree, $manipulators);
      $tree_build = $menu_tree->build($tree);

      $render['menu_wrapper'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['featured-links'],
        ],
        '#weight' => 5,
        'inner_wrapper' => [
          '#type' => 'container',
          '#attributes' => [
            'class' => ['featured-links-inner'],
          ],
          'title' => [
            '#markup' => '<span> ' . $menu->label() . '</span>',
          ],
          'menu' => $tree_build,
        ],
      ];
    }
    return $render;
  }

}
