<?php

namespace Drupal\quizz_migrate\Source;

use MigrateSource;

class QuestionSettingsSource extends MigrateSource {

  private $question_type;
  private $item_number = 0;
  private $variables = array();

  public function __construct($options = array()) {
    $this->question_type = $options['question_type'];
    parent::__construct($options);

    $question_type = quizz_question_type_load($this->question_type);
    if (($handler = $question_type->getHandler()) && method_exists($handler, 'questionTypeConfigForm')) {
      $handler_form = $handler->questionTypeConfigForm($question_type);
    }
    elseif (($fn = $question_type->handler . '_quiz_question_config') && function_exists($fn)) {
      $handler_form = $fn($question_type);
    }

    $this->variables = array();

    if ($handler_form) {
      foreach (element_children($handler_form) as $name) {
        if (!isset($handler_form[$name]['#type'])) {
          continue;
        }

        $type = $handler_form[$name]['#type'];
        $types = array('textfield', 'textarea', 'select', 'radios', 'checkbox', 'checkboxes');
        if (in_array($type, $types)) {
          $this->variables[$name] = array($name, variable_get($name, $handler_form[$name]['#default_value']));
        }
      }
    }
  }

  public function fields() {
    return array(
        'name'  => 'Variable name',
        'value' => 'Varible value',
    );
  }

  public function __toString() {
    return "Settings for question:{$this->question_type}";
  }

  public function computeCount() {
    return count($this->variables);
  }

  public function performRewind() {
    $this->item_number = 0;
  }

  public function getNextRow() {
    if (!isset($this->variables[$this->item_number])) {
      return NULL;
    }

    $var = $this->variables[$this->item_number++];
    return (object) array('name' => $var[0], 'value' => $var[1]);
  }

}
