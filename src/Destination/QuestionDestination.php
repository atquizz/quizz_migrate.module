<?php

namespace Drupal\quizz_migrate\Destination;

use Drupal\quizz_question\Entity\QuestionController;
use MigrateDestinationEntity;
use stdClass;

class QuestionDestination extends MigrateDestinationEntity {

  protected $base_table = 'quiz_question_entity';
  protected static $pk_name = 'qid';

  public function __construct($bundle, $options = array()) {
    parent::__construct('quiz_question_entity', $bundle, $options);
  }

  static public function getKeySchema() {
    return array(
        static::$pk_name => array(
            'type'        => 'int',
            'unsigned'    => TRUE,
            'description' => 'Question ID or VID',
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
    $fields += migrate_handler_invoke_all('Question', 'fields', $this->entityType, $this->bundle, $migration);

    return $fields;
  }

  public function bulkRollback(array $ids) {
    migrate_instrument_start($this->entityType . '_delete_multiple');
    $this->prepareRollback($ids);
    entity_delete_multiple($this->entityType, $ids);
    $this->completeRollback($ids);
    migrate_instrument_stop($this->entityType . '_delete_multiple');
  }

  public function import(stdClass $question, stdClass $row) {
    $question = entity_create($this->entityType, (array) $question);
    unset($question->is_new);

    $this->prepare($question, $row);

    QuestionController::$disable_invoking = TRUE;
    $question->save();
    QuestionController::$disable_invoking = FALSE;

    $this->complete($question, $row);
    return array($question->{static::$pk_name});
  }

}
