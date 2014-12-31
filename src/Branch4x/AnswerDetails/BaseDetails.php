<?php

namespace Drupal\quizz_migrate\Branch4x\AnswerDetails;

use Drupal\quizz_migrate\Branch4x\AnswerDetailMigration;
use Drupal\quizz_migrate\Branch4x\QuestionDetails\DetailsInterface;
use Drupal\quizz_migrate\Destination\AnswerDetailsDestination;
use MigrateSourceSQL;
use MigrateSQLMap;
use RuntimeException;

abstract class BaseDetails implements DetailsInterface {

  /** @var AnswerDetailMigration */
  protected $migration;

  /** @var string */
  protected $bundle;

  /** @var string */
  protected $source_table_name;

  /** @var string[] */
  protected $source_columns = array();

  /** @var string */
  protected $dest_table_name;

  /** @var string[] */
  protected $column_mapping = array();

  /** @var array[] */
  protected $pk_source = array(
      'answer_id' => array('type' => 'int', 'not null' => TRUE, 'alias' => 'details'),
  );

  /** @var array[] */
  protected $pk_dest = array(
      'answer_id' => array('type' => 'int', 'not null' => TRUE)
  );

  public function __construct($bundle, AnswerDetailMigration $migration) {
    $this->bundle = $bundle;
    $this->migration = $migration;
  }

  public function setupMigrateSource() {
    $query = db_select($this->source_table_name, 'details');
    $query->innerJoin('node_revision', 'r', 'details.vid = r.vid');
    $query->innerJoin('node', 'n', 'r.nid = n.nid');
    $query->addField('n', 'type', 'question_type');
    $query->fields('details', $this->source_columns);
    $query->condition('n.type', $this->bundle);
    return new MigrateSourceSQL($query);
  }

  public function setupMigrateDestination() {
    return new AnswerDetailsDestination($this->dest_table_name);
  }

  public function setupMigrateFieldMapping() {
    $m = $this->migration;
    foreach ($this->column_mapping as $source_column => $destination_column) {
      $m->addFieldMapping($destination_column, $source_column);
    }
    $m->addUnmigratedSources(array('question_type'));
  }

  public function setupMigrateMap() {
    return new MigrateSQLMap($this->migration->getMachineName(), $this->pk_source, $this->pk_dest);
  }

  public function prepare($entity, $row) {
    $map = array(
        'result_id'    => 'SELECT destid1 FROM {migrate_map_quiz_result} WHERE sourceid1 = :id',
        'answer_id'    => 'SELECT destid1 FROM {migrate_map_quiz_answer} WHERE sourceid1 = :id',
        'qid'          => 'SELECT destid1 FROM {migrate_map_quiz_question__' . $row->question_type . '} WHERE sourceid1 = :id',
        'question_qid' => 'SELECT destid1 FROM {migrate_map_quiz_question__' . $row->question_type . '} WHERE sourceid1 = :id',
        'vid'          => 'SELECT destid1 FROM {migrate_map_quiz_question_revision__' . $row->question_type . '} WHERE sourceid1 = :id',
        'question_vid' => 'SELECT destid1 FROM {migrate_map_quiz_question_revision__' . $row->question_type . '} WHERE sourceid1 = :id',
    );

    foreach ($map as $k => $sql) {
      if (isset($entity->{$k})) {
        if (!$entity->{$k} = db_query($sql, array(':id' => $entity->{$k}))->fetchColumn()) {
          throw new RuntimeException($k . ' not found. Source: ' . var_export($row));
        }
      }
    }
  }

  /**
   * {@inhertidoc}
   */
  public function complete($entity, $row) {

  }

}
