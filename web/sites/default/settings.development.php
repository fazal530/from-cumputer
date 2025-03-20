<?php

/**
 * @file
 * Settings overrides that apply to every local development environment.
 */

use Drupal\Component\Assertion\Handle;

assert_options(ASSERT_ACTIVE, TRUE);
Handle::register();

// Enforce MailHog intervention.
$config['smtp.settings']['smtp_on'] = FALSE;
$config['mailsystem.settings']['defaults']['sender'] = 'php_mail';
$config['system.mail']['interface']['default'] = 'php_mail';
$config['symfony_mailer.settings']['default_transport'] = 'mailhog';

$settings['container_yamls'][] = __DIR__ . '/development.services.yml';
$config['system.logging']['error_level'] = 'verbose';
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['discovery_migration'] = 'cache.backend.memory';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['extension_discovery_scan_tests'] = TRUE;
$settings['rebuild_access'] = TRUE;
$settings['skip_permissions_hardening'] = TRUE;
