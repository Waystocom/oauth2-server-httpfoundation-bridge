<?php

global $apache_request_headers;

/**
 * apache_request_headers
 *
 * @return mixed
 */
function apache_request_headers()
{
	global $apache_request_headers;

	return $apache_request_headers;
}

/**
 * set_apache_request_headers
 *
 * @param $headers
 */
function set_apache_request_headers($headers)
{
	global $apache_request_headers;

	$apache_request_headers = $headers;
}
