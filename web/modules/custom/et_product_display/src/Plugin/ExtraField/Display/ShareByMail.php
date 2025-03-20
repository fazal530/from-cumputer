<?php

namespace Drupal\et_product_display\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayBase;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;

/**
 * Disclaimer related to ordering products.
 *
 * @ExtraFieldDisplay(
 *   id = "et_product_display_share_by_mail",
 *   label = @Translation("Share by Mail"),
 *   description = @Translation("Share by Mail button."),
 *   bundles = {
 *     "commerce_product.*",
 *   },
 *   weight = 0,
 *   visible = false
 * )
 */
class ShareByMail extends ExtraFieldPlusDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    $element = [
      '#type' => 'link',
      '#title' => [
        '#markup' => '<i class="icon-envelope-o"></i><span>' . $this->t('Email') . '</span>',
      ],
      '#url' => Url::fromUserInput('/share-product', [
        'query' => [
          'product_ids' => $entity->id(),
        ],
      ]),
      '#attributes' => [
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode(['width' => '80%']),
        'class' => ['use-ajax', 'shareit', 'shareit-mail'],
      ],
    ];

    $element['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return $element;
  }

}
