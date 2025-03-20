<?php

namespace Drupal\et_stock;

use Aws\S3\S3Client;
use Aws\S3\S3ClientInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\File\FileSystem;
use Drupal\Core\File\FileSystemInterface;

/**
 * Handle interactions with S3 file storage.
 */
class AWS {
  use LoggerChannelTrait;

  /**
   * Logger Interface.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Drupal file system interface.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected FileSystemInterface $fileSystem;

  /**
   * S3 Client.
   *
   * @var \Aws\S3\S3ClientInterface
   */
  private S3ClientInterface $s3Client;

  /**
   * Constructs a new AWS object.
   */
  public function __construct(FileSystem $file_system) {
    $this->logger = $this->getLogger('et_stock');
    $this->fileSystem = $file_system;
    $this->s3Client = new S3Client([
      'version'     => 'latest',
      'region'      => 'us-east-1',
      'credentials' => [
        'key'    => Settings::get('import_aws_s3_key'),
        'secret' => Settings::get('import_aws_s3_secret'),
      ],
    ]);
  }

  /**
   * Moves the inventory import file into place for processing.
   */
  public function prepareInventoryFile() {
    $bucket = Settings::get('import_aws_s3_bucket');
    $result = $this->s3Client->listObjects([
      'Bucket' => $bucket,
      'MaxKeys' => 1000,
      'Prefix' => 'Website_Inventory',
    ]);
    $files = $result->getPath('Contents');
    $current_file = array_reduce($files, function ($carry, $n) {
      if (!$carry || $carry['LastModified'] < $n['LastModified']) {
        return $n;
      }
      return $carry;
    }, NULL);

    $temp_name = $this->fileSystem->tempnam('temporary://', 'PL_');
    $result = $this->s3Client->getObject([
      'Bucket' => $bucket,
      'Key' => $current_file['Key'],
      'version' => 'latest',
      'SaveAs' => $this->fileSystem->realpath($temp_name),
    ]);

    $dest = 'private://import/currentInventory.xlsx';
    $dest_dir = $this->fileSystem->dirname($dest);
    $this->fileSystem->prepareDirectory($dest_dir, FileSystemInterface::CREATE_DIRECTORY);
    $this->fileSystem->move($temp_name, $dest, FileSystemInterface::EXISTS_REPLACE);
    return $dest;
  }

}
