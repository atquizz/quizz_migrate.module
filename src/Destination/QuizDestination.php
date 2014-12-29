<?php

namespace Drupal\quizz_migrate\Destination;

use MigrateDestinationEntity;
use stdClass;

class QuizDestination extends MigrateDestinationEntity {

  protected $base_table = 'quiz_entity';
  protected static $pk_name = 'qid';

  public function __construct($options = array()) {
    parent::__construct('quiz_entity', 'quiz', $options);
  }

  static public function getKeySchema() {
    return array(
        'qid' => array(
            'type'        => 'int',
            'unsigned'    => TRUE,
            'description' => 'ID of destination quiz',
        ),
    );
  }

  public function fields($migration = NULL) {
    $fields = array();

    $schema = drupal_get_schema($this->base_table);
    foreach ($schema['fields'] as $name => $info) {
      $fields[$name] = isset($info['description']) ? $info['description'] : $name;
    }

    $fields += migrate_handler_invoke_all('Entity', 'fields', $this->entityType, $this->bundle, $migration);
    $fields += migrate_handler_invoke_all('QuizRevision', 'fields', $this->entityType, $this->bundle, $migration);

    return $fields;
  }

  public function bulkRollback(array $ids) {
    migrate_instrument_start($this->entityType . '_delete_multiple');
    $this->prepareRollback($ids);
    entity_delete_multiple($this->entityType, $ids);
    $this->completeRollback($ids);
    migrate_instrument_stop($this->entityType . '_delete_multiple');
  }

  public function import(stdClass $entity, stdClass $row) {
    $entity = entity_create($this->entityType, (array) $entity);

    $this->prepare($entity, $row);
    $entity->save();

    $this->complete($entity, $row);
    return array($entity->{static::$pk_name});
  }

}
