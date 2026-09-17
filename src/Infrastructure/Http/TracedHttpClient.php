<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Http;

use Elastic\Apm\ElasticApm;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final readonly class TracedHttpClient implements HttpClientInterface
{
    public function __construct(
        private HttpClientInterface $inner,
        private string $clientId,
    ) {}

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $path = parse_url($url, \PHP_URL_PATH) ?: $url;

        $span = ElasticApm::getCurrentTransaction()->beginCurrentSpan(
            sprintf('%s %s %s', strtoupper($method), $this->clientId, $path),
            'external',
            'http',
        );

        try {
            $response = $this->inner->request($method, $url, $options);

            return new TracedResponse(
                $response,
                $span,
                strtoupper($method),
                $url,
            );
        } catch (\Throwable $e) {
            $span->end();

            throw $e;
        }
    }

    public function stream(ResponseInterface|iterable $responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->inner->stream(
            $responses instanceof ResponseInterface
                ? $this->unwrap($responses)
                : array_map(
                    fn (ResponseInterface $response) => $this->unwrap($response),
                    is_array($responses) ? $responses : iterator_to_array($responses),
                ),
            $timeout,
        );
    }

    public function withOptions(array $options): static
    {
        return new self($this->inner->withOptions($options), $this->clientId);
    }

    private function unwrap(ResponseInterface $response): ResponseInterface
    {
        return $response instanceof TracedResponse
            ? $response->getInnerResponse()
            : $response;
    }
}
