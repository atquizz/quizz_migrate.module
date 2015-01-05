<?php

namespace Drupal\quizz_migrate\Destination;

use MigrateDestination;
use stdClass;

class QuizSettingsDestination extends MigrateDestination {

  public function __toString() {
    return '';
  }

  public static function getKeySchema() {
    return array(
        'type' => array('type' => 'varchar', 'length' => 32, 'not null' => TRUE),
        'name' => array('type' => 'varchar', 'length' => 128, 'not null' => TRUE),
    );
  }

  public function fields() {
    return array(
        'type'  => 'Quiz type (Bundle name)',
        'name'  => 'Variable name',
        'value' => 'Variable value',
    );
  }

  public function import(stdClass $object, stdClass $row) {
    $quiz_type = quizz_type_load($object->type);
    $quiz_type->setConfig($object->name, $object->value);
    $quiz_type->save();
    return array($object->type, $object->name);
  }

}
