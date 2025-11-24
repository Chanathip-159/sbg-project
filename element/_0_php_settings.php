<?php
error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set('memory_limit', "2048M");

ini_set('session.gc_maxlifetime', 14400); // server should keep session data for AT LEAST 4 hour
ini_set('session.cookie_lifetime', 14400);
ini_set('session.cache_expire', 14400);
session_set_cookie_params(14400); // each client should remember their session id for EXACTLY 4 hour

ini_set('session.cache_limiter','public');
session_cache_limiter(false);

# session define
session_start();
ob_start();
?>