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

class SimpleClient
{
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
        curl_setopt($curl, CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use ($response) {
                $len = strlen($header);
                if (preg_match('_^(HTTP/.+) (\d+) (.+)$_', trim($header), $m)) {
                    $response->setStatusProtocol($m[1]);
                    $response->setStatusCode(intval($m[2]));
                    $response->setStatusReasonPhrase($m[3]);

                    return $len;
                }

                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                {
                    return $len;
                }

                $response->addHeader(strtolower(trim($header[0])), trim($header[1]));

                return $len;
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

        $response = new SimpleResponse();

        $curl = $this->curlInit($url, $response);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_UPLOAD, true);
        curl_setopt($curl, CURLOPT_READFUNCTION, [$multipart, 'curl_read']);

        $headers = ['Content-Type: ' . $multipart->getContentType()];
        $contentLength = $multipart->getContentLength();
        if ($contentLength >= 0) {
            $headers[] = 'Content-Length: ' . $contentLength;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        return $this->curlExec($curl, $response);
    }
}
