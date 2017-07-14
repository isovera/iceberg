<?php

namespace Iceberg\composer;

use Composer\Script\Event;
use Composer\IO\IOInterface;

/**
 * Project handler.
 */
class ProjectHandler {

  const DRUPAL_ROOT = 'docroot';
  const DRUPAL_CONFIG = 'config';

  /**
   * Drupal core directories.
   */
  const DRUPAL_DIRS = [
    'themes',
    'modules',
    'profiles',
  ];

  /**
   * Drupal project directories.
   */
  const DRUPAL_PROJECT_DIRS = [
    'config',
    'docroot',
    'patches',
  ];

  /**
   * Drupal project types.
   */
  const DRUPAL_PROJECT_TYPES = [
    'default',
    'commence'
  ];

  /**
   * Drupal sites default path.
   */
  const DRUPAL_SITES_DEFAULT = 'sites/default';

  /**
   * Create project.
   */
  public static function createProject(Event $event) {
    $io = $event->getIO();

    // Copy Drupal settings file.
    self::copyDrupalSettings($io);

    // Write Drupal settings file.
    self::writeDrupalSettings();

    // Create Drupal directories.
    self::createDrupalDirectories($io);

    // Create Project directories.
    self::createProjectDirectories($io);

    // Copy over Drupal module composer.json based type.
    $project_choices = self::DRUPAL_PROJECT_TYPES;

    $type_key = $io->select(
      'Project Type [default]:', $project_choices, 0
    );
    $type_name = $project_choices[$type_key];

    $status = self::copyDrupalModuleTemplate($type_name);

    if (!$status) {
      $io->writeError('Failed to copy module template.');
    }
  }

  /**
   * Copy Drupal module template.
   *
   * @param string $type
   *   The project type.
   *
   * @return bool
   *   Return TRUE if successfully copied over Drupal module template.
   */
  protected static function copyDrupalModuleTemplate($type) {
    $template_path = __DIR__ . '/templates';

    if (!file_exists("$template_path/$type.composer.json")) {
      return FALSE;
    }
    $current_path = getcwd();

    $status = copy(
      "$template_path/$type.composer.json",
      "$current_path/project-composer.json"
    );

    if (!$status) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Copy Drupal settings.
   *
   * @param IOInterface $io
   *   Composer IO object.
   */
  protected static function copyDrupalSettings(IOInterface $io) {
    $default_path = self::defaultPath();

    if (!file_exists("$default_path/settings.php")
      && file_exists("$default_path/default.settings.php")) {
      $status = copy(
        "$default_path/default.settings.php",
        "$default_path/settings.php"
      );

      if (!$status) {
        $io->writeError('Failed to copy settings file.');
      }
    }
  }

  /**
   * Create Drupal directories.
   *
   * @param IOInterface $io
   *   Composer IO object.
   */
  protected static function createDrupalDirectories(IOInterface $io) {
    foreach (self::DRUPAL_DIRS as $dir) {
      $path = self::DRUPAL_ROOT . "/$dir";

      if (file_exists($path)) {
        continue;
      }
      $status = mkdir($path, 0777, TRUE);

      if (!$status) {
        $io->writeError('Failed to create Drupal directory.');
      }
    }
  }

  /**
   * Create Project directories.
   *
   * @param IOInterface $io
   *   Composer IO object.
   */
  protected static function createProjectDirectories(IOInterface $io) {
    foreach (self::DRUPAL_PROJECT_DIRS as $dir) {
      $path = getcwd() . "/$dir";

      if (file_exists($path)) {
        continue;
      }
      $status = mkdir($path, 0777, TRUE);

      if (!$status) {
        $io->writeError('Failed to create Project directory.');
      }
    }
  }

  /**
   * Write Drupal settings configurations.
   */
  protected static function writeDrupalSettings() {
    $default_path = self::defaultPath();

    if (self::hasDrupal() && file_exists("$default_path/settings.php")) {
      require_once self::DRUPAL_ROOT . '/core/includes/bootstrap.inc';
      require_once self::DRUPAL_ROOT . '/core/includes/install.inc';

      $drupal_config_path = implode('/', [getcwd(), self::DRUPAL_CONFIG]);

      $settings['config_directories'] = [
        CONFIG_SYNC_DIRECTORY => (object) [
          'value' => $drupal_config_path,
          'required' => TRUE,
        ],
      ];

      drupal_rewrite_settings($settings, "$default_path/settings.php");
    }
  }

  /**
   * Has Drupal core installed.
   *
   * @return bool
   *   Return TRUE if Drupal has been installed in docroot; otherwise FALSE.
   */
  protected static function hasDrupal() {
    return file_exists(self::DRUPAL_ROOT . '/core');
  }

  /**
   * Drupal default path.
   *
   * @return string
   *   The path to the Drupal sites default directory.
   */
  protected static function defaultPath() {
    return implode(
      '/', [self::DRUPAL_ROOT, self::DRUPAL_SITES_DEFAULT]
    );
  }

}
