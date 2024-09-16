<?php

namespace Drupal\custom_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Queue\QueueFactory;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Drupal\node\Entity\Node;

/**
 * Custom API Controller to fetch and store data.
 */
class CustomApiController extends ControllerBase {

  protected $database;
  protected $queueFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(Connection $database, QueueFactory $queue_factory) {
    $this->database = $database;
    $this->queueFactory = $queue_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('queue')
    );
  }

  /**
   * Fetches data from external API and stores it in the database.
   */
  public function fetchData() {
    $client = new Client();
    try {
      $response = $client->get('https://api.restful-api.dev/objects');
      $data = json_decode($response->getBody()->getContents(), TRUE);
    }
    catch (\Exception $e) {
      \Drupal::logger('custom_api')->error($e->getMessage());
      return new JsonResponse(['error' => 'Failed to fetch data.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    if (!empty($data)) {
      foreach ($data as $item) {
        $id = isset($item['id']) ? (int) $item['id'] : NULL;
        $name = isset($item['name']) ? filter_var($item['name']) : '';
        $color = isset($item['data']['color']) ? filter_var($item['data']['color']) :
                 (isset($item['data']['Color']) ? filter_var($item['data']['Color']) : 'unknown');
        $capacity = isset($item['data']['capacity']) ? (int) $item['data']['capacity'] :
                   (isset($item['data']['Capacity']) ? (int) $item['data']['Capacity'] : 0);

        // if ($id !== NULL) {
          // Create or update the node with fetched data.
          // $node = Node::load($id);
          // if ($node) {
          //   $node->set('title', $name);
          //   $node->set('field_color', $color);
          //   $node->set('field_capacity', $capacity);
          //   $node->save();
          // } else {
            $node = Node::create([
              'type' => 'custom_type',  // Define the content type.
              'title' => $name,
              'field_color' => $color,
              'field_capacity' => $capacity,
            ]);
            $node->save();
          // }

          // Store in the custom table as well.
          $this->database->merge('custom_table')
            ->keys(['id' => $id])
            ->fields([
              'name' => $name,
              'color' => $color,
              'capacity' => $capacity,
            ])
            ->execute();
        // }
      }

      // Enqueue the task to invalidate the cache.
      $queue = $this->queueFactory->get('custom_api_queue_worker');
      $queue->createItem(['invalidate_cache' => TRUE]);

      // Return the fetched data in JSON response.
      return new JsonResponse($data);
    }

    return new JsonResponse(['error' => 'No data available.'], Response::HTTP_NO_CONTENT);
  }
}
