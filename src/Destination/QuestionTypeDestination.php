<?php

namespace Drupal\quizz_migrate\Destination;

use Drupal\quizz_question\Entity\QuestionType;
use MigrateDestinationEntity;
use stdClass;

class QuestionTypeDestination extends MigrateDestinationEntity {

  public function __construct($options = array()) {
    parent::__construct('quiz_question_type', NULL, $options);
  }

  static public function getKeySchema() {
    return array(
        'type' => array(
            'type'        => 'varchar',
            'length'      => 32,
            'not null'    => TRUE,
            'default'     => '',
            'description' => 'Machine name of question type entity.',
        ),
    );
  }

  public function fields($migration = NULL) {
    $fields = array();

    $schema = drupal_get_schema('quiz_question_type');
    foreach ($schema['fields'] as $name => $info) {
      $fields[$name] = isset($info['description']) ? $info['description'] : $name;
    }

    return $fields;
  }

  public function import(stdClass $entity, stdClass $row) {
    $entity = entity_create($this->entityType, (array) $entity);
    $return = $this->doImport($entity, $row);
    return $return;
  }

  /**
   * @param QuestionType $entity
   */
  private function doImport($entity, $row) {
    $this->prepare($entity, $row);
    $entity->save();
    $this->complete($entity, $row);
    quizz_migrate_enable_question_type($entity->type);
    return array($entity->type);
  }

}
