<?php

namespace Drupal\custom_503\EventSubscriber;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
// Use the LoggerChannelFactoryInterface.
use Symfony\Component\HttpKernel\KernelEvents;

/**
 *
 */
class ExceptionSubscriber implements EventSubscriberInterface {

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The theme manager service.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * The logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Constructs the ExceptionSubscriber.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Theme\ThemeManagerInterface $themeManager
   *   The theme manager service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory service.
   */
  public function __construct(RendererInterface $renderer, ThemeManagerInterface $themeManager, LoggerChannelFactoryInterface $logger_factory) {
    $this->renderer = $renderer;
    $this->themeManager = $themeManager;
    // Use the logger factory to get the 'custom_503' channel.
    $this->logger = $logger_factory->get('custom_503');
  }

  /**
   * Responds to kernel exception events.
   */
  public function onException(ExceptionEvent $event) {
    // Log the fact that an exception event was caught.
    $this->logger->notice('Exception event triggered');

    $exception = $event->getThrowable();

    // Check if the exception is a 503 Service Unavailable exception.
    if ($exception instanceof ServiceUnavailableHttpException) {
      $this->logger->notice('ServiceUnavailableHttpException caught.');

      // Set up a custom 503 response.
      $build = [
        '#theme' => '503_error',
      ];

      // Log the fact that the custom response is being created.
      $this->logger->notice('Rendering 503 error page.');

      $html = $this->renderer->renderRoot($build);
      $response = new Response($html, 503);

      // Set the custom response.
      $event->setResponse($response);

      // Log that the response has been set.
      $this->logger->notice('503 response set successfully.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Return the exception event subscription.
    $events[KernelEvents::EXCEPTION][] = ['onException'];
    return $events;
  }

}
