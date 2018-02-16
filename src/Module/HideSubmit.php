<?php

namespace Drupal\hide_submit\Module;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\user\UserInterface;

/**
 * Class HideSubmit.
 *
 * Custom functionality for the Hide Submit Button module.
 *
 * @package Drupal\hide_submit
 */
class HideSubmit {
  use StringTranslationTrait;

  const METHOD_NONE = 'none';

  /**
   * Singleton loader.
   *
   * @return static
   *   This module object.
   */
  public static function load() {
    static $instance;
    $instance = $instance ?: new static();
    return $instance;
  }

  /**
   * Get the config settings for this module.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   The Drupal config object for hide_submit.settings.
   */
  public static function config() {
    static $config;
    $config = $config ?: \Drupal::config('hide_submit.settings');
    return $config;
  }

  /**
   * Permissions wrapper.
   *
   * Determine if a given user may bypass the hide_submit implementation.
   *
   * @param UserInterface $account
   *   A user account to check.
   *
   * @return bool
   *   TRUE if the user my bypass the hide_submit settings.
   */
  public function userMayBypass(UserInterface $account = NULL) {
    $permission = 'bypass hide submit';

    if ($account) {
      return $account->hasPermission($permission);
    }
    else {
      return \Drupal::currentUser()->hasPermission($permission);
    }
  }

  /**
   * Settings wrapper.
   *
   * Determine if the functionality is active.
   *
   * @return bool
   *   TRUE if the hide_submit functionality should be applied.
   */
  public function isActive() {
    // Exclude hide_submit from Views UI paths to prevent issues.
    $exclude = [
      'admin/structure/views',
      'admin/structure/views/*',
    ];
    $patterns = implode(PHP_EOL, $exclude);
    $current_path = \Drupal::service('path.current')->getPath();
    $exclude_path = \Drupal::service('path.matcher')->matchPath($current_path, $patterns);

    return (bool) (!$exclude_path && (static::config()->get('method') != self::METHOD_NONE));
  }

  /**
   * Add the necessary JS settings.
   */
  public function getSettings() {
    $hide_submit_settings = drupal_static(__FUNCTION__, []);

    if (empty($hide_submit_settings)) {
      $hide_submit_settings = [
        'hide_submit_method' => static::config()->get('method'),
        'hide_submit_reset_time' => (int) static::config()->get('reset_time'),
        'hide_submit_css' => static::config()->get('disable.css'),
        'hide_submit_abtext' => $this->t(static::config()->get('disable.abtext')),
        'hide_submit_atext' => $this->t(static::config()->get('disable.atext')),
        'hide_submit_hide_text' => $this->t(static::config()->get('hide.hide_text')),
        'hide_submit_hide_fx' => static::config()->get('hide.hide_fx'),
        'hide_submit_hide_css' => static::config()->get('hide.hide_css'),
        'hide_submit_indicator_style' => static::config()->get('indicator.indicator_style'),
        'hide_submit_spinner_color' => static::config()->get('indicator.spinner_color'),
        'hide_submit_spinner_lines' => (int) static::config()->get('indicator.spinner_lines'),
      ];

      // Allow other modules to modify settings.
      \Drupal::moduleHandler()->alter('hide_submit', $hide_submit_settings);
    }

    return $hide_submit_settings;
  }

}
