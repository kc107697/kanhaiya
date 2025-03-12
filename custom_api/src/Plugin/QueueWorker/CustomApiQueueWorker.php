<?php

namespace Drupal\custom_api\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;

/**
 * Processes the queue to invalidate and update cache.
 *
 * @QueueWorker(
 *   id = "custom_api_queue_worker",
 *   title = @Translation("Custom API Queue Worker"),
 *   cron = {"time" = 60}
 * )
 */
class CustomApiQueueWorker extends QueueWorkerBase {

  /**
   * Process the queue item.
   */
  public function processItem($data) {
    // Invalidate the cache to trigger a fresh cache on the next request.
    $service = \Drupal::service('custom_api.custom_service');
    $service->invalidateCache();

    // Optionally, refresh the cache after invalidating it by fetching fresh data.
    $service->getDataFromCacheOrDb();
  }

}
