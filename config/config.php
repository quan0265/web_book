<?php 

date_default_timezone_set('Asia/Ho_Chi_Minh');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(true);

if (!function_exists('isLocal')) {
	function isLocal () {
		$_file = __FILE__;
		if (strpos($_file, '\xampp\htdocs') !== false || strpos($_file, '\laragon') !== false) {
			return true;
		}
		return false;
	}
}

// define
if (strpos(__FILE__, 'xampp\htdocs') !== false || strpos(__FILE__, '\laragon') !== false) {
	define('root_domain', 'http://localhost:8000');
}
else {
	define('root_domain', 'https://interdogmedia.com');
}

define('ROOT_IMAGE', 'interdogmedia.com');
define('f_url', 'interdogmedia.com');

if (!defined('root_base')) {
	if (isLocal()) {
		define('root_base', '');
	}
	else {
		define('root_base', '');
	}
}
if (!defined('folder_base')) {
	if (strpos(__FILE__, '\xampp\htdocs') !== false || strpos(__FILE__, '\laragon') !== false) {
		define('folder_base', '');
	}
	else {
		define('folder_base', '/home/admin/domains/interdogmedia.com/public_html/');
	}
}

// define for ajax
$image_types = array('image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/webp');
if (!defined('image_types')) {
	define('image_types', array('image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'));
}
if (!defined('image_max_size')) {
	define('image_max_size', 5200000);
}
// if (!defined('image_max_width')) {
// 	define('image_max_width', 4000);
// }

// E2

if (strpos(__FILE__, '\xampp\htdocs') !== false || strpos(__FILE__, '\laragon') !== false) {
	// $team = 'test';
}

include 'LIB.php';
include 'functions_app.php';

class Config {
	static public $version = '0.09';
}











 ?>