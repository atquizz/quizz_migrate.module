<?php

namespace Drupal\quizz_migrate\Branch4x;

use Drupal\quizz_migrate\Branch4x\QuestionDetails\ClozeDetails;
use Drupal\quizz_migrate\Branch4x\QuestionDetails\DdlinesDetails;
use Drupal\quizz_migrate\Branch4x\QuestionDetails\DetailsInterface;
use Drupal\quizz_migrate\Branch4x\QuestionDetails\LongTextDetails;
use Drupal\quizz_migrate\Branch4x\QuestionDetails\MatchingDetails;
use Drupal\quizz_migrate\Branch4x\QuestionDetails\ShortTextDetails;
use Drupal\quizz_migrate\Branch4x\QuestionDetails\TrueFalseDetails;
use Drupal\quizz_question\Entity\Question;
use Migration;
use RuntimeException;

class QuestionDetailMigration extends Migration {

  /** @var string */
  protected $bundle;

  /** @var DetailsInterface */
  protected $details;

  public function __construct($arguments = array()) {
    $this->bundle = $arguments['bundle'];
    $handler = $this->getDetailsHandler();
    $this->source = $handler->setupMigrateSource();
    $this->destination = $handler->setupMigrateDestination();
    $this->map = $handler->setupMigrateMap();
    $handler->setupMigrateFieldMapping();
    parent::__construct($arguments);
  }

  public function getMachineName() {
    return $this->machineName;
  }

  protected function getDetailsHandler() {
    if (NULL === $this->details) {
      switch ($this->bundle) {
        case 'quiz_cloze':
          $this->details = new ClozeDetails($this);
          break;
        case 'quiz_ddlines':
          $this->details = new DdlinesDetails($this);
          break;
        case 'quiz_long_answer':
          $this->details = new LongTextDetails($this);
          break;
        case 'quiz_matching':
          $this->details = new MatchingDetails($this);
          break;
        case 'quiz_truefalse':
          $this->details = new TrueFalseDetails($this);
          break;
        case 'quiz_short_answer':
          $this->details = new ShortTextDetails($this);
          break;
      }
    }
    return $this->details;
  }

  protected function import() {
    $this->getDetailsHandler()->setup();
    parent::import();
  }

  /**
   * @param Question $question
   * @throws RuntimeException
   */
  public function prepare($question, $row) {
    $map = array(
        'qid' => 'SELECT destid1 FROM {migrate_map_quiz_question__' . $row->question_type . '} WHERE sourceid1 = :id',
        'vid' => 'SELECT destid1 FROM {migrate_map_quiz_question_revision__' . $row->question_type . '} WHERE sourceid1 = :id',
    );

    foreach ($map as $k => $sql) {
      if (!$question->{$k} = db_query($sql, array(':id' => $question->{$k}))->fetchColumn()) {
        throw new RuntimeException($k . ' not found. Source: ' . var_export($row));
      }
    }
  }

  protected function postImport() {
    parent::postImport();
    $this->getDetailsHandler()->import();
  }

}
