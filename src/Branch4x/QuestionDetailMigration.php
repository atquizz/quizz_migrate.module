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
  private $details_handler;

  public function __construct($arguments = array()) {
    $this->bundle = $arguments['bundle'];

    if ($handler = $this->getDetailsHandler()) {
      $this->source = $handler->setupMigrateSource();
      $this->destination = $handler->setupMigrateDestination();
      $this->map = $handler->setupMigrateMap();
      $handler->setupMigrateFieldMapping();
    }

    parent::__construct($arguments);
  }

  public function sourceCount($refresh = FALSE) {
    if (NULL === $this->source) {
      return t('N/A');
    }
    return parent::sourceCount($refresh);
  }

  public function processedCount() {
    if (NULL === $this->map) {
      return t('N/A');
    }
    return parent::processedCount();
  }

  public function importedCount() {
    if (NULL === $this->map) {
      return t('N/A');
    }
    return parent::importedCount();
  }

  public function messageCount() {
    if (NULL === $this->map) {
      return t('N/A');
    }
    return parent::messageCount();
  }

  public function getMachineName() {
    return $this->machineName;
  }

  protected function getDetailsHandler() {
    if (NULL === $this->details_handler) {
      switch ($this->bundle) {
        case 'cloze':
          $this->details_handler = new ClozeDetails($this->bundle, $this);
          break;
        case 'quiz_ddlines':
          $this->details_handler = new DdlinesDetails($this->bundle, $this);
          break;
        case 'long_answer':
          $this->details_handler = new LongTextDetails($this->bundle, $this);
          break;
        case 'matching':
          $this->details_handler = new MatchingDetails($this->bundle, $this);
          break;
        case 'truefalse':
          $this->details_handler = new TrueFalseDetails($this->bundle, $this);
          break;
        case 'short_answer':
          $this->details_handler = new ShortTextDetails($this->bundle, $this);
          break;
        case 'multichoice':
        case 'pool':
        case 'quiz_directions':
          break;
      }
    }
    return $this->details_handler;
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

  protected function rollback() {
    parent::rollback();
  }

  protected function postImport() {
    parent::postImport();
    $this->getDetailsHandler()->postImport();
  }

}
