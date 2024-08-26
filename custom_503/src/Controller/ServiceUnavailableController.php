<?php

namespace Drupal\custom_503\Controller;

use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 *
 */
class ServiceUnavailableController {

  /**
   * Simulate a 503 Service Unavailable exception.
   */
  public function serviceUnavailable() {
    // Throwing a 503 exception.
    throw new ServiceUnavailableHttpException(NULL, 'Service is currently unavailable');
  }

}
