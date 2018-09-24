<?php

define('MODULE_ROOT',  dirname(__FILE__) . '/../');
define('TMP_DIR',  MODULE_ROOT . 'tests/tmp');
define('RESOURCES_DIR',  MODULE_ROOT . 'tests/resources');
define('LOCAL_ONLY', TRUE);
define('ND_TEST_MODE', TRUE);

require_once MODULE_ROOT . 'vendor/autoload.php';
require_once MODULE_ROOT . 'tests/lib/TestDataLoader.php';


