<?php

namespace Drupal\custom_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

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
    $results = $service->getDataFromTable();
    foreach ($results as $key => $record) {
      $data[$key]['id'] = $record->id;
      $data[$key]['name'] = $record->name;
      $data[$key]['data']['color'] = $record->color;
      $data[$key]['data']['capacity'] = $record->capacity;
    }
    $response['data'] = $data;
    // $response = ['message' => 'Hello, this is a rest service'];
    return new ResourceResponse($response);

  }

}
