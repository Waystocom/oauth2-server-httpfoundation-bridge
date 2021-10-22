<?php

/**
 * Created by PhpStorm.
 * User: WAYSTOCOM
 * Date: 22/10/2021
 * Time: 14:23
 */

declare(strict_types=1);

namespace OAuth2\HttpFoundationBridge;

use PHPUnit\Framework\TestCase;

/**
 * Class RequestTest
 */
class RequestTest extends TestCase
{
    /**
     * testFixAuthHeader
     */
    public function testFixAuthHeader(): void
    {
        require_once __DIR__ .'/../../includes/apache_request_headers.php';

        \set_apache_request_headers(array('Authorization' => 'Bearer xyz'));

        $request = Request::createFromGlobals();

        $this->assertEquals('Bearer xyz', $request->headers('Authorization'));
    }
}

