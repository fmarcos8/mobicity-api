<?php

namespace MobiCity\Core;

class Request
{
    private string $method;
    private string $uri;
    private array $queryParams;
    private array $bodyParams;
    private array $headers;
    private array $files;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->queryParams = $_GET;
        $this->bodyParams = json_decode(file_get_contents('php://input'), true) ?? [];
        $this->files = $_FILES;
    }

    public static function capture(): self
    {
        return new self();
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function getUri() : string
    {
        return $this->uri;
    }

    public function getQueryParams() : array
    {
        return $this->queryParams;
    }

    public function getBodyParams() : array
    {
        return $this->bodyParams;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function hasHeader(string $key): bool  
    {
        return isset($this->headers[$key]);
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}