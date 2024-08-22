<?php

namespace Drupal\custom_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides a Custom REST resource.
 *
 * @RestResource(
 *   id = "custom_api_resource",
 *   label = @Translation("Custom API Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/cms/restpath"
 * }
 * )
 */
class CustomApiResource extends ResourceBase {

  /**
   * {@inheritdoc}
   */
  public function get() {
    $service = \Drupal::service('custom_api.custom_service');
    $data = $service->getDataFromTable();

    return new ResourceResponse($data);
  }
}
