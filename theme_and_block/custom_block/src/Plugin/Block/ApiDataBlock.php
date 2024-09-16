<?php

namespace Drupal\custom_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block for API data.
 *
 * @Block(
 *   id = "api_data_block",
 *   admin_label = @Translation("API Data Block"),
 *   category = @Translation("Custom")
 * )
 */
class ApiDataBlock extends BlockBase {

  protected $httpClient;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }

  public function build() {
    $response = $this->httpClient->request('GET', 'https://api.restful-api.dev/objects');
    $data = json_decode($response->getBody(), TRUE);

    $output = '';
    foreach ($data as $item) {
      $output .= '<div>' . $item['id']. $item['name'] . ': ' . $item['data']['color'] . $item['data']['Color'] . $item['data']['capacity']'</div>';
    }

    return [
      '#markup' => $output,
      '#cache' => [
        'max-age' => 3600, // Cache for 1 hour
      ],
    ];
  }
}
