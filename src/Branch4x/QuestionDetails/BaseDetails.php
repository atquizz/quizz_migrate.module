<?php

namespace Drupal\quizz_migrate\Branch4x\QuestionDetails;

use Drupal\quizz_migrate\Branch4x\QuestionDetailMigration;
use MigrateDestinationTable;
use MigrateSourceSQL;
use MigrateSQLMap;

abstract class BaseDetails implements DetailsInterface {

  /** @var QuestionDetailMigration */
  protected $migration;

  public function __construct(QuestionDetailMigration $migration) {
    $this->migration = $migration;
  }

  public function setupMigrateSource() {
    $query = db_select($this->source_table_name, 'details');
    $query->fields('details', $this->source_columns);
    return new MigrateSourceSQL($query);
  }

  public function setupMigrateDestination() {
    return new MigrateDestinationTable($this->dest_table_name);
  }

  public function setupMigrateFieldMapping() {
    $m = $this->migration;
    foreach ($this->column_mapping as $source_column => $destination_column) {
      $m->addFieldMapping($destination_column, $source_column);
    }
  }

  public function setupMigrateMap() {
    $pk_source = MigrateDestinationTable::getKeySchema($this->dest_table_name);
    $pk_dest = MigrateDestinationTable::getKeySchema($this->source_table_name);
    return new MigrateSQLMap($this->migration->getMachineName(), $pk_source, $pk_dest);
  }

  public function import() {

  }

}
