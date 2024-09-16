<?php

namespace Drupal\custom_api\Service;

use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\CronInterface;

/**
 * Service for handling cron tasks.
 */
class CronService {

  protected $queueFactory;

  /**
   * Constructs a CronService object.
   *
   * @param \Drupal\Core\Queue\QueueFactory $queue_factory
   *   The queue factory service.
   */
  public function __construct(QueueFactory $queue_factory) {
    $this->queueFactory = $queue_factory;
  }

  /**
   * Process the custom API queue.
   */
  public function processCustomApiQueue() {
    $queue = $this->queueFactory->get('custom_api_queue_worker');
    $queue->runQueue();
  }
}
