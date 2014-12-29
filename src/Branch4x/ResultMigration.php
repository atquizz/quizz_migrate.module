<?php

namespace Drupal\quizz_migrate\Branch4x;

use Migration;

class ResultMigration extends Migration {

  public function __construct($arguments = array()) {
    parent::__construct($arguments);
    $this->source = $this->setupMigrateSource();
    $this->destination = $this->setupMigrateDestination();
    $this->map = new MigrateSQLMap($this->machineName, $this->sourcePK, RelationshipDestination::getKeySchema());
    $this->setupFieldMapping();
  }

  protected function setupMigrateSource() {
    $query = db_select('quiz_node_results', 'result');
    $query->innerJoin('node_revision', 'r', 'result.vid = r.vid'); // Do not migrate broken revisions
    $query
      ->fields('result', array('result_id', 'nid', 'vid', 'uid', 'time_start', 'time_end', 'released', 'score', 'is_invalid', 'is_evaluated', 'time_left', 'archived'))
      ->orderBy('result.vid')
    ;
    return new MigrateSourceSQL($query);
  }

  protected function setupMigrateDestination() {
    return new ResultDestination();
  }

}
