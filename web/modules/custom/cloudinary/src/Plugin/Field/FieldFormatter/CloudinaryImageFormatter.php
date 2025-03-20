<?php

namespace Drupal\cloudinary\Plugin\Field\FieldFormatter;

use Drupal\cloudinary\CloudinaryInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'cloudinary_image' formatter.
 *
 * @FieldFormatter(
 *   id = "cloudinary_image",
 *   module = "cloudinary",
 *   label = @Translation("Rendered image"),
 *   field_types = {
 *     "cloudinary_image"
 *   }
 * )
 */
class CloudinaryImageFormatter extends FormatterBase {

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
  public static function defaultSettings() {
    return [
      'transformation' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $options = $this->cloudinaryService->namedTransformations();
    $options = ['' => 'Original'] + array_combine($options, $options);

    $elements['transformation'] = [
      '#type' => 'select',
      '#title' => $this->t('Transformation'),
      '#options' => $options,
      '#default_value' => $this->getSetting('transformation'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = $this->getSetting('transformation');

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $image_tag = $this->cloudinaryService->imageTag($item->public_id, 'jpg');

      if (!empty($this->getSetting('transformation'))) {
        $image_tag->addTransformation($this->getSetting('transformation'));
      }

      $elements[$delta] = [
        '#type' => 'markup',
        '#markup' => $image_tag,
      ];
    }

    return $elements;
  }

}
