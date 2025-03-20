<?php

namespace Drupal\et_stock\Commands;

use Drush\Commands\DrushCommands;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\et_stock\AWS;

/**
 * Drush commands for performing stock imports.
 */
class ImportCommands extends DrushCommands {
  use LoggerChannelTrait;

  /**
   * AWS interface.
   *
   * @var Drupal\et_stock\AWS
   */
  protected AWS $aws;

  /**
   * Constructs a new BatchProcessingCommands object.
   */
  public function __construct(AWS $aws) {
    $this->logger = $this->getLogger('et_stock');
    $this->aws = $aws;
  }

  /**
   * Process product inventory.
   *
   * @command et:get-inventory
   *
   * @usage et:get-inventory
   */
  public function downloadInventory() {
    $this->output()->writeln('Downloading new price list from AWS bucket.');
    $filepath = $this->aws->prepareInventoryFile();
    $this->output()->writeln('Price list download complete. (' . $filepath . ')');
  }

}
