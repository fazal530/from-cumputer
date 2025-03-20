<?php

namespace Drupal\cloudinary;

use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\ApiClient;
use Cloudinary\Api\Exception\NotFound;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Tag\ImageTag;
use Cloudinary\Tag\UploadTag;
use Drupal\Component\Utility\Html;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Service defining interaction with Cloudinary API.
 */
class Cloudinary implements CloudinaryInterface {

  use StringTranslationTrait;

  /**
   * The module configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected ImmutableConfig $moduleConfig;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->moduleConfig = $config_factory->get('cloudinary.settings');

    $cloud_config = Configuration::instance();
    $cloud_config->cloud->cloudName = $this->moduleConfig->get('cloudName');
    $cloud_config->cloud->apiKey = $this->moduleConfig->get('apiKey');
    $cloud_config->cloud->apiSecret = $this->moduleConfig->get('apiSecret');
    $cloud_config->url->secure = TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function imageExists(string $public_id) : bool {
    $api = new UploadApi();
    try {
      $api->explicit($public_id, [
        'type' => 'upload',
      ]);
    }
    catch (NotFound $e) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function imageTag(string $public_id, string $format) : ImageTag {
    return new ImageTag($public_id . '.' . $format);
  }

  /**
   * {@inheritdoc}
   */
  public function namedTransformations() : array {
    $api = new AdminApi();

    $transformation_names = [];

    foreach ($api->transformations(['named' => TRUE])['transformations'] as $transformation) {
      $transformation_names[] = $transformation['name'];
    }

    return $transformation_names;
  }

  /**
   * Create a render array for an upload element.
   */
  public function uploader(string $field_id) : array {
    $render = [];

    $render['button'] = [
      '#type' => 'html_tag',
      '#tag' => 'button',
      '#value' => $this->t('Upload new file'),
      '#attributes' => [
        'class' => ['cloudinary-button'],
        'data-field' => $field_id,
      ],
    ];

    $render['#attached']['library'][] = 'cloudinary/upload';
    $render['#attached']['drupalSettings']['cloudinary']['cloudName'] = $this->moduleConfig->get('cloudName');

    return $render;
  }

}
