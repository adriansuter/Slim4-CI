<?php
/**
 * Slim4 CI (https://github.com/adriansuter/Slim4-CI)
 *
 * @license https://github.com/adriansuter/Slim4-CI/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\CI;

class SimpleResponse
{
    /**
     * @var string|null
     */
    private $statusProtocol = null;

    /**
     * @var int|null
     */
    private $statusCode = null;

    /**
     * @var string|null
     */
    private $statusReasonPhrase = null;

    /**
     * @var array
     */
    private $headers = [];
    /**
     * @var string
     */
    private $body;

    /**
     * @return string|null
     */
    public function getStatusProtocol(): ?string
    {
        return $this->statusProtocol;
    }

    /**
     * @param string|null $statusProtocol
     */
    public function setStatusProtocol(?string $statusProtocol): void
    {
        $this->statusProtocol = $statusProtocol;
    }

    /**
     * @return int|null
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * @param int|null $statusCode
     */
    public function setStatusCode(?int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return string|null
     */
    public function getStatusReasonPhrase(): ?string
    {
        return $this->statusReasonPhrase;
    }

    /**
     * @param string|null $statusReasonPhrase
     */
    public function setStatusReasonPhrase(?string $statusReasonPhrase): void
    {
        $this->statusReasonPhrase = $statusReasonPhrase;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function addHeader(string $name, string $value)
    {
        if (!array_key_exists($name, $this->headers)) {
            $this->headers[$name] = [];
        }

        array_push($this->headers[$name], $value);
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

}
