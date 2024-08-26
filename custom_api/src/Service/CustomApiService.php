<?php

namespace Drupal\custom_api\Service;

use Drupal\Core\Database\Connection;

/**
 *
 */
class CustomApiService {

  protected $database;

  /**
   *
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   *
   */
  public function getDataFromTable() {
    $query = $this->database->select('custom_table', 'c')
      ->fields('c')
      ->execute();

    return $query->fetchAll();
  }

}
