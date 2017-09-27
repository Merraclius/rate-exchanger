<?php
/**
 * This file is run before all codeception tests
 */

require(realpath(dirname(dirname(dirname(__FILE__)))) . '/helpers/DockerEnv.php');
\DockerEnv::init();

defined('YII_DEBUG') or define('YII_DEBUG', true);

defined('YII_TEST_ENTRY_URL') or define('YII_TEST_ENTRY_URL', parse_url(\Codeception\Configuration::config()['config']['test_entry_url'], PHP_URL_PATH));
defined('YII_TEST_ENTRY_FILE') or define('YII_TEST_ENTRY_FILE', \DockerEnv::APP_DIR . 'web/index.php');

$_SERVER['SCRIPT_FILENAME'] = YII_TEST_ENTRY_FILE;
$_SERVER['SCRIPT_NAME'] = YII_TEST_ENTRY_URL;
$_SERVER['SERVER_NAME'] = parse_url(\Codeception\Configuration::config()['config']['test_entry_url'], PHP_URL_HOST);
$_SERVER['SERVER_PORT'] = parse_url(\Codeception\Configuration::config()['config']['test_entry_url'], PHP_URL_PORT) ?: '80';

Yii::setAlias('@tests', \DockerEnv::TEST_DIR);
Yii::setAlias('@app', \DockerEnv::APP_DIR);
