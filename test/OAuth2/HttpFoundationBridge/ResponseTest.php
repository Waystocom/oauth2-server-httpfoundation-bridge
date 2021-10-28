<?php

namespace OAuth2\HttpFoundationBridge;

use PHPUnit\Framework\TestCase;

/**
 * Class ResponseTest
 */
class ResponseTest extends TestCase
{
    /**
     * testAddParameters
     */
    public function testAddParameters(): void
    {
        foreach ($this->provideAddParameters() as $params) {
            $response = new Response();

            if (isset($params[2])) {
                $response->setContent($params[2]);
            }

            $response->addParameters($params[1]);
            $this->assertEquals($params[0], $response->getContent());
        }
    }

    /**
     * provideAddParameters
     *
     * @return array
     */
    public function provideAddParameters(): array
    {
        return array(
            array('[]', array()),
            array('{"test":"foo"}', array('test' => 'foo')),
            array('{"test2":"foo2","test":"foo"}', array('test' => 'foo'), '{"test2":"foo2"}'),
        );
    }

    /**
     * testAddHttpHeaders
     */
    public function testAddHttpHeaders(): void
    {
        foreach ($this->provideAddHttpHeaders() as $params) {
            $response = new Response();
            $response->addHttpHeaders($params[1]);
            $this->assertStringContainsString($params[0], json_encode($response->headers->all()));
        }
    }

    /**
     * provideAddHttpHeaders
     *
     * @return array
     */
    public function provideAddHttpHeaders(): array
    {
        return array(
            array('"cache-control":["no-store, private"]', array('Cache-Control' => array('no-store'))),
            array('"header":["value"]', array('foo' => 'bar', 'header' => 'value')),
            array('"content-type":["application\/xml"]', array('content-type' => 'application/xml')),
        );
    }

    /**
     * testGetParameter
     */
    public function testGetParameter(): void
    {
        foreach ($this->provideGetParameter() as $params) {
            $response = new Response();
            $response->setContent($params[1]);

            $this->assertEquals($params[0], $response->getParameter($params[ 2]));
        }

    }

    /**
     * provideGetParameter
     *
     * @return array
     */
    public function provideGetParameter(): array
    {
        return array(
            array(null, '', 'foo'),
            array('foo', '{"test":"foo"}', 'test'),
            array(array('bar', 'baz'), '{"foo":["bar","baz"]}', 'foo'),
        );
    }

    /**
     * testSetError
     */
    public function testSetError(): void
    {
        foreach ($this->provideSetError() as $params) {
            $errorUri = $errorDescription = null;
            if (isset($params[3])) {
                $errorDescription = $params[3];
            }
            if (isset($params[4])) {
                $errorUri = $params[4];
            }
            $response = new Response();
            $response->setError($params[1], $params[2], $errorDescription, $errorUri);

            $this->assertEquals($params[0], $response->getContent());
            $this->assertEquals($params[1], $response->getStatusCode());
        }
    }

    /**
     * provideSetError
     *
     * @return array
     */
    public function provideSetError(): array
    {
        $message = '{"error":"invalid_argument","error_description":"missing required parameter",';
        $message .= '"error_uri":"http:\/\/brentertainment.com"}';
        return array(
            array(
                '{"error":"invalid_argument"}',
                400,
                'invalid_argument'
            ),
            array(
                '{"error":"invalid_argument","error_description":"missing required parameter"}',
                400,
                'invalid_argument',
                'missing required parameter'
            ),
            array(
                $message,
                400,
                'invalid_argument',
                'missing required parameter',
                'http://brentertainment.com'
            ),
        );
    }

    /**
     * testSetRedirect
     */
    public function testSetRedirect(): void
    {
        foreach ($this->provideSetRedirect() as $params) {
            $state = $error = $errorUri = $errorDescription = null;
            if (isset($params[2])) {
                $state = $params[2];
            }
            if (isset($params[3])) {
                $error = $params[3];
            }
            if (isset($params[4])) {
                $errorDescription = $params[4];
            }
            if (isset($params[5])) {
                $errorUri = $params[5];
            }
            $response = new Response();
            $response->setRedirect(301, $params[1], $state, $error, $errorDescription, $errorUri);
            $this->assertEquals($params[0], $response->headers->get('Location'));
        }
    }

    /**
     * provideSetRedirect
     *
     * @return array
     */
    public function provideSetRedirect(): array
    {
        $message = 'http://test.com/path?state=xyz&error=';
        $message .= 'foo&error_description=this+is+a+description&error_uri=http%3A%2F%2Fbrentertainment.com';
        return array(
            array(
                'http://test.com/path?error=foo',
                'http://test.com/path',
                null,
                'foo'
            ),
            array(
                'https://sub.test.com/path?query=string&error=foo',
                'https://sub.test.com/path?query=string',
                null,
                'foo'
            ),
            array(
                'http://test.com/path?error=foo&error_description=this+is+a+description',
                'http://test.com/path',
                null,
                'foo',
                'this is a description'
            ),
            array(
                'http://test.com/path?state=xyz&error=foo&error_description=this+is+a+description',
                'http://test.com/path',
                'xyz',
                'foo',
                'this is a description'
            ),
            array(
                $message,
                'http://test.com/path',
                'xyz',
                'foo',
                'this is a description',
                'http://brentertainment.com'
            ),
        );
    }
}
