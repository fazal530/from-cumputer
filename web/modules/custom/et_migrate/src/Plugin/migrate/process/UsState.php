<?php

namespace Drupal\et_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Look up a state code from its name.
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "us_state"
 * )
 */
class UsState extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $subdivision_repository = \Drupal::service('address.subdivision_repository');
    $states = array_flip($subdivision_repository->getList(['US']));
    if (!empty($states[$value])) {
      return $states[$value];
    }
    return $value;
  }

}
