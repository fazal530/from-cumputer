<?php

namespace Drupal\cloudinary\Plugin\Field\FieldWidget;

use Drupal\cloudinary\CloudinaryInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'cloudinary_image_upload' widget.
 *
 * @FieldWidget(
 *   id = "cloudinary_image_upload",
 *   module = "cloudinary",
 *   label = @Translation("Cloudinary Upload"),
 *   field_types = {
 *     "cloudinary_image"
 *   }
 * )
 */
class CloudinaryImageUpload extends WidgetBase {

  /**
   * The cloudinary service.
   *
   * @var \Drupal\cloudinary\CloudinaryInterface
   */
  protected CloudinaryInterface $cloudinaryService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $plugin = parent::create($container, $configuration, $plugin_id, $plugin_definition);

    $plugin->cloudinaryService = $container->get('cloudinary');

    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = $items[$delta]->public_id ?? '';

    $id = Html::getUniqueId('cloudinary-upload');
    $hidden = [
      '#type' => 'hidden',
      '#default_value' => $value,
      '#attributes' => [
        'id' => $id,
      ],
    ];

    if (empty($value)) {
      $preview_tag = $this->cloudinaryService->imageTag('missing', 'png');
    }
    else {
      $preview_tag = $this->cloudinaryService->imageTag($value, 'jpg');
    }
    $preview_tag->addTransformation('t_media_lib_thumb');
    $preview_tag->addClass('cloudinary-preview');
    $preview = [
      '#type' => 'markup',
      '#markup' => $preview_tag,
    ];

    return [
      'label' => $element + [
        '#type' => 'label',
      ],
      'public_id' => $hidden,
      'uploader' => $this->cloudinaryService->uploader($id),
      'preview' => $preview,
    ];
  }

}
