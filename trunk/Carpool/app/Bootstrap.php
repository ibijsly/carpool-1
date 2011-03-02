<?php

/**
 * The bootstrap class handles all basic initializations
 * and auto-loading of classes.
 * 
 * This class should usually included in the beginning of
 * each public script or a job 
 * 
 * @author itay
 * 
 */

// Constants that do not need to be customized
define('STATUS_DONT_CARE', 0);
define('STATUS_LOOKING', 1);
define('STATUS_OFFERED', 2);

define('RIDE_INACTIVE', 0);
define('RIDE_ACTIVE', 1);

define('TIME_IRRELEVANT', 0);
define('TIME_DIFFERS', -1);

define('LOCATION_NOT_FOUND', -1);
define('LOCATION_DONT_CARE', -2);

define('SESSION_KEY_AUTH_USER', 'user');
define('SESSION_KEY_RUNNING', 'running');
define('SESSION_KEY_GLOBAL_MESSAGE', 'msg');

// Error reporting and assertions
if (ENV === ENV_DEVELOPMENT) {
	error_reporting(E_ALL | E_STRICT); 
	
	// Activate assert and make it quiet
    assert_options(ASSERT_ACTIVE, 1);
    assert_options(ASSERT_WARNING, 0);
    assert_options(ASSERT_QUIET_EVAL, 1);
    
    // Assert handler - log the failure
    function loggerAssertHandler($file, $line, $code) {
        err("Assertion Failed in $file, line $line");
    }
    
    // Set up the callback
    assert_options(ASSERT_CALLBACK, 'loggerAssertHandler');
    
} else {
	error_reporting(0);
	assert_options(ASSERT_ACTIVE, 0); 
}

// Simple auto loading:
//   View classes (name starts with View) - look in app/views
//   Standard classes - look in app/
function __autoload($className) {
	if (strncmp($className, 'View_', 5) === 0) {
		require_once APP_PATH . '/views/' . $className . '.php';
	} elseif (strncmp($className, 'Service_', 8) === 0) {
		require_once APP_PATH . '/services/' . $className . '.php';
	} else {
    	require_once APP_PATH . '/' . $className . '.php';
	}
}

// Global configuration
$globalConf = parse_ini_file(CONF_PATH . '/' . CONF_FILE, false);
if (!$globalConf) {
	// We can't really do anything without that
	die('Could not parse configuration file. Aborting.');
}

$GLOBALS['conf'] = $globalConf;

function getConfiguration($key, $default = false) {
    $globalConf = $GLOBALS['conf'];
    if (!isset($globalConf) || !isset($globalConf[$key])) {
        return $default;
    }
    return $globalConf[$key];
}

// Logger
$logger = null;
$logLevel = getConfiguration('log.level');

try {
    if ($logLevel < Logger::LOG_NONE) {
        $logger = new Logger(str_replace("~", BASE_PATH, getConfiguration('log.file')), $logLevel);
    }
} catch (Exception $e) {
    // Ignore
}

if ($logger == null) {
    $logger = new NullLogger();
}


$GLOBALS['logger'] = $logger;

function debug($str) {
    global $logger;
    $logger->doLog(Logger::LOG_DEBUG, $str);
}
 
function info($str) {
    global $logger;
    $logger->doLog(Logger::LOG_INFO, $str);
}
 
function warn($str) {
    global $logger;
    $logger->doLog(Logger::LOG_WARN, $str);
}
 
function err($str) {
    global $logger;
    $logger->doLog(Logger::LOG_ERR, $str);
}

function logException(Exception $e) {
    global $logger;
    $logger->logException($e);
}
 
// Locale
$localeManager = LocaleManager::getInstance();
$localeManager->init();

// Start session
AuthHandler::init();

info('Bootstrap done.');
