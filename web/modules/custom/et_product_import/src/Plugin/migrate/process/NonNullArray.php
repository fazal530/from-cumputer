<?php

namespace Drupal\et_product_import\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Creates an array from a set of strings. Skips empty items.
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "non_null_array",
 *   handle_multiples = TRUE
 * )
 */
class NonNullArray extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (is_array($value)) {
      return array_filter($value, function ($x) {
        return !empty($x);
      });
    }
    else {
      throw new MigrateException(sprintf('%s is not an array', var_export($value, TRUE)));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    return TRUE;
  }

}
