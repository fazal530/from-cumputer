<?php

namespace Drupal\et_product_display;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Build the custom breadcrumb for a product.
 */
class ProductBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructor.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager = NULL) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    return $route_match->getRouteName() == 'entity.commerce_product.canonical';
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
    $links = [];

    $links[] = Link::createFromRoute($this->t('Home'), '<front>');

    $links[] = Link::createFromRoute($this->t('Products'), 'view.products.page_1');

    /** @var \Drupal\commerce_product\Entity\Product */
    $product = $route_match->getParameter('commerce_product');
    $type = $this->entityTypeManager->getStorage('commerce_product_type')->load($product->bundle());
    $links[] = Link::createFromRoute($type->label(), 'view.products.page_1', ['facets_query' => 'type/' . $type->id()]);

    $breadcrumb->addCacheContexts(['url.path']);

    return $breadcrumb->setLinks($links);
  }

}
