<?php

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
    public function addParameters(array $parameters)
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
    public function addHttpHeaders(array $httpHeaders)
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
    public function getParameter($name)
    {
        if ($this->content && $data = json_decode($this->content, true)) {
            return $data[$name] ?? null;
        }
    }

    /**
     * setError
     *
     * @param int    $statusCode
     * @param string $name
     * @param null   $description
     * @param null   $uri
     */
    public function setError($statusCode, $name, $description = null, $uri = null)
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
     * @param int    $statusCode
     * @param string $url
     * @param null   $state
     * @param null   $error
     * @param null   $errorDescription
     * @param null   $errorUri
     */
    public function setRedirect(
        $statusCode,
        $url,
        $state = null,
        $error = null,
        $errorDescription = null,
        $errorUri = null
    ) {
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
}
