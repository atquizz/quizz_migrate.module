<?php

namespace Drupal\quizz_migrate\Destination;

use Drupal\quizz_migrate\Destination\QuestionDestination;
use Drupal\quizz_question\Entity\Question;
use stdClass;

class QuestionRevisionDestination extends QuestionDestination {

  protected $base_table = 'quiz_question_revision';
  protected static $pk_name = 'vid';

  public function fields($migration = NULL) {
    $fields = array();

    $schema = drupal_get_schema('quiz_question_entity');
    foreach ($schema['fields'] as $name => $info) {
      $fields[$name] = isset($info['description']) ? $info['description'] : $name;
    }

    return $fields + parent::fields($migration);
  }

  /**
   * @param Question $entity
   * @param stdClass $row
   */
  public function prepare($entity, stdClass $row) {
    $table = "migrate_map_quiz_question__{$entity->type}";
    $sql = 'SELECT destid1 FROM {' . $table . '} WHERE sourceid1 = :nid';
    $entity->qid = db_query($sql, array(':nid' => $row->nid))->fetchColumn();
    $entity->is_new = FALSE;
    $entity->is_new_revision = TRUE;
  }

}
