<?php
return array (
  'backend' =>
  array (
    'frontName' => '{MAGENTO_ADMIN_URL}',
  ),
  'crypt' =>
  array (
    'key' => '{MAGENTO_ENCRYPTION_KEY}',
  ),
  'session' =>
  array (
    'save' => 'redis',
    'redis' =>
    array (
      'host' => 'nr_redis',
      'port' => '6379',
      'password' => '',
      'timeout' => '2.5',
      'persistent_identifier' => '',
      'database' => '1',
      'compression_threshold' => '2048',
      'compression_library' => 'gzip',
      'log_level' => '1',
      'max_concurrency' => '6',
      'break_after_frontend' => '5',
      'break_after_adminhtml' => '30',
      'first_lifetime' => '600',
      'bot_first_lifetime' => '60',
      'bot_lifetime' => '7200',
      'disable_locking' => '0',
      'min_lifetime' => '60',
      'max_lifetime' => '2592000',
    ),
  ),
  'db' =>
  array (
    'table_prefix' => '{MAGENTO_DB_PREFIX}',
    'connection' =>
    array (
      'default' =>
      array (
        'host' => '{DB_HOST}',
        'dbname' => '{MAGENTO_DB_DATABASE}',
        'username' => '{DB_USERNAME}',
        'password' => '{DB_PASSWORD}',
        'active' => '1',
      ),
    ),
  ),
  'resource' =>
  array (
    'default_setup' =>
    array (
      'connection' => 'default',
    ),
  ),
  'cache_types' =>
  array (
    'config' => 1,
    'layout' => 1,
    'block_html' => 1,
    'collections' => 1,
    'reflection' => 1,
    'db_ddl' => 1,
    'eav' => 1,
    'customer_notification' => 1,
    'full_page' => 1,
    'config_integration' => 1,
    'config_integration_api' => 1,
    'translate' => 1,
    'config_webservice' => 1,
    'compiled_config' => 1,
  ),
  'install' =>
  array (
    'date' => '{MAGENTO_INSTALL_DATE}',
  ),
  'cache' =>
  array (
    'frontend' =>
    array (
      'default' =>
      array (
        'backend' => 'Cm_Cache_Backend_Redis',
        'backend_options' =>
        array (
          'server' => 'redis',
          'database' => '2',
          'port' => '6379',
        ),
      ),
      'page_cache' =>
      array (
        'backend' => 'Cm_Cache_Backend_Redis',
        'backend_options' =>
        array (
          'server' => 'nr_redis',
          'port' => '6379',
          'database' => '3',
          'compress_data' => '1',
        ),
      ),
    ),
  ),
  'MAGE_MODE' => 'production',
  'x-frame-options' => 'SAMEORIGIN',
  'http_cache_hosts' =>
  array (
    0 =>
    array (
      'host' => 'nginx-proxy',
    ),
  ),
);

