<?php

namespace Drupal\user_content_tracker\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Drupal\user\UserInterface;

/**
 * Event that is fired when a user logs in.
 */
class UserCustomLoginEvent extends Event {
  const EVENT_NAME = 'user_content_tracker.user_login';

  /**
   * The user object.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * The IP address.
   *
   * @var string
   */
  protected $ipAddress;

  /**
   * The login time.
   *
   * @var int
   */
  protected $loginTime;

  /**
   * Constructs the event object.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user object.
   * @param string $ip_address
   *   The IP address of the user.
   * @param int $login_time
   *   The login time.
   */
  public function __construct(UserInterface $user, string $ip_address, int $login_time) {
    $this->user = $user;
    $this->ipAddress = $ip_address;
    $this->loginTime = $login_time;
  }

  /**
   * Gets the user.
   *
   * @return \Drupal\user\UserInterface
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * Gets the IP address.
   *
   * @return string
   */
  public function getIpAddress() {
    return $this->ipAddress;
  }

  /**
   * Gets the login time.
   *
   * @return int
   */
  public function getLoginTime() {
    return $this->loginTime;
  }
}
