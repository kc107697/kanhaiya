<?php

namespace Drupal\custom_api\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Database\Connection;

/**
 * Custom API service to handle data fetching and caching.
 */
class CustomApiService {

  protected $database;
  protected $cache;

  /**
   *
   */
  public function __construct(Connection $database, CacheBackendInterface $cache) {
    $this->database = $database;
    $this->cache = $cache;
  }

  /**
   * Get data from cache or database.
   */
  public function getDataFromCacheOrDb() {
    // Try to get data from cache.
    if ($cache = $this->cache->get('custom_api_data')) {
      return $cache->data;
    }

    // If no cache, fetch from database.
    $query = $this->database->select('custom_table', 'c')
      ->fields('c')
      ->execute();
    $data = $query->fetchAll();

    // Store the data in cache.
    $this->cache->set('custom_api_data', $data, CacheBackendInterface::CACHE_PERMANENT);

    return $data;
  }

  /**
   * Invalidate cache when data is updated or inserted.
   */
  public function invalidateCache() {
    $this->cache->invalidate('custom_api_data');
  }

}
