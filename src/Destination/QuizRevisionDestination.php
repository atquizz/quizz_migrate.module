<?php

namespace Drupal\quizz_migrate\Destination;

use Drupal\quizz\Entity\QuizEntity;
use Drupal\quizz_migrate\Destination\QuizDestination;
use stdClass;

class QuizRevisionDestination extends QuizDestination {

  protected $base_table = 'quiz_entity_revision';
  protected static $pk_name = 'vid';

  public function fields($migration = NULL) {
    $fields = array();

    $schema = drupal_get_schema('quiz_entity');
    foreach ($schema['fields'] as $name => $info) {
      $fields[$name] = isset($info['description']) ? $info['description'] : $name;
    }

    return $fields + parent::fields($migration);
  }

  public function bulkRollback(array $ids) {
    migrate_instrument_start($this->entityType . '_delete_multiple');
    $this->prepareRollback($ids);
    foreach ($ids as $revision_id) {
      entity_revision_delete($this->entityType, $revision_id);
    }
    $this->completeRollback($ids);
    migrate_instrument_stop($this->entityType . '_delete_multiple');
  }

  static public function getKeySchema() {
    return array(
        'vid' => array(
            'type'        => 'int',
            'unsigned'    => TRUE,
            'description' => 'ID of destination quiz revision',
        ),
    );
  }

}
