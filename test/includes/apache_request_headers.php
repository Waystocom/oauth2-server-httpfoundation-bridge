<?php

/**
 * Created by PhpStorm.
 * User: WAYSTOCOM
 * Date: 22/10/2021
 * Time: 14:23
 */

declare(strict_types=1);

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
