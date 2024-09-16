<?php

namespace Drupal\custom_module\Commands;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drush\Commands\DrushCommands;

/**
 *
 */
class CustomConfigCommands extends DrushCommands {

  protected $configFactory;

  /**
   *
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * Update the custom configuration.
   *
   * @command custom_config:update
   * @param string $key
   *   The configuration key.
   * @param string $value
   *   The configuration value.
   *
   * @access public
   */
  public function updateConfig($key, $value) {
    $config = $this->configFactory->getEditable('custom_config.settings');
    $config->set($key, $value)->save();
    $this->logger()->notice("Updated configuration: $key = $value");
  }

  /**
   * Add a new value to the configuration.
   *
   * @command custom_config:add
   * @param string $key
   *   The new configuration key.
   * @param string $value
   *   The new configuration value.
   */
  public function addConfig($key, $value) {
    $config = $this->configFactory->getEditable('custom_config.settings');
    $config->set($key, $value)->save();
    $this->logger()->notice("Added configuration: $key = $value");
  }

}
