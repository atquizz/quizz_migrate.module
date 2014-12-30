<?php

namespace Drupal\quizz_migrate\Destination;

use MigrateDestinationTable;

class QuestionDetailsDestination extends MigrateDestinationTable {

  public function rollback(array $ids) {
    return parent::rollback($ids);
  }

}
