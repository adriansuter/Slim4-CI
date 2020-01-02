<?php
/**
 * Slim4 CI (https://github.com/adriansuter/Slim4-CI)
 *
 * @license https://github.com/adriansuter/Slim4-CI/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\CI;

use Robtimus\Multipart\MultipartFormData;
use RuntimeException;

class SimpleRequestClient
{
    /**
     * @var string[]
     */
    private $headers = [];

    /**
     * @var string[]
     */
    private $cookies = [];

    /**
     * @param string $url
     * @param SimpleResponse $response
     *
     * @return resource
     */
    private function curlInit(string $url, SimpleResponse $response)
    {
        $curl = curl_init();
        if (!is_resource($curl)) {
            throw new RuntimeException('Curl could not be initialized.');
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 9);

        if (!empty($this->headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        }

        if (!empty($this->cookies)) {
            $cookieValue = '';
            foreach ($this->cookies as $key => $value) {
                if (!empty($cookieValue)) {
                    $cookieValue .= '; ';
                }

                $cookieValue .= $key . '=' . $value;
            }
            curl_setopt($curl, CURLOPT_COOKIE, $cookieValue);
        }

        curl_setopt($curl, CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use ($response) {
                $length = strlen($header);
                if (preg_match('_^(HTTP/.+) (\d+) (.+)$_', trim($header), $m)) {
                    $response->setStatusProtocol($m[1]);
                    $response->setStatusCode(intval($m[2]));
                    $response->setStatusReasonPhrase($m[3]);

                    return $length;
                }

                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                {
                    return $length;
                }

                $response->addHeader(strtolower(trim($header[0])), trim($header[1]));

                return $length;
            }
        );

        return $curl;
    }

    /**
     * @param resource $curl
     * @param SimpleResponse $response
     *
     * @return SimpleResponse
     */
    private function curlExec($curl, SimpleResponse $response): SimpleResponse
    {
        $body = curl_exec($curl);
        if ($body !== false) {
            $response->setBody($body);
        }
        curl_close($curl);

        return $response;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * @param array $cookies
     */
    public function setCookies(array $cookies): void
    {
        $this->cookies = $cookies;
    }

    /**
     * @param string $url
     * @param array $queryParams
     *
     * @return SimpleResponse
     */
    public function get(string $url, array $queryParams = []): SimpleResponse
    {
        $response = new SimpleResponse();

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $curl = $this->curlInit($url, $response);

        return $this->curlExec($curl, $response);
    }

    /**
     * @param string $url
     * @param array $params
     *
     * @return SimpleResponse
     */
    public function post(string $url, array $params = []): SimpleResponse
    {
        $response = new SimpleResponse();

        $curl = $this->curlInit($url, $response);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

        return $this->curlExec($curl, $response);
    }

    /**
     * Send a POST request containing multipart form data to the server
     *
     * @param string $url
     * @param MultipartFormData $multipart
     *
     * @return SimpleResponse
     */
    public function postMultipart(string $url, MultipartFormData $multipart): SimpleResponse
    {
        if (!$multipart->isFinished()) {
            $multipart->finish();
        }

        array_push($this->headers, 'Content-Type: ' . $multipart->getContentType());
        $contentLength = $multipart->getContentLength();
        if ($contentLength >= 0) {
            array_push($this->headers, 'Content-Length: ' . $contentLength);
        }

        $response = new SimpleResponse();
        $curl = $this->curlInit($url, $response);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $multipart->buffer(
            $multipart->getContentLength()
        ));

        return $this->curlExec($curl, $response);
    }
}
