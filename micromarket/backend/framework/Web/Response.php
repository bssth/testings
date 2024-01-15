<?php

namespace Framework\Web;

class Response
{
    protected string $content_type = 'text/plain';
    protected array $headers = [];
    protected string $body = '';

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->content_type;
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
     * @return Response
     */
    public function withBody(string $body): Response
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param string $content_type
     * @return Response
     */
    public function withContentType(string $content_type): Response
    {
        $this->content_type = $content_type;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $key
     * @param string|null $value
     * @return Response
     */
    public function withHeader(string $key, string $value = null): Response
    {
        if($value === null) {
            unset($this->headers[$key]);
        } else {
            $this->headers[$key] = $value;
        }

        return $this;
    }
}
