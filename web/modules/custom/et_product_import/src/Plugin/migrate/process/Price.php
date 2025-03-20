<?php

namespace Drupal\et_product_import\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateLookupInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Converts a price into a Commerce data structure.
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "price"
 * )
 */
class Price extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The migrate lookup service.
   *
   * @var \Drupal\migrate\MigrateLookupInterface
   */
  protected MigrateLookupInterface $migrateLookup;

  /**
   * Constructs a MigrationLookup object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\migrate\MigrateLookupInterface $migrate_lookup
   *   The migrate lookup service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, MigrateLookupInterface $migrate_lookup) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->migrateLookup = $migrate_lookup;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('migrate.lookup'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $number = preg_replace('%[^0-9.]%', '', $value);
    if (!is_numeric($number)) {
      throw new MigrateSkipRowException('No price found.');
    }
    if (!empty($this->configuration['term_lookup'])) {
      $id_array = $this->migrateLookup->lookup('pricing_level', [$this->configuration['term_lookup']]);
      $tier_id = array_pop(array_pop($id_array));
      return [
        'tier_id' => $tier_id,
        'number' => $number,
        'currency_code' => 'USD',
      ];
    }
    else {
      return [
        'number' => $number,
        'currency_code' => 'USD',
      ];
    }
  }

}
