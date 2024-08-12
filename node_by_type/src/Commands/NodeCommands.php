<?php

namespace Drupal\node_by_type\Commands;

use Drush\Commands\DrushCommands;
use Drupal\node\Entity\Node;

class NodeCommands extends DrushCommands {
    /**
     * List nodes by type.
     *
     * @command node-by-type
     * @aliases node-by-type
     * @option type The type of the nodes to list.
     * @usage drush node-by-type --type="World Cup"
     *   List all nodes of type sports.
     * @validate-module-enabled node_by_type
    */
    public function listNodesByType($options = ['type' => NULL]) {
    if (empty($options['type'])) {
      $this->logger()->error(dt('The --type option is required.'));
      return;
    }

    $nids = \Drupal::entityQuery('node')
      ->accessCheck(TRUE)
      ->condition('type', 'landing_page') 
      ->condition('field_sports_type.entity.name', $options['type'])
      ->execute();

    if (empty($nids)) {
      $this->logger()->notice(dt('No nodes found of type @type.', ['@type' => $options['type']]));
    } else {
      $nodes = Node::loadMultiple($nids);
      foreach ($nodes as $node) {
        $this->logger()->notice(dt('Node ID: @nid, Title: @title', ['@nid' => $node->id(), '@title' => $node->getTitle()]));
      }
    }
  }

}