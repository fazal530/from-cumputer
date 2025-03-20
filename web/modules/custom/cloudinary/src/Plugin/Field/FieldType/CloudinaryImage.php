<?php

namespace Drupal\cloudinary\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'cloudinary_image' field type.
 *
 * @FieldType(
 *   id = "cloudinary_image",
 *   label = @Translation("Cloudinary Image"),
 *   module = "cloudinary",
 *   description = @Translation("Image stored on Cloudinary service."),
 *   default_widget = "cloudinary_image_upload",
 *   default_formatter = "cloudinary_image"
 * )
 */
class CloudinaryImage extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'public_id' => [
          'type' => 'text',
          'not null' => TRUE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('public_id')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['public_id'] = DataDefinition::create('string')
      ->setLabel(t('Public ID'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function url($format = 'jpg', $transformation = NULL) {
    // DI is not available for field types.
    /** @var \Drupal\cloudinary\CloudinaryInterface $cloudinary_service **/
    $cloudinary_service = \Drupal::service('cloudinary');
    $image_tag = $cloudinary_service->imageTag($this->get('public_id')->getValue(), $format);

    if (!empty($transformation)) {
      $image_tag->addTransformation($transformation);
    }

    if (preg_match('%src="([^"]+)"%', $image_tag->__toString(), $matches)) {
      return Url::fromUri($matches[1]);
    }
  }

}
