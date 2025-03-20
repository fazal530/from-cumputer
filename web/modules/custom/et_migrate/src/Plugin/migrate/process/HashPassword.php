<?php

namespace Drupal\et_migrate\Plugin\migrate\process;

use Drupal\Core\Password\PasswordInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Apply a hash to a plaintext password.
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "hash_password"
 * )
 */
class HashPassword extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The migrate lookup service.
   *
   * @var \Drupal\Core\Password\PasswordInterface
   */
  protected PasswordInterface $passwordService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );

    $instance->passwordService = $container->get('password');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    return $this->passwordService->hash($value);
  }

}
