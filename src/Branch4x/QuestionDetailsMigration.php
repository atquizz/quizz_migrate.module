<?php

namespace Drupal\quizz_migrate\Branch4x;

use Drupal\quizz_migrate\Branch4x\QuestionDetails\ClozeDetails;
use Drupal\quizz_migrate\Branch4x\QuestionDetails\DdlinesDetails;
use Drupal\quizz_migrate\Branch4x\QuestionDetails\DetailsInterface;
use Drupal\quizz_migrate\Branch4x\QuestionDetails\LongTextDetails;
use Drupal\quizz_migrate\Branch4x\QuestionDetails\ShortTextDetails;
use Drupal\quizz_migrate\Branch4x\QuestionDetails\TrueFalseDetails;
use Migration;

class QuestionDetailMigration extends Migration {

  /** @var string */
  protected $bundle;

  /** @var DetailsInterface */
  protected $details;

  public function __construct($arguments = array()) {
    $this->bundle = $arguments['bundle'];
    $handler = $this->getDetailsHelper();
    $this->source = $handler->setupMigrateSource();
    $this->destination = $handler->setupMigrateDestination();
    $this->map = $handler->setupMigrateMap();
    $handler->setupMigrateFieldMapping();
    parent::__construct($arguments);
  }

  public function getMachineName() {
    return $this->machineName;
  }

  protected function getDetailsHelper() {
    if (NULL === $this->details) {
      switch ($this->bundle) {
        case 'quiz_cloze':
          $this->details = new ClozeDetails($this);
          break;
        case 'quiz_ddlines':
          $this->details = new DdlinesDetails($this);
          break;
        case 'quiz_fileupload':
          break;
        case 'quiz_long_answer':
          $this->details = new LongTextDetails($this);
          break;
        case 'quiz_matching':
          break;
        case 'quiz_multichoice':
          break;
        case 'quiz_pool':
          break;
        case 'quiz_scale':
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
    $this->getDetailsHelper()->setup();
    parent::import();
  }

}
