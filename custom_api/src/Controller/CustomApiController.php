<?php

namespace Drupal\custom_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

class CustomApiController extends ControllerBase {

  public function fetchData() {
    $client = new Client();
    $response = $client->get('https://api.restful-api.dev/objects');
    $data = json_decode($response->getBody()->getContents(), TRUE);

    // Get the database connection.
    $database = \Drupal::database();

    if (!empty($data)) {
      foreach ($data as $item) {
        // Validate and sanitize data before insertion.
        $id = isset($item['id']) ? (int) $item['id'] : NULL;
        $name = isset($item['name']) ? filter_var($item['name'], FILTER_SANITIZE_STRING) : '';
        // $color = isset($item['data']['color']) ? filter_var($item['data']['color'], FILTER_SANITIZE_STRING) : 'unknown';// Default value if color is missing
        // $capacity = isset($item['data']['capacity']) ? (int) $item['data']['capacity'] : 0; // Default value if capacity is missing

        // Handle case-sensitive fields for both "color" and "Color"

        $color = isset($item['data']['color']) ? filter_var($item['data']['color'], FILTER_SANITIZE_STRING) :
                 (isset($item['data']['Color']) ? filter_var($item['data']['Color'], FILTER_SANITIZE_STRING) : 'unknown'); // Default to 'unknown' if both are missing


        // Handle case-sensitive fields for both "capacity" and "Capacity"

        $capacity = isset($item['data']['capacity']) ? (int) $item['data']['capacity'] :
                    (isset($item['data']['Capacity']) ? (int) $item['data']['Capacity'] : 0); // Default to 0 if both are missing

        // Insert or update data in the table.
        if ($id !== NULL) {
          $database->merge('custom_table')
            ->keys(['id' => $id])
            ->fields([
              'name' => $name,
              'color' => $color,
              'capacity' => $capacity,
            ])
            ->execute();
        }
      }

      // After inserting, fetch all data from the database.
      $query = $database->select('custom_table', 'c')
        ->fields('c', ['id', 'name', 'color', 'capacity'])
        ->execute();
     
      $fetched_data = $query->fetchAllAssoc('id');

      // Return the fetched data in JSON response.
      return new JsonResponse($fetched_data);
    }

    return new JsonResponse(['error' => 'Failed to fetch data.'], Response::HTTP_INTERNAL_SERVER_ERROR);
  }
}
