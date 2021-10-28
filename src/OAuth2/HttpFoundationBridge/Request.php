<?php

namespace OAuth2\HttpFoundationBridge;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request as BaseRequest;
use OAuth2\RequestInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class Request
 */
class Request extends BaseRequest implements RequestInterface
{
    /**
     * query
     *
     * @param string $name
     * @param null   $default
     *
     * @return bool|float|int|string|InputBag|null
     */
    public function query($name, $default = null)
    {
        return $this->query->get($name, $default);
    }

    /**
     * request
     *
     * @param string $name
     * @param null   $default
     *
     * @return bool|float|int|mixed|string|InputBag|null
     */
    public function request($name, $default = null)
    {
        return $this->request->get($name, $default);
    }

    /**
     * server
     *
     * @param string $name
     * @param null   $default
     *
     * @return mixed
     */
    public function server($name, $default = null)
    {
        return $this->server->get($name, $default);
    }

    /**
     * headers
     *
     * @param string $name
     * @param null $default
     *
     * @return string|null
     */
    public function headers($name, $default = null): ?string
    {
        return $this->headers->get($name, $default);
    }

    /**
     * getAllQueryParameters
     *
     * @return array
     */
    public function getAllQueryParameters(): array
    {
        return $this->query->all();
    }

    /**
     * createFromRequest
     *
     * @param BaseRequest $request
     *
     * @return Request
     */
    public static function createFromRequest(BaseRequest $request): Request
    {
        return new static($request->query->all(), $request->request->all(), $request->attributes->all(), $request->cookies->all(), $request->files->all(), $request->server->all(), $request->getContent());
    }

    /**
     * createFromRequestStack
     *
     * @param RequestStack $request
     *
     * @return Request
     */
    public static function createFromRequestStack(RequestStack $request): Request
    {
        $request = $request->getCurrentRequest();
        return self::createFromRequest($request);
    }

    /**
     * createFromGlobals
     *
     * Creates a new request with values from PHP's super globals.
     * Overwrite to fix an apache header bug. Read more here:
     * http://stackoverflow.com/questions/11990388/request-headers-bag-is-missing-authorization-header-in-symfony-2%E2%80%94
     *
     * @return Request A new request
     *
     * @api
     */
    public static function createFromGlobals(): Request
    {
        $request = parent::createFromGlobals();

        //fix the bug.
        self::fixAuthHeader($request->headers);

        return $request;
    }

    /**
     * fixAuthHeader
     *
     * PHP does not include HTTP_AUTHORIZATION in the $_SERVER array, so this header is missing.
     * We retrieve it from apache_request_headers()
     *
     * @see https://github.com/symfony/symfony/issues/7170
     *
     * @param HeaderBag $headers
     */
    protected static function fixAuthHeader(HeaderBag $headers): void
    {
        if (!$headers->has('Authorization') && function_exists('apache_request_headers')) {
            $all = apache_request_headers();
            if (isset($all['Authorization'])) {
                $headers->set('Authorization', $all['Authorization']);
            }
        }
    }
}
