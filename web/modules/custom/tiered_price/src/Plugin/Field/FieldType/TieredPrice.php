<?php

namespace Drupal\tiered_price\Plugin\Field\FieldType;

use Drupal\commerce_price\Plugin\Field\FieldType\PriceItem;
use Drupal\Core\Entity\TypedData\EntityDataDefinition;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataReferenceDefinition;

/**
 * Plugin implementation of the 'tiered_price' field type.
 *
 * @FieldType(
 *   id = "tiered_price",
 *   label = @Translation("Tiered Price"),
 *   description = @Translation("Stores a decimal number and a three letter currency code, per tier."),
 *   category = @Translation("Commerce"),
 *   default_widget = "tiered_price_default",
 *   default_formatter = "tiered_price_default",
 * )
 */
class TieredPrice extends PriceItem {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['tier'] = DataReferenceDefinition::create('entity')
      ->setLabel(t('Tier'))
      ->setComputed(TRUE)
      ->setReadOnly(FALSE)
      ->setTargetDefinition(EntityDataDefinition::create('taxonomy_term'))
      ->addConstraint('EntityType', 'taxonomy_term');

    $properties['tier_id'] = DataDefinition::create('integer')
      ->setLabel(t('Tier ID'))
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);

    $schema['columns']['tier_id'] = [
      'description' => 'The tier term ID.',
      'type' => 'int',
      'unsigned' => TRUE,
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $value = parent::generateSampleValue($field_definition);

    $value['tier'] = 1;

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function onChange($property_name, $notify = TRUE) {
    // Make sure that the target ID and the target property stay in sync.
    if ($property_name == 'tier') {
      $property = $this->get('tier');
      $target_id = $property->isTargetNew() ? NULL : $property->getTargetIdentifier();
      $this->writePropertyValue('tier_id', $target_id);
    }
    elseif ($property_name == 'tier_id') {
      $this->writePropertyValue('tier', $this->target_id);
    }
    parent::onChange($property_name, $notify);
  }

}
