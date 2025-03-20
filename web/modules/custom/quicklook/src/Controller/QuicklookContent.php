<?php

namespace Drupal\quicklook\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller routines for Quicklook routes.
 */
class QuicklookContent extends ControllerBase {

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected RendererInterface $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $controller = parent::create($container);

    $controller->renderer = $container->get('renderer');

    return $controller;
  }

  /**
   * Return the Quicklook display for the given entity.
   */
  public function content(string $entity_type, int $entity_id) {
    $entity = $this->entityTypeManager()->getStorage($entity_type)->load($entity_id);
    $view_builder = $this->entityTypeManager()->getViewBuilder($entity_type);
    $view = $view_builder->view($entity, 'quicklook');

    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand('#quicklook > .content', $view));

    return $response;
  }

}
