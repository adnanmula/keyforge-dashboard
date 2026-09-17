<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Http;

use Elastic\Apm\SpanInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class TracedResponse implements ResponseInterface
{
    private bool $ended = false;

    public function __construct(
        private readonly ResponseInterface $inner,
        private readonly SpanInterface $span,
        private readonly string $method,
        private readonly string $url,
    ) {}

    public function getStatusCode(): int
    {
        try {
            $statusCode = $this->inner->getStatusCode();

            $this->finish($statusCode);

            return $statusCode;
        } catch (\Throwable $e) {
            $this->fail();

            throw $e;
        }
    }

    public function getHeaders(bool $throw = true): array
    {
        try {
            $headers = $this->inner->getHeaders($throw);

            $this->finish($this->getHttpCode());

            return $headers;
        } catch (\Throwable $e) {
            $this->fail();

            throw $e;
        }
    }

    public function getContent(bool $throw = true): string
    {
        try {
            $content = $this->inner->getContent($throw);

            $this->finish($this->getHttpCode());

            return $content;
        } catch (\Throwable $e) {
            $this->fail();

            throw $e;
        }
    }

    public function toArray(bool $throw = true): array
    {
        try {
            $data = $this->inner->toArray($throw);

            $this->finish($this->getHttpCode());

            return $data;
        } catch (\Throwable $e) {
            $this->fail();

            throw $e;
        }
    }

    public function cancel(): void
    {
        try {
            $this->inner->cancel();
        } finally {
            $this->finish();
        }
    }

    public function getInfo(?string $type = null): mixed
    {
        return $this->inner->getInfo($type);
    }

    public function getInnerResponse(): ResponseInterface
    {
        return $this->inner;
    }

    private function getHttpCode(): ?int
    {
        $code = $this->inner->getInfo('http_code');

        return is_int($code) && $code > 0
            ? $code
            : null;
    }

    private function finish(?int $statusCode = null): void
    {
        if ($this->ended) {
            return;
        }

        $this->ended = true;

        if (null !== $statusCode) {
            $this->span->context()->setLabel('http.status_code', $statusCode);
        }

        $host = parse_url($this->url, \PHP_URL_HOST);

        $this->span->context()->setLabel('http.method', $this->method);

        if (false !== $host && null !== $host) {
            $this->span->context()->setLabel('http.host', $host);
        }

        $this->span->end();
    }

    private function fail(): void
    {
        if ($this->ended) {
            return;
        }

        $this->finish();
    }

    public function __destruct()
    {
        if (false === $this->ended) {
            $this->finish($this->getHttpCode());
        }
    }
}
