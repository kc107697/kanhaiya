<?php

namespace Drupal\custom_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;

/**
 * Provides a block for User data.
 *
 * @Block(
 *   id = "user_data_block",
 *   admin_label = @Translation("User Data Block"),
 *   category = @Translation("Custom")
 * )
 */
class UserDataBlock extends BlockBase {

  public function build() {
    $current_user = \Drupal::currentUser();
    $user = User::load($current_user->id());

    $output = '<div><strong>User Name:</strong> ' . $user->getUsername() . '</div>';
    $output .= '<div><strong>Email:</strong> ' . $user->getEmail() . '</div>';

    return [
      '#markup' => $output,
    ];
  }
}
