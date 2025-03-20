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
 *   id = "cloudinary_image_fallback",
 *   module = "cloudinary",
 *   label = @Translation("Rendered image fallback"),
 *   field_types = {
 *     "cloudinary_image"
 *   }
 * )
 */
class CloudinaryImageFallbackFormatter extends FormatterBase {

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
      'fallback_url' => '',
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

    $elements['fallback_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fallback image URL'),
      '#description' => $this->t('Use [transformation] in a cloudinary url to add to URL. Example: https://res.cloudinary.com/europatex/image/upload/[transformation]/placeholders/product.jpg'),
      '#default_value' => $this->getSetting('fallback_url'),
      '#required' => TRUE,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = $this->getSetting('transformation');
    $summary[] = substr($this->getFallbackUrl(), 0, 40) . '...';

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $transformation = $this->getSetting('transformation');

    if (!$items->isEmpty()) {
      foreach ($items as $delta => $item) {
        $image_tag = $this->cloudinaryService->imageTag($item->public_id, 'jpg');

        if (!empty($transformation)) {
          $image_tag->addTransformation($transformation);
        }

        $elements[$delta] = [
          '#type' => 'markup',
          '#markup' => $image_tag,
        ];
      }
    }
    else {

      $elements = [
        '#type' => 'markup',
        '#markup' => '<img class="placeholder-image" src="' . $this->getFallbackUrl() . '" alt="">',
      ];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function getFallbackUrl() {
    $fallback_url = $this->getSetting('fallback_url') ? $this->getSetting('fallback_url') : '';
    $transformation = $this->getSetting('transformation');

    return str_replace('[transformation]', $transformation, $fallback_url);
  }

}
