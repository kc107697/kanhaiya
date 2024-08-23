<?php

namespace Drupal\custom_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Custom API Controller to fetch and store data.
 */
class CustomApiController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * CustomApiController constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database service.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Fetch data from external API and insert into the database.
   */
  public function fetchData() {
    $client = new Client();
    $response = $client->get('https://api.restful-api.dev/objects');
    $data = json_decode($response->getBody()->getContents(), TRUE);

    if (!empty($data)) {
      foreach ($data as $item) {
        // Validate and sanitize data before insertion.
        $id = isset($item['id']) ? (int) $item['id'] : NULL;
        $name = isset($item['name']) ? filter_var($item['name']) : '';
        $color = isset($item['data']['color']) ? filter_var($item['data']['color']) :
                 (isset($item['data']['Color']) ? filter_var($item['data']['Color']) : 'unknown');
        $capacity = isset($item['data']['capacity']) ? (int) $item['data']['capacity'] :
                    (isset($item['data']['Capacity']) ? (int) $item['data']['Capacity'] : 0);

        // Insert or update data in the table.
        if ($id !== NULL) {
          $this->database->merge('custom_table')
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
      $query = $this->database->select('custom_table', 'c')
        ->fields('c', ['id', 'name', 'color', 'capacity'])
        ->execute();

      $fetched_data = $query->fetchAllAssoc('id');

      // Return the fetched data in JSON response.
      return new JsonResponse($fetched_data);
    }

    return new JsonResponse(['error' => 'Failed to fetch data.'], Response::HTTP_INTERNAL_SERVER_ERROR);
  }

}
