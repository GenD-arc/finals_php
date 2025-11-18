<?php
define('APP_NAME', 'Theater Seat System');
define('APP_VERSION', '2.0.0');
define('APP_ENV', 'development');


define('BASE_URL', 'http://localhost/theater-seat-system/public');


define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('MODELS_PATH', ROOT_PATH . '/models');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');


define('SESSION_LIFETIME', 3600); 

define('SEAT_ROWS', 10);
define('SEAT_COLS', 14);
define('SEAT_TOTAL', 120); 
define('RECORDS_RETENTION_DAYS', 30);

if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . '/logs/error.log');
}

date_default_timezone_set('Asia/Manila');