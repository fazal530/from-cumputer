<?php

namespace Drupal\cloudinary;

use Cloudinary\Tag\ImageTag;

/**
 * Service defining interaction with Cloudinary API.
 */
interface CloudinaryInterface {

  /**
   * Determine whether a given public ID exists.
   */
  public function imageExists(string $public_id) : bool;

  /**
   * Fetch a Cloudinary image tag for a given public ID.
   */
  public function imageTag(string $public_id, string $format) : ImageTag;

  /**
   * Fetch the list of named transformations.
   */
  public function namedTransformations() : array;

  /**
   * Create a render array for an upload element.
   */
  public function uploader(string $field_id) : array;

}
