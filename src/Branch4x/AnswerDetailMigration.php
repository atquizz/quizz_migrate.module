<?php

namespace Drupal\quizz_migrate\Branch4x;

use Migration;

/**
 * quiz_cloze
 * quiz_ddlines
 * quiz_fileupload
 * quiz_long_answer
 * quiz_matching
 * quiz_multichoice
 * quiz_pool
 * quiz_scale
 * quiz_truefalse
 * quiz_short_answer
 */
class AnswerDetailMigration extends Migration {

  protected $handler_name;
  protected $source_table_name;
  protected $dest_table_name;

  public function __construct($arguments = array()) {
    $this->handler_name = $arguments['handler_name'];
    $this->source_table_name = $arguments['source_table_name'];
    $this->dest_table_name = $arguments['dest_table_name'];
    parent::__construct($arguments);
  }

}
