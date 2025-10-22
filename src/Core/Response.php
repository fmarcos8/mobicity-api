<?php

namespace MobiCity\Core;

class Response
{
    public function __construct(
        protected mixed $content,
        protected int $statusCode = 200
    ) {}

    public static function json(mixed $data, int $statusCode = 200): void
    {
        header('Content-type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        echo $this->content;
    }
}