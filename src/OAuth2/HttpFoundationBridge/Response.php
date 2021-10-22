<?php

/**
 * Created by PhpStorm.
 * User: WAYSTOCOM
 * Date: 22/10/2021
 * Time: 14:23
 */

declare(strict_types=1);

namespace OAuth2\HttpFoundationBridge;

use Symfony\Component\HttpFoundation\JsonResponse;
use OAuth2\ResponseInterface;

/**
 * Class Response
 */
class Response extends JsonResponse implements ResponseInterface
{
    /**
     * addParameters
     *
     * @param array $parameters
     */
    public function addParameters(array $parameters): void
    {
        if ($this->content && $data = json_decode($this->content, true)) {
            $parameters = array_merge($data, $parameters);
        }

        $this->setData($parameters);
    }

    /**
     * addHttpHeaderss
     *
     * @param array $httpHeaders
     */
    public function addHttpHeaders(array $httpHeaders): void
    {
        foreach ($httpHeaders as $key => $value) {
            $this->headers->set($key, $value);
        }
    }

    /**
     * getParameter
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function getParameter(string $name)
    {
        if ($this->content && $data = json_decode($this->content, true)) {
            return $data[$name] ?? null;
        }
    }

    /**
     * setError
     *
     * @param int         $statusCode
     * @param string      $name
     * @param string|null $description
     * @param string|null $uri
     */
    public function setError(int $statusCode, string $name, ?string $description = null, ?string $uri = null): void
    {
        $this->setStatusCode($statusCode);
        $this->addParameters(
            array_filter(
                array(
                    'error'             => $name,
                    'error_description' => $description,
                    'error_uri'         => $uri,
                )
            )
        );
    }

    /**
     * setRedirect
     *
     * @param int         $statusCode
     * @param string      $url
     * @param string|null $state
     * @param string|null $error
     * @param string|null $errorDescription
     * @param string|null $errorUri
     */
    public function setRedirect(
        int $statusCode,
        string $url,
        ?string $state = null,
        ?string $error = null,
        ?string $errorDescription = null,
        ?string $errorUri = null
    ): void {
        $this->setStatusCode($statusCode);

        $params = array_filter(
            array(
                'state'             => $state,
                'error'             => $error,
                'error_description' => $errorDescription,
                'error_uri'         => $errorUri,
            )
        );

        if ($params) {
            $parts = parse_url($url);
            $sep = isset($parts['query']) && !empty($parts['query']) ? '&' : '?';
            $url .= $sep . http_build_query($params);
        }

        $this->headers->set('Location', $url);
    }

    /**
     * setStatusCode
     *
     * @param int  $statusCode
     * @param null $text
     *
     * @return object
     */
    public function setStatusCode(int $statusCode, $text = null): object
    {
        return parent::setStatusCode($statusCode);
    }
}
