<?php
namespace Drupal\user_content_tracker\EventSubscriber;

use Drupal\Core\Database\Database;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\user_content_tracker\Event\UserCustomLoginEvent;

/**
 * Event Subscriber for user custom login events.
 */
class UserLoginSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      UserCustomLoginEvent::EVENT_NAME => 'onUserLogin',
    ];
  }

  /**
   * Responds to the user login event.
   *
   * @param \Drupal\user_content_tracker\Event\UserCustomLoginEvent $event
   *   The event object.
   */
  public function onUserLogin(UserCustomLoginEvent $event) {
    $user = $event->getUser();
    // $ip_address = $event->getIpAddress();
    $login_time = $event->getLoginTime();

    // Insert the login details into the custom table.
    $database = \Drupal::database();
    $database->insert('user_login_details')
      ->fields([
        'uid' => $user->id(),
        'login_time' => $login_time,
      ])
      ->execute();
  }
}