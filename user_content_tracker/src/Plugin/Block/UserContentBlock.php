<?php

namespace Drupal\user_content_tracker\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\views\Views;

/**
 * Provides a 'User Content' Block.
 *
 * @Block(
 *   id = "user_content_block",
 *   admin_label = @Translation("User Content Block")
 * )
 */
class UserContentBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_user = \Drupal::currentUser();
    // Load the view that lists content by the logged-in user.
    $view = Views::getView('user_content_view');
    if ($view) {
      $view->setDisplay('block_1');
      $view->setArguments([$current_user->id()]);
      $view->preExecute();
      $view->execute();
      return $view->render();
    }
    return [
      '#markup' => $this->t('No content found'),
    ];
  }

}
