<?php

// version 1.1

/**
 * Convert String to Array
 * @param type $str
 * @param type $delimiter
 * @return type
 */
if (!function_exists('strToArr')) {

    function strToArr($str, $delimiter = ",") {
        $returns = array();
        $arr = explode($delimiter, $str);
        if ($arr) {
            foreach (array_unique($arr) as $value) {
                $_value = strtolower(trim($value));
                if ($_value && !in_array($_value, $returns)) {
                    $returns[] = $_value;
                }
            }
        }
        return $returns;
    }

}

/**
 * Get Client IP
 * @return type
 */
if (!function_exists('getClientIp')) {

    function getClientIp() {
		$ip = null;
		if (isLocal()) {
			$ip = '::1';
		}
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

}

/**
 * 
 * @param type $string
 * @param type $length
 * @return type
 */
if (!function_exists('truncate')) {

    function truncate($string, $length = 80) {
        $strlen = strlen(remove_unicode($string));
        if ($strlen <= $length) {
            return $string;
        } else {
            mb_internal_encoding("UTF-8");
            return mb_substr($string, 0, $length) . "...";
        }
    }

}

/**
 * 
 * @param type $url
 * @param type $timeout
 * @param type $referer
 * @param type $USERAGENT
 * @return type
 */
if (!function_exists('curlGet')) {

    function curlGet($url, $headers=array(), $timeout = 3600, $referer = false, $USERAGENT = false, $proxy=null, $user_pass=null) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		if ($USERAGENT) {
			curl_setopt($curl, CURLOPT_USERAGENT, $USERAGENT);
		}
		if ($referer) {
			curl_setopt($curl, CURLOPT_REFERER, $referer);
		}
		if ($proxy && $user_pass) {
			curl_setopt($curl, CURLOPT_PROXY, $proxy);
			curl_setopt($curl, CURLOPT_PROXYUSERPWD, $user_pass);
		}
	
		curl_setopt($curl, CURLOPT_ENCODING, '');
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$dataReturn = curl_exec($curl);
		curl_close($curl);
		return $dataReturn;
	}

}

/**
 * 
 * @param type $url
 * @param type $field
 * @param type $timeout
 * @param type $referer
 * @param type $USERAGENT
 * @return type
 */
if (!function_exists('curlPost')) {

    function curlPost($url, $field = array(), $headers=array(), $timeout = 3600, $referer = false, $USERAGENT = false) {
        $post = $field ? http_build_query($field) : '';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if ($USERAGENT) {
            curl_setopt($curl, CURLOPT_USERAGENT, $USERAGENT);
        }
        if ($referer) {
            curl_setopt($curl, CURLOPT_REFERER, $referer);
        }
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $dataReturn = curl_exec($curl);
        curl_close($curl);
        return $dataReturn;
    }

}

/**
 * Redirect
 * @param type $url
 */
if (!function_exists('redirect')) {

    function redirect($url) {
        header("Location: $url");
        exit();
    }

}

/**
 * Get Current url
 * @return string
 */
if (!function_exists('current_url')) {

    function current_url() {
        $protocol = getProtocol();
        $pageURL = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        return $pageURL;
    }

}

/**
 * getProtocol
 * @return type
 */
if (!function_exists('getProtocol')) {

    function getProtocol() {
        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $isSecure = true;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $isSecure = true;
        } elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
            $isSecure = true;
        }
        return $isSecure ? 'https://' : 'http://';
    }

}

/**
 * 
 * @param type $text
 * @return type
 */
if (!function_exists('build_slug')) {

    function build_slug($text) {

        $text = htmlspecialchars(trim(strip_tags($text)));

        $CLEAN_URL_REGEX = '*([\s$+,/:=\?@"\'<>%{}|\\^~[\]`\r\n\t\x00-\x1f\x7f]|(?(?<!&)#|#(?![0-9]+;))|&(?!#[0-9]+;)|(?<!&#\d|&#\d{2}|&#\d{3}|&#\d{4}|&#\d{5});)*s';
        $text = preg_replace($CLEAN_URL_REGEX, '-', strip_tags($text));
        $text = trim(preg_replace('#-+#', '-', $text), '-');

        $code_entities_match = array('\\', '&quot;', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '{', '}', '|', ':', '"', '<', '>', '?', '[', ']', '', ';', "'", ',', '.', '_', '/', '*', '+', '~', '`', '=', ' ', '---', '--', '--');
        $code_entities_replace = array('', '', '-', '-', '', '', '', '-', '-', '', '', '', '', '', '', '', '-', '', '', '', '', '', '', '', '', '', '-', '', '-', '-', '', '', '', '', '', '-', '-', '-', '-');
        $text = str_replace($code_entities_match, $code_entities_replace, $text);

        $chars = array("a", "A", "e", "E", "o", "O", "u", "U", "i", "I", "d", "D", "y", "Y");
        $uni [0] = array("á", "à", "ạ", "ả", "ã", "â", "ấ", "ầ", "ậ", "ẩ", "ẫ", "ă", "ắ", "ằ", "ặ", "ẳ", "� �", "ả", "á", "ặ");
        $uni [1] = array("Á", "À", "Ạ", "Ả", "Ã", "Â", "Ấ", "Ầ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ắ", "Ằ", "Ặ", "Ẳ", "� �");
        $uni [2] = array("é", "è", "ẹ", "ẻ", "ẽ", "ê", "ế", "ề", "ệ", "ể", "ễ", "ệ");
        $uni [3] = array("É", "È", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ế", "Ề", "Ệ", "Ể", "Ễ");
        $uni [4] = array("ó", "ò", "ọ", "ỏ", "õ", "ô", "ố", "ồ", "ộ", "ổ", "ỗ", "ơ", "ớ", "ờ", "ợ", "ở", "� �");
        $uni [5] = array("Ó", "Ò", "Ọ", "Ỏ", "Õ", "Ô", "Ố", "Ồ", "Ộ", "Ổ", "Ỗ", "Ơ", "Ớ", "Ờ", "Ợ", "Ở", "� �");
        $uni [6] = array("ú", "ù", "ụ", "ủ", "ũ", "ư", "ứ", "ừ", "ự", "ử", "ữ", "ù");
        $uni [7] = array("Ú", "Ù", "Ụ", "Ủ", "Ũ", "Ư", "Ứ", "Ừ", "Ự", "Ử", "Ữ");
        $uni [8] = array("í", "ì", "ị", "ỉ", "ĩ");
        $uni [9] = array("Í", "Ì", "Ị", "Ỉ", "Ĩ");
        $uni [10] = array("đ");
        $uni [11] = array("Đ");
        $uni [12] = array("ý", "ỳ", "ỵ", "ỷ", "ỹ");
        $uni [13] = array("Ý", "Ỳ", "Ỵ", "Ỷ", "Ỹ");

        for ($i = 0; $i <= 13; $i++) {
            $text = str_replace($uni[$i], $chars[$i], $text);
        }

        $characters = '0123456789abcdefghijklmnopqrstuvwxyz-';
        $textReturn = '';
        for ($i = 0; $i <= strlen($text); $i++) {
            if (isset($text[$i])) {
                //$text[$i] = strtolower($text[$i]);
                if (preg_match("/{$text[$i]}/i", $characters)) {
                    $textReturn .= $text[$i];
                }
            }
        }

        $textReturn = strtolower($textReturn);
        return $textReturn;
    }

}


/**
 * 
 * @param type $_text
 * @return type
 */
if (!function_exists('remove_unicode')) {

    function remove_unicode($_text) {
        $text = htmlspecialchars(trim(strip_tags($_text)));
        $chars = array("a", "A", "e", "E", "o", "O", "u", "U", "i", "I", "d", "D", "y", "Y");
        $uni [0] = array("á", "à", "ạ", "ả", "ã", "â", "ấ", "ầ", "ậ", "ẩ", "ẫ", "ă", "ắ", "ằ", "ặ", "ẳ", "� �", "ả", "á", "ặ");
        $uni [1] = array("Á", "À", "Ạ", "Ả", "Ã", "Â", "Ấ", "Ầ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ắ", "Ằ", "Ặ", "Ẳ", "� �", "Ạ", "Á", "À", "Ã", "Ả");
        $uni [2] = array("é", "è", "ẹ", "ẻ", "ẽ", "ê", "ế", "ề", "ệ", "ể", "ễ", "ệ");
        $uni [3] = array("É", "È", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ế", "Ề", "Ệ", "Ể", "Ễ", "É", "Ẽ");
        $uni [4] = array("ó", "ò", "ọ", "ỏ", "õ", "ô", "ố", "ồ", "ộ", "ổ", "ỗ", "ơ", "ớ", "ờ", "ợ", "ở", "� �");
        $uni [5] = array("Ó", "Ò", "Ọ", "Ỏ", "Õ", "Ô", "Ố", "Ồ", "Ộ", "Ổ", "Ỗ", "Ơ", "Ớ", "Ờ", "Ợ", "Ở", "� �", "Ọ", "Õ");
        $uni [6] = array("ú", "ù", "ụ", "ủ", "ũ", "ư", "ứ", "ừ", "ự", "ử", "ữ", "ù");
        $uni [7] = array("Ú", "Ù", "Ụ", "Ủ", "Ũ", "Ư", "Ứ", "Ừ", "Ự", "Ử", "Ữ", "Ú", "Ũ");
        $uni [8] = array("í", "ì", "ị", "ỉ", "ĩ");
        $uni [9] = array("Í", "Ì", "Ị", "Ỉ", "Ĩ", "Ỉ", "Ì", "Ĩ", "Í", "Ị");
        $uni [10] = array("đ");
        $uni [11] = array("Đ");
        $uni [12] = array("ý", "ỳ", "ỵ", "ỷ", "ỹ");
        $uni [13] = array("Ý", "Ỳ", "Ỵ", "Ỷ", "Ỹ");
        for ($i = 0; $i <= 13; $i++) {
            $text = str_replace($uni[$i], $chars[$i], $text);
        }
        return $text;
    }

}

if (!function_exists('isLocal')) {
	function isLocal () {
		$_file = __FILE__;
		if (strpos($_file, '\xampp\htdocs') !== false || strpos($_file, '\laragon') !== false) {
			return true;
		}
		return false;
	}
}

if (!function_exists('isDomain')) {
	function isDomain($url) {
		$url = trim($url);
	    $pattern = '/^(?:https?:\/\/)?(?:www\.)?([a-zA-Z0-9-]+)\.([a-zA-Z]{2,})(?:\/.*)?$/';
	    $rs = preg_match($pattern, $url);
	    if (empty($rs)) {
	    	$pattern = '/^(?:https?:\/\/)?(?:www\.)?([a-zA-Z0-9-]+)\.([a-zA-Z0-9-]+)\.([a-zA-Z]{2,})(?:\/.*)?$/';
		    $rs = preg_match($pattern, $url);
	    }
	    if (empty($rs)) {
	    	$pattern = '/^(?:https?:\/\/)?(?:www\.)?([a-zA-Z0-9-]+)\.([a-zA-Z0-9-]+)\.([a-zA-Z0-9-]+)\.([a-zA-Z]{2,})(?:\/.*)?$/';
		    $rs = preg_match($pattern, $url);
	    }
	    if (empty($rs)) {
	    	$rs = '';
	    }
	    return $rs;
	}
}

if (!function_exists('isDomainAuto')) {
	function isDomainAuto($domain, $http = false) {
	    $domain = trim($domain);
	    $domain = preg_replace('/\?.*/', '', $domain);
	    $domain = preg_replace('/\/$/', '', $domain);
	    $domainPattern = '/^(https?:\/\/)?(www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

	    if (preg_match($domainPattern, $domain)) {
	        $domain = str_replace(array('http://www.', 'http://', 'https://www.', 'https://'), '', $domain);

	        if ($http) {
	            if (preg_match('/^http/', $domain)) {
	                return $domain;
	            } else {
	                return 'https://' . $domain;
	            }
	        } else {
	            return $domain;
	        }
	    }
	    return false;
	}
}


if (!function_exists('getDomain')) {
	function getDomain($url) {
		// return abc.com
		$url = trim($url);
		$url = preg_replace('/.*\/\/www./', '', $url);
		$url = preg_replace('/.*:\/\//', '', $url);
		$url = preg_replace('/#.*$/', '', $url);
		$url = preg_replace('/\?.*$/', '', $url);
		$url = preg_replace('/^\./', '', $url);
		$url = preg_replace('/\.$/', '', $url);
		$url = preg_replace('/\/.*/', '', $url);
		if (!preg_match('/\./', $url)) {
			return null;
		}
		$cc_ltd = [".ac",".ad",".ae",".af",".ag",".ai",".al",".am",".ao",".aq",".ar",".as",".at",".au",".aw",".ax",".az",".ba",".bb",".bd",".be",".bf",".bg",".bh",".bi",".bj",".bm",".bn",".bo",".br",".bs",".bt",".bw",".by",".bz",".ca",".cc",".cd",".cf",".cg",".ch",".ci",".ck",".cl",".cm",".cn",".co",".cr",".cu",".cv",".cw",".cx",".cy",".cz",".de",".dj",".dk",".dm",".do",".dz",".ec",".ee",".eg",".er",".es",".et",".eu",".fi",".fj",".fk",".fm",".fo",".fr",".ga",".gd",".ge",".gf",".gg",".gh",".gi",".gl",".gm",".gn",".gp",".gq",".gr",".gs",".gt",".gu",".gw",".gy",".hk",".hm",".hn",".hr",".ht",".hu",".id",".ie",".il",".im",".in",".io",".iq",".ir",".is",".it",".je",".jm",".jo",".jp",".ke",".kg",".kh",".ki",".km",".kn",".kp",".kr",".kw",".ky",".kz",".la",".lb",".lc",".li",".lk",".lr",".ls",".lt",".lu",".lv",".ly",".ma",".mc",".md",".me",".mg",".mh",".mk",".ml",".mm",".mn",".mo",".mp",".mq",".mr",".ms",".mt",".mu",".mv",".mw",".mx",".my",".mz",".na",".nc",".ne",".nf",".ng",".ni",".nl",".no",".np",".nr",".nu",".nz",".om",".pa",".pe",".pf",".pg",".ph",".pk",".pl",".pm",".pn",".pr",".ps",".pt",".pw",".py",".qa",".re",".ro",".rs",".ru",".rw",".sa",".sb",".sc",".sd",".se",".sg",".sh",".si",".sk",".sl",".sm",".sn",".so",".sr",".ss",".st",".sv",".sx",".sy",".sz",".tc",".td",".tf",".tg",".th",".tj",".tk",".tl",".tm",".tn",".to",".tr",".tt",".tv",".tw",".tz",".ua",".ug",".uk",".us",".uy",".uz",".va",".vc",".ve",".vg",".vi",".vn",".vu",".wf",".ws",".ye",".yt",".za",".zm",".zw"];
		$arr = explode('.', $url);
		if (count($arr) >= 3) {
			$url = $arr[count($arr) - 3] . '.' . $arr[count($arr) - 2] . '.' . $arr[count($arr) - 1];
		}
		$arr = explode('.', $url);
		if (count($arr) >= 3) {
			$last_ltd = $arr[count($arr) - 1];
			if (!in_array('.' . $last_ltd, $cc_ltd)) {
				$url = $arr[count($arr) - 2] . '.' . $arr[count($arr) - 1];
			}
		}
		return $url;
	}
	
}

if (!function_exists('getSiteName')) {
	function getSiteName($url) {
		$url = trim($url);
	    $pattern = '/^(?:https?:\/\/)?(?:www\.)?([a-zA-Z0-9-]+)\.([a-zA-Z]{2,})(?:\/.*)?$/';
	    $rs = preg_match($pattern, $url);
	    if (empty($rs)) {
	    	$pattern = '/^(?:https?:\/\/)?(?:www\.)?([a-zA-Z0-9-]+)\.([a-zA-Z0-9-]+)\.([a-zA-Z]{2,})(?:\/.*)?$/';
		    $rs = preg_match($pattern, $url);
	    }
	    if (empty($rs)) {
	    	$pattern = '/^(?:https?:\/\/)?(?:www\.)?([a-zA-Z0-9-]+)\.([a-zA-Z0-9-]+)\.([a-zA-Z0-9-]+)\.([a-zA-Z]{2,})(?:\/.*)?$/';
		    $rs = preg_match($pattern, $url);
	    }
	    if (empty($rs)) {
	    	$rs = '';
	    }
	    else {
	    	$url = str_replace('https://www.', '', $url);
	    	$url = str_replace('https://', '', $url);
	    	$url = str_replace('http://', '', $url);
	    	$url = preg_replace('/^\//', '', $url);
	    	$url = preg_replace('/^\//', '', $url);
	    	$url = explode('/', $url)[0];
	    	$arr = ['facebook.com', 'linkedin.com'];
	    	if (in_array(strtolower($url), $arr)) {
	    		$url = '';
	    	}
	    	$rs = $url;
	    }
	    return $rs;
	}
}

if (!function_exists('getDomainName')) {
	function getDomainName($url) {
		$url = preg_replace('/https:\/\/www./', '', $url);
		$url = preg_replace('/https:\/\//', '', $url);
		$url = preg_replace('/http:\/\//', '', $url);
		$url = preg_replace('/\?.*/', '', $url);
		$url = preg_replace('/\#.*/', '', $url);
		$url = preg_replace('/\/.*/', '', $url);
		return $url;
	}
}

if (!function_exists('encodeToUnicodeEscape')) {
	function encodeToUnicodeEscape($string) {
	    $encodedString = '';
	    $length = strlen($string);
	    for ($i = 0; $i < $length; $i++) {
	        $char = $string[$i];
	        $asciiCode = ord($char);
	        $encodedChar = '\x' . dechex($asciiCode);
	        $encodedString .= $encodedChar;
	    }
	    return $encodedString;
	}
}

function isRegx($pattern) {
    try {
        return @preg_match($pattern, '') !== false;
    } catch (ErrorException $e) {
        return false;
    }
}


// ===== Edit =====
if (!function_exists('textSummary1')) {
    function textSummary1($text, $length=140) {
        $text = trim($text);
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length - 3) . '...';
    }
}

if (!function_exists('textSummary')) {
    function textSummary($text, $length=150) {
        $text = trim($text);
        if (strlen($text) <= $length) {
            return $text;
        }
        $text = substr($text, 0, $length - 2);
        $arr = explode(' ', $text);
        array_pop($arr);
        $rs = trim(implode(' ', $arr));
        if (substr($rs, -1) == '.') {
            return $rs . '..';
        }
        else {
            return $rs . '...';
        }
    }
}

if (!function_exists('dateFormat')) {
    function dateFormat($time=NULL, $format='d-m-Y') {
        if ($time === NULL || $time == '' || $time == '0000-00-00' || $time == '0000-00-00 00:00:00') {
            return '';
        }
        return date($format, strtotime($time));
    }
}

if (!function_exists('timeFormat')) {
    function timeFormat($time=NULL, $format='H:i:s d-m-Y') {
        if ($time === NULL || $time == '0000-00-00' || $time == '0000-00-00 00:00:00') {
            return '';
        }
        return date($format, strtotime($time));
    }
}

if (!function_exists('getDayAgo')) {
    function getDayAgo($time) {
        if ($time == '0000-00-00 00:00:00' || $time == '') {
            return 0;
        }
        if (strpos($time, ':') !== false) {
            $time = strtotime($time);
        }
        $time_range = time() - $time;
        return (int)floor($time_range / (3600*24));
    }
}

if (!function_exists('getdateFromRange')) {
	function getdateFromRange($range) {
		$range = trim($range);
		$date_start = date('Y-m-d', time() - 29 * 24 * 3600);
		$date_end = date('Y-m-d');
		if (!empty($range)) {
			if (preg_match('/ - /', $range)) {
				[$date_start, $date_end] = explode(' - ', $range);
			    $date_start = str_replace('/', '-', $date_start);
			    $date_end = str_replace('/', '-', $date_end);
			}
			else if (preg_match('/^- /', $range)) {
				$date_end = preg_replace('/^- /', '', $range);
				$date_end = date('Y-m-d', strtotime($date_end));
			}
		}
		$date_start = date('Y-m-d', strtotime($date_start));
	    $date_end = date('Y-m-d', strtotime($date_end));
	    if (strtotime($date_start) > strtotime($date_end)) {
	    	$x = $date_start;
	    	$date_start = $date_end;
	    	$date_end = $x;
	    }
		if (strtotime($date_start) > time()) {
			$date_start = date('Y-m-d', time() - 29 * 24 * 3600);
		}
		if (strtotime($date_end) > time()) {
			$date_end = date('Y-m-d');
		}
		return [$date_start, $date_end];
	}
}

if (!function_exists('getStar')) {
    function getStar($rating=5) {
        if ($rating < 1 || $rating > 5) {
            $rating = 5;
        }
        $rating_floor = floor($rating);
        $rating_point = $rating - $rating_floor;
        $star_half = 0;
        if ($rating_point < 0.25) {
            $rating = $rating_floor;
        }
        else if ($rating_point >= 0.25 && $rating_point < 0.75) {
            $rating = $rating_floor + 0.5;
            $star_half = $rating_floor + 1;
        }
        else {
            $rating = $rating_floor + 1;
        }
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($star_half > 0 && $star_half == $i) {
                $html .= '<i class="fas fa-star-half-alt"></i>';
            }
            else if ($i <= $rating) {
                $html .= '<i class="fa fa-star"></i>';
            }
            else {
                $html .= '<i class="far fa-star"></i>';
            }
        }
        return $html;
    }
}

if (!function_exists('getRootUrl')) {
    function getRootUrl() {
        $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'? "https://" : "http://";
        return $http . $_SERVER["HTTP_HOST"];
    }
}

if (!function_exists('getRootPath')) {
    function getRootPath () {
        $root_path = '';
        if (!empty($_SERVER['SCRIPT_NAME'])) {
            $arr = explode('/', $_SERVER['SCRIPT_NAME']);
            $count = count($arr) - 2;
            for ($i=1; $i<=$count; $i++) {
                $root_path .= '../';
            }
        }
        return $root_path;
    }
}

if (!function_exists('getFilePath')) {
    function getFilePath () {
        $file_path = getRootPath();
        if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] != 'localhost') {
            $file_path = $file_path . '../files.dandautu.vn/public_html/';
        }
        else {
            $file_path = $file_path . 'files';
        }
        return $file_path;
    }
}

if (!function_exists('getLogo')) {
    function getLogo($file_name) {
        if (strpos($file_name, 'https://') !== false) {
            return $file_name;
        }
        if (empty($file_name)) {
            return getRootUrl() . '/assets/images/user.png';
        }
        return getRootUrl() . '/public/images/users/' . $file_name;
    }
}

if (!function_exists('htmlActive')) {
    function htmlActive($status, $rule='1') {
        $disabled = '';
        if ($rule != '1') {
            $disabled = 'disabled';
        }
        if ($status == 1) {
            return "<span class='badge bg-success btn_active' $disabled>Hiển thị</span>";
        }
        else {
            return "<span class='badge bg-danger btn_active' $disabled>Ẩn</span>"; 
        }
    }
}

if (!function_exists('getAlert')) {
    function getAlert() {
        if (!empty($_SESSION['error'])) {
            $error_text = $_SESSION['error'];
            echo '<button class="alert alert-danger">'.$error_text.'</button>';
            unset($_SESSION['error']);
        }
        if (!empty($_SESSION['success'])) {
            $success_text = $_SESSION['success'];
            echo '<button class="alert alert-success">'.$success_text.'</button>';
            unset($_SESSION['error']);
        }
    }
}

if (!function_exists('isEmail')) {
    function isEmail($email) {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('generateCouponCode')) {
    function generateCouponCode($length = 6) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $couponCode = '';
        $max = strlen($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $couponCode .= $characters[mt_rand(0, $max)];
        }
        return $couponCode;
    }
}

if (!function_exists('spinText')) {
	// '{Hi|Hello}' -> Hi or Hello
	function spinText($text) {
	    $regex = '/{([^{}]+?)}/';
	    while (preg_match($regex, $text, $matches)) {
	        $options = explode('|', $matches[1]);
	        $random = mt_rand(0, count($options) - 1);
	        $text = preg_replace($regex, $options[$random], $text, 1);
	    }
	    return $text;
	}
}

if (!function_exists('textToLink')) {
	function textToLink($content) {
		$content = preg_replace(
	        array(
	            '~(\s|^)(www\..+?)(\s|$)~im', 
	            '~(\s|^)(https?://)(.+?)(\s|$)~im', 
	        ),
	        array(
	            '$1http://$2$3', 
	            '$1<a href="$2$3" target="_blank">$3</a>$4', 
	        ),
	        $content
	    );
	    return $content;
	}
}

if (!function_exists('getNameFromEmail')) {
	function getNameFromEmail($email, $domain='') {
		if (!preg_match('/@/', $email)) {
			return '';
		}
		$not_names = ['info', 'contact', 'support', 'service', 'us', 'privacy', 'admin', 'to', 'pr', 'hr', 'legal', 'media', 'ads'];
		$not_address = ['yahoo.com', 'gmail.com'];
		$name = explode('@', $email)[0];
		$address = explode('@', $email)[1];

		$names = explode('.', $name);
		$rs = '';
		foreach ($names as $n) {
			if (!in_array($n, $not_names) && strlen($n) >= 4) {
				$rs = $n;
			}
		}

		if (!$rs) {
			if (in_array($address, $not_address)) {
				$rs = $name;
			}
			else {
				$addresses = explode('.', $address);
				foreach ($addresses as $item) {
					if (!in_array($item, $not_names) && strlen($item) >= 4) {
						$rs = $item;
					}
				}
				if (!$rs) {
					$rs = $address;
				}
			}
		}

		if (!$rs) {
			$rs = $name;
		}

		// $name = str_replace('_', ' ', $name);
		// $name = str_replace('-', ' ', $name);
		// $name = str_replace('+', ' ', $name);
		// $name = preg_replace('/[0-9]/', '', $name);
		return trim($rs);
	}
}

// if (!function_exists('markdownToHtml')) {
// 	function markdownToHtml($text) {
// 		if (preg_match('/<p/', $text) || preg_match('/<li/', $text) || preg_match('/<br/', $text)) {
// 			return $text;
// 		}
// 		$parser = new Parsedown();
// 		return $parser->text($text);

// 		// Replace headers
// 		$text = preg_replace('/^#\s*(.*)$/m', '<h1>$1</h1>', $text);
// 		$text = preg_replace('/^##\s*(.*)$/m', '<h2>$1</h2>', $text);
// 		$text = preg_replace('/^###\s*(.*)$/m', '<h3>$1</h3>', $text);
// 		$text = preg_replace('/^####\s*(.*)$/m', '<h4>$1</h4>', $text);
// 		$text = preg_replace('/^#####\s*(.*)$/m', '<h5>$1</h5>', $text);
// 		$text = preg_replace('/^######\s*(.*)$/m', '<h6>$1</h6>', $text);
// 		// Replace bold and italic
// 		$text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
// 		$text = preg_replace('/__(.*?)__/', '<strong>$1</strong>', $text); // Định dạng bold
// 		$text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
// 		$text = preg_replace('/_(.*?)_/', '<em>$1</em>', $text); // Định dạng italic
// 		// Replace inline code
// 		$text = preg_replace('/`(.*?)`/', '<code>$1</code>', $text);

// 		// $text = preg_replace('/\*   (.*)\n/', '<li>$1</li>', $text);
// 		return $text;
// 	}
// }

if (!function_exists('htmlToMarkdown')) {
    function htmlToMarkdown($text) {
        // Replace headers
        $text = preg_replace('/<h1>(.*?)<\/h1>/', '# $1', $text);
        $text = preg_replace('/<h2>(.*?)<\/h2>/', '## $1', $text);
        $text = preg_replace('/<h3>(.*?)<\/h3>/', '### $1', $text);
        $text = preg_replace('/<h4>(.*?)<\/h4>/', '#### $1', $text);
        $text = preg_replace('/<h5>(.*?)<\/h5>/', '##### $1', $text);
        $text = preg_replace('/<h6>(.*?)<\/h6>/', '###### $1', $text);
        // Replace bold and italic
        $text = preg_replace('/<strong>(.*?)<\/strong>/', '**$1**', $text);
        $text = preg_replace('/<em>(.*?)<\/em>/', '*$1*', $text);
        // Replace inline code
        $text = preg_replace('/<code>(.*?)<\/code>/', '`$1`', $text);
        return $text;
    }
}


if (!function_exists('textToFloat')) {
	function textToFloat($text) {
	    $x = 1;
	    if (preg_match('/^-/', $text)) {
	        $x = -1;
	    }
	    $text = preg_replace('/[^\d]+$/', '', $text);
	    $text = preg_replace('/^[^\d]+/', '', $text);
	    $arr_char = array();
	    for ($i = 0; $i < strlen($text); $i++) {
	        if (preg_match('/[^\d]/', $text[$i])) {
	            if (!in_array($text[$i], $arr_char)) {
	                array_push($arr_char, $text[$i]);
	            }
	        }
	    }
	    if (count($arr_char) == 1 && preg_match('/[^\d]\d{3}$/', $text)) {
	        // Không có phần thập phân, chỉ có một dạng ký tự
	        $text = preg_replace('/[^\d]/', '', $text);
	        return $x * floatval($text);
	    }

	    $text = preg_replace('/[^\d]/', '.', $text);
	    $text = preg_replace('/[^\d]$/', '', $text);
	    $arr = explode('.', $text);
	    if (count($arr) === 1) {
	        return $x * floatval($text);
	    }

	    $text = implode('', array_map(function ($item, $index) use ($arr) {
	        return $index === count($arr) - 1 ? '.' . $item : $item;
	    }, $arr, array_keys($arr)));

	    return $x * floatval($text);
	}
}

if (!function_exists('writeFile')) {
    function writeFile($content, $name='') {
        if ($name == '') {
            $name = basename($_SERVER['PHP_SELF'], '.php');
            $name += '.txt';
        }
        $file_name = $name;
        $file = fopen($file_name, 'w');
        fwrite($file, $content);
        fclose($file);
        chmod($file_name, 0777);
        return true;
    }
}

if (!function_exists('toAgoShort')) {
	function toAgoShort($time) {
		$seconds = time() - $time;
		$interval = abs(floor($seconds / (365 * 86400)));
		if ($interval >= 1)  return $interval . "y";

		$interval = floor($seconds / 2592000);
		if ($interval >= 1)  return $interval . "mn";

		$interval = floor($seconds / 86400);
		if ($interval >= 1)  return $interval . "d";

		$interval = floor($seconds / 3600);
		if ($interval >= 1)  return $interval . "h";

		$interval = floor($seconds / 60);
		if ($interval >= 1)  return $interval . "m";

		return floor($seconds) . "s";
	}
}

if (!function_exists('convertNumberToText')) {
	function convertNumberToText($number) {
		if ($number < 0) {
			return "";
		}

		$suffixes = ["", "K", "M", "B", "T"];
		$suffixIndex = 0;
		
		while ($number >= 1000 && $suffixIndex < count($suffixes) - 1) {
			$number /= 1000;
			$suffixIndex++;
		}

		$roundedNumber = round($number * 10) / 10;
		return $roundedNumber . $suffixes[$suffixIndex];
	}
}

if (!function_exists('varToData')) {
	function varToData($var) {
		// chuyển biến chứa key có 1 tham số là json;
		if (empty($var)) return $var;
		$var = json_decode(json_encode($var), true);
		foreach ($var as $k => $item) {
			if (is_int($k)) {
				foreach ($item as $key => $value) {
					if (is_string($value)) {
						try {
							$json_value = json_decode($value);
							if ($json_value != '' && !is_int($json_value)) {
								$var[$k][$key] = $json_value;
							}
						} catch (\Throwable $th) {
							//throw $th;
						}
					}
				}
			}
			else {
				$key = $k;
				$value = $item;
				if (is_string($value)) {
					try {
						$json_value = json_decode($value);
						if ($json_value != '' && !is_int($json_value)) {
							$var[$key] = $json_value;
						}
					} catch (\Throwable $th) {
						//throw $th;
					}
				}
			}
		}
		return $var;
	}
}

if (!function_exists('getDataFiles')) {
    function getDataFiles($files) {
        $rs = [];
        foreach ($files['name'] as $k => $v) {
            $rs[] = [
                'name' => $files['name'][$k],
                'type' => $files['type'][$k],
                'tmp_name' => $files['tmp_name'][$k],
                'size' => $files['size'][$k],
            ];
        }
        return $rs;
    }
}


// ===== Use DB =====
if (!function_exists('getName')) {
    function getName($table, $id, $col='name') {
        if ((int)$id >= 1) {
            $item = DB::table($table)->select($col)->find($id);
            if ($item) {
                return $item->$col;
            }
        }
        return '';
    }
}
if (!function_exists('isAdmin')) {
	function isAdmin($admin) {
		$rs = false;
		if (!empty($admin) && $admin->role == 1) {
			$rs = true;
		}
		return $rs;
	}
}
if (!function_exists('isAllowIp')) {
	function isAllowIp() {
		$rs = false;
		$ip = getClientIp();
		if (preg_match('/^2405\:4802:/', $ip)) {
			return true;
		}
		$setting_is_check_ip = DB::table('settings')->where('type', 'is_check_ip')->first();
		if ($setting_is_check_ip && $setting_is_check_ip->value == 'false') {
			$rs = true;
		}
		else {
			$allow_ip = DB::table('allow_ips')->where('ip', $ip)->where('status', 'active')->first();
			if ($allow_ip) $rs = true;
		}
		return $rs;
	}
}


// ===== Only clickup =====
if (!function_exists('getDataFromParams')) {
	function getDataFromParams($params) {
		/*
			name = quan02651
			pub name = quan
		*/
		$params = preg_replace('/^[\r\n]+/', '', $params);
		$params = preg_replace('/[\r\n]+$/', '', $params);
		$params = explode("\n", $params);
		$data = [];
		foreach ($params as $param) {
			if (preg_match('/=/', $param)) {
				$param = explode("=", $param);
				if (count($param) >= 2) {
					$k = $param[0];
					$v = $param[1];
					$data[$k] = $v;
				}
			}
		}
		return $data;
	}
}

if (!function_exists('handleContentVarDefine')) {
	function handleContentVarDefine($content, $language='EN') {
		/*
			[Current Month] = December, tháng 12 này
			[Current Quarter] = [Q1, Q2, Q3, Q4]
			[RAND 30-70] = trả về ngẫu nhiên trong giới hạn từ 30-70, số nguyên
		*/
		$language = strtoupper($language);
		$pattern = '/\[.*?\]/';
		$data_define = [
			
		];
		$content = preg_replace_callback($pattern, function($match) use (&$language, &$data_define) {
			$key = trim($match[0]);
			$key = str_replace('[', '', $key);
			$key = str_replace(']', '', $key);
			$key = strtoupper(trim($key));
			$key = preg_replace('/\s+/', ' ', $key);

			$current_mounth = (int)date('m');
			$current_quarter = 'Q1';
			if ($current_mounth >= 4 && $current_mounth <=6) {
				$current_quarter = 'Q2';
			}
			else if ($current_mounth >= 7 && $current_mounth <=9){
				$current_quarter = 'Q3';
			}
			else if ($current_mounth >= 10){
				$current_quarter = 'Q4';
			}

			if (preg_match('/^RAND ([0-9]+)-([0-9]+)$/', $key, $matches)) {
				return rand((int)$matches[1], (int)$matches[2]);
			}
			if ($language == 'EN') {
				setlocale(LC_TIME, 'en_US.UTF-8');
				if ($key == 'CURRENT QUARTER') {
					return $current_quarter;
				}
			}
			if ($language == 'VI') {
				setlocale(LC_TIME, 'vi_VN.UTF-8');
				if ($key == 'CURRENT QUARTER') {
					if ($current_quarter == 'Q1') {
						return 'Quí 1';
					}
					if ($current_quarter == 'Q2') {
						return 'Quí 2';
					}
					if ($current_quarter == 'Q3') {
						return 'Quí 3';
					}
					if ($current_quarter == 'Q4') {
						return 'Quí 4';
					}
					return $current_quarter;
				}
			}
			if ($language == 'FR') {
				setlocale(LC_TIME, 'fr_FR.UTF-8');
				if ($key == 'CURRENT QUARTER') {
					return $current_quarter;
				}
			}
			
			if ($key == 'CURRENT MONTH') {
				return strftime('%B');
			}

			setlocale(LC_TIME, 'en_US.UTF-8');
			return $match[0];
		}, $content);
		return $content;
	}
}

if (!function_exists('handleSubject')) {
	function handleSubject($subject, $data, $content='') {
		if (is_string($data)) {
			$data = json_decode($data, true);
			if (empty($data)) {
				$data = [];
			}
		}

		foreach ($data as $k => $v) {
			$new_key = trim($k);
			$new_key = preg_replace('/\s+/', ' ', $new_key);
			$new_key = strtolower($new_key);
			$data[$new_key] = $v;
		}

		if (preg_match('/<hr break_subject>/', $content)) {
			// $subject = preg_replace('/<hr break_subject>.*?/', '', $content);
			$subject = explode('<hr break_subject>', $content)[0];
			$subject = trim($subject);
		}

		$pattern = '/\[.*?\]/';
		$subject = preg_replace_callback($pattern, function($match) use (&$data) {
			$key = trim($match[0]);
			$key = str_replace('[', '', $key);
			$key = str_replace(']', '', $key);
			$key = trim($key);
			$key = preg_replace('/\s+/', ' ', $key);
			$key = strtolower($key);
			if (isset($data[$key])) {
				return $data[$key];
			}
			return $match[0];
		}, $subject);
		$subject = spinText($subject);
		$subject = strip_tags($subject);
		return $subject;
	}
}

if (!function_exists('handleContent')) {
	function handleContent($content, $data=[], $content_signature='', $language='EN') {
		if (is_string($data)) {
			$data = json_decode($data, true);
			if (empty($data)) {
				$data = [];
			}
		}

		if (preg_match('/<hr break_subject>/', $content)) {
			// $subject = preg_replace('/<hr break_subject>.*?/', '', $content);
			// $subject = explode('<hr break_subject>', $content)[0];
			// $subject = trim($subject);

			$content = explode('<hr break_subject>', $content)[count(explode('<hr break_subject>', $content)) - 1];
			$content = preg_replace('/^\n/', '', $content);
		}

		if (!preg_match('/<br/', $content) && !preg_match('/<p/', $content)) {
			$content = str_replace("\n", "<br>", $content);
		}

		$content = $content . $content_signature;
		$content = handleContentVarDefine($content, $language);

		$data_empty = [];
		$data_key_empty = [];

		foreach ($data as $k => $v) {
			$new_key = trim($k);
			$new_key = preg_replace('/\s+/', ' ', $new_key);
			$new_key = strtolower($new_key);
			$data[$new_key] = $v;
		}

		$pattern = '/\[.*?\]/';
		$content = preg_replace_callback($pattern, function($match) use (&$data, &$data_empty, &$data_key_empty) {
			$key = trim($match[0]);
			$key = str_replace('[', '', $key);
			$key = str_replace(']', '', $key);
			$key = trim($key);
			$key = preg_replace('/\s+/', ' ', $key);
			$key = strtolower($key);
			if (isset($data[$key])) {
				if ($data[$key] != '') {
					$replace_text = $data[$key];
					if ($key == 'Your Skype') {
			    		// $replace_text = '<a href="skype:'.$replace_text.'?chat" style="color:inherit;text-decoration: none;"><b>'. $replace_text .'</b></a>';
			    		$replace_text = $replace_text;
			    	}
			    	else if (preg_match('/mail/', $key) && isEmail($replace_text)) {
			    		$replace_text = '<a href="mailto:'.$replace_text.'" target="_blank" ><b>'.$replace_text.'</b></a>';
			    	}
			    	else if (isDomain($replace_text)) {
			    		$replace_text = trim($replace_text);
			    		$https = '';
			    		if (!preg_match('/^http/', $replace_text)) {
			    			$https = 'https://';
			    		}
						$replace_text = '<a href="'.$https.$replace_text.'" target="_blank">'.$replace_text.'</a>';
					}
					else {
						$replace_text = '<b>'.$replace_text.'</b>';
					}
					return $replace_text;
				}
			}
			else {
				$data_empty[$key] = '';
				$data_key_empty[] = $key;
			}
			return $match[0];
		}, $content);

		$template_params_update = [];
		if (!empty($data_empty)) {
			$template_params_update = array_merge($data_empty, $data);
		}

		if (preg_match('/\[/', $content) || preg_match('/\]/', $content)) {
			$content = '';
			// return $content;
		}

		// $content = textToLink($content);

		return [
			'content' => spinText($content),
			'template_params_update' => $template_params_update,
			'data_key_empty' => $data_key_empty,
		];
	}
}

if (!function_exists('handleContentDataImage')) {
	function handleContentDataImage($content) {
		if (empty($content)) return $content;
		
		$folder_path = PATH::image() . 'content';
		$folder_src = URL::image() . '/content';
		$content = LIB::addImagesFromHtml($content, $folder_path, $folder_src, 'one' . '-');
		return $content;
	}
}