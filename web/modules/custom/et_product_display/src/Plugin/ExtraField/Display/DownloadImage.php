<?php

namespace Drupal\et_product_display\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayBase;

/**
 * Disclaimer related to ordering products.
 *
 * @ExtraFieldDisplay(
 *   id = "et_product_display_download_image",
 *   label = @Translation("Download Image"),
 *   description = @Translation("Download Image button."),
 *   bundles = {
 *     "commerce_product.*",
 *   },
 *   weight = 0,
 *   visible = false
 * )
 */
class DownloadImage extends ExtraFieldPlusDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    $element = [];

    if (!empty($entity->field_product_image->first())) {
      $element = [
        '#type' => 'link',
        '#title' => [
          '#markup' => '<i class="icon-download1"></i><span>' . $this->t('Download') . '</span>',
        ],
        '#url' => $entity->field_product_image->first()->url(),
        '#attributes' => [
          'class' => ['shareit', 'shareit-download'],
          'target' => '_blank',
        ],
      ];
    }

    return $element;
  }

}
