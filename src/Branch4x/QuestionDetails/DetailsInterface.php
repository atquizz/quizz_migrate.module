<?php

namespace Drupal\quizz_migrate\Branch4x\QuestionDetails;

use MigrateDestination;
use MigrateSource;
use MigrateSQLMap;

interface DetailsInterface {

  /**
   * @return MigrateSource
   */
  public function setupMigrateSource();

  /**
   * @return MigrateDestination
   */
  public function setupMigrateDestination();

  /**
   * @return MigrateSQLMap
   */
  public function setupMigrateMap();

  /**
   * Setup field mappings.
   */
  public function setupMigrateFieldMapping();

  /**
   * On importing.
   */
  public function import();
}
