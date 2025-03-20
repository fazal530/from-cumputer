<?php

namespace Drupal\et_product_import\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Converts a filename string into a Cloudinary ID.
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "cloudinary_id"
 * )
 */
class CloudinaryId extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $base_name = preg_replace('%\.[a-z]+$%', '', $value);
    if (empty($base_name)) {
      throw new MigrateSkipProcessException();
    }
    $id = 'products/' . $base_name;

    $cloudinaryService = \Drupal::service('cloudinary');
    if ($cloudinaryService->imageExists($id)) {
      return $id;
    }

    throw new MigrateSkipProcessException('Image not found: ' . $id);
  }

}
