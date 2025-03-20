<?php

namespace Drupal\et_product_display\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field_plus\Plugin\ExtraFieldPlusDisplayBase;

/**
 * Disclaimer related to ordering products.
 *
 * @ExtraFieldDisplay(
 *   id = "et_product_display_product_image_lightbox_trigger",
 *   label = @Translation("Product Image Lightbox Trigger"),
 *   description = @Translation("A link that opens the enlarged product image in a lightbox window"),
 *   bundles = {
 *     "commerce_product.*",
 *   },
 *   weight = 0,
 *   visible = false
 * )
 */
class ProductImageLightboxTrigger extends ExtraFieldPlusDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    $element = [];

    if (!empty($entity->field_product_image->first())) {
      $title = $entity->title->value;
      $rendered_field = $entity->field_product_image->first()->view([
        'type' => 'cloudinary_image',
        'label' => 'hidden',
        'settings' => [
          'transformation' => 't_europatex_fabric_detail',
          'fallback_url' => 'https://res.cloudinary.com/europatex/image/upload/[transformation]/placeholders/product.jpg',
        ],
      ]);

      $element['link'] = [
        '#type' => 'link',
        '#title' => $rendered_field,
        '#url' => $entity->field_product_image->first()->url(),
        '#options' => [
          'html' => TRUE,
          'title' => $title,
        ],
        '#attributes' => [
          'class' => ['lightbox'],
          'data-caption' => $title,
          'data-group' => 'fabric',
        ],
      ];

      $element['#attached']['library'][] = 'et_product_display/lightboxGallery';

    }
    else {
      $element = [
        '#type' => 'markup',
        '#markup' => '<img class="placeholder-image" src="https://res.cloudinary.com/europatex/image/upload/t_europatex_fabric_detail/placeholders/product.jpg" alt="">',
      ];
    }

    return $element;
  }

}
