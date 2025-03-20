<?php

namespace Drupal\et_stock\Form;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\et_stock\StubMigrationMessage;
use Drupal\et_stock\MigrateBatchExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Stock import form.
 */
class StockImportForm extends FormBase {

  /**
   * The migration plugin manager.
   *
   * @var \Drupal\migrate\Plugin\MigrationPluginManager
   */
  protected $pluginManagerMigration;

  /**
   * The migration definitions.
   *
   * @var array
   */
  protected $definitions;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->pluginManagerMigration = $container->get('plugin.manager.migration');
    $instance->definitions = $instance->pluginManagerMigration->getDefinitions();
    $instance->fileSystem = $container->get('file_system');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'et_stock_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['source_file'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload the source file'),
    ];
    $form['import'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $validators = ['file_validate_extensions' => ['xlsx']];

    $directory = $this->fileSystem->realpath(FALSE);
    $this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);

    $file = file_save_upload('source_file', $validators, FALSE, 0, FileSystemInterface::EXISTS_REPLACE);

    if (isset($file)) {
      // File upload was attempted.
      if ($file) {
        $form_state->setValue('file_path', $file->getFileUri());
      }
      // File upload failed.
      else {
        $form_state->setErrorByName('source_file', $this->t('The file could not be uploaded.'));
      }
    }
    else {
      $form_state->setErrorByName('source_file', $this->t('You have to upload a source file.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $migration_id = 'stock';
    /** @var \Drupal\migrate\Plugin\Migration $migration */
    $migration = $this->pluginManagerMigration->createInstance($migration_id);

    // Reset status.
    $status = $migration->getStatus();
    if ($status !== MigrationInterface::STATUS_IDLE) {
      $migration->setStatus(MigrationInterface::STATUS_IDLE);
      $this->messenger()->addWarning($this->t('Migration @id reset to Idle', ['@id' => $migration_id]));
    }

    $options = [
      'file_path' => $form_state->getValue('file_path'),
      'update' => 1,
    ];

    $executable = new MigrateBatchExecutable($migration, new StubMigrationMessage(), $options);
    $executable->batchImport();
  }

}
