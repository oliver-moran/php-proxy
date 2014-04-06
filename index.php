<?php
	/*
	    The MIT License (MIT)

	    Copyright (c) 2014 Oliver Moran

	    Permission is hereby granted, free of charge, to any person obtaining a copy of
	    this software and associated documentation files (the "Software"), to deal in
	    the Software without restriction, including without limitation the rights to
	    use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
	    of the Software, and to permit persons to whom the Software is furnished to do
	    so, subject to the following conditions:

	    The above copyright notice and this permission notice shall be included in all
	    copies or substantial portions of the Software.

	    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	    SOFTWARE.
	*/

	// Used to enable cross-domain AJAX calls.
	// Example: index.php?url=http://www.example.org/resource.json

	$url = $_REQUEST["url"];
	
	if (substr ($url, 0, 7) != "http://"
		&& substr ($url, 0, 8) != "https://"
		&& substr ($url, 0, 6) != "ftp://") {
		// NB: only absolute URLs are allowed -
		// otherwise the script could be used to access local-to-file system files
		die("ERROR: The argument 'url' must be an absolute URL beginning with 'http://', 'https://', or 'ftp://'.");
	}

	// temporarily override CURLs user agent with the user's own
	ini_set("user_agent", $_SERVER['HTTP_USER_AGENT']);

	// enable access from all domains
	enable_cors();

	switch ($_SERVER["REQUEST_METHOD"]) {
		case "GET":
			get($url);
			break;
		default:
			post($url);
			break;
	}


	// get the contents of the URL and echo the results
	function get($url) {
		// if (substr ($url, 0, 8) == "https://") {
		//	echo getSSL($url);
		// } else {
			echo file_get_contents($url);
		// }
	}

	// gets over HTTPS
	function getSSL($url) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_SSLVERSION,3); 
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return $result[0];
	}

	// post (or put or delete?) the encoded form to the URL and echo the results
	function post($url) {
		$postdata = http_build_query(
		    array()
		);

		$opts = array('http' =>
		    array(
		        'method'  => $_SERVER['REQUEST_METHOD'],
		        'header'  => 'Content-type: application/x-www-form-urlencoded',
		        'content' => $postdata
		    )
		);

		$context  = stream_context_create($opts);

		// get the contents of the external URL and echo it
		echo file_get_contents($url, false, $context);
	}

	/**
	 *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
	 *  origin.
	 *
	 *  In a production environment, you probably want to be more restrictive, but this gives you
	 *  the general idea of what is involved.  For the nitty-gritty low-down, read:
	 *
	 *  - https://developer.mozilla.org/en/HTTP_access_control
	 *  - http://www.w3.org/TR/cors/
	 *
	 */
	function enable_cors() {
		// Allow from any origin
		if (isset($_SERVER['HTTP_ORIGIN'])) {
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');;
			header('Access-Control-Max-Age: 86400');	// cache for 1 day
		} else {
			header("Access-Control-Allow-Origin: *");
			header('Access-Control-Allow-Credentials: true');;
			header('Access-Control-Max-Age: 86400');	// cache for 1 day
		}

		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");		 

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

			exit(0);
		}
	}
?>