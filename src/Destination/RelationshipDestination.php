<?php

namespace Drupal\quizz_migrate\Destination;

use MigrateDestinationEntity;
use stdClass;

class RelationshipDestination extends MigrateDestinationEntity {

  protected $base_table = 'quiz_relationship';

  public function __construct($options = array()) {
    parent::__construct('quiz_relationship', NULL, $options);
  }

  static public function getKeySchema() {
    return array(
        'qr_id' => array(
            'type'        => 'int',
            'unsigned'    => TRUE,
            'description' => 'ID of relationship entity',
        ),
    );
  }

  public function fields($migration = NULL) {
    $fields = array();

    $schema = drupal_get_schema($this->base_table);
    foreach ($schema['fields'] as $name => $info) {
      $fields[$name] = isset($info['description']) ? $info['description'] : $name;
    }

    return $fields;
  }

  public function bulkRollback(array $ids) {
    migrate_instrument_start($this->entityType . '_delete_multiple');
    $this->prepareRollback($ids);
    entity_delete_multiple($this->entityType, $ids);
    $this->completeRollback($ids);
    migrate_instrument_stop($this->entityType . '_delete_multiple');
  }

  public function import(stdClass $relationship, stdClass $row) {
    $relationship = entity_create($this->entityType, (array) $relationship);
    unset($relationship->is_new);
    $this->prepare($relationship, $row);
    $relationship->save();
    $this->complete($relationship, $row);
    return array($relationship->qr_id);
  }

}
