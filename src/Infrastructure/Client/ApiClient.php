<?php

namespace App\Infrastructure\Client;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class ApiClient
{
    public function __construct(
        public HttpClientInterface $httpClient,
        public LoggerInterface $logger,
        public array $baseEndpoints = [],
    ) {
    }

    public function fetchUserProfileData(string $userId): array
    {
        $urls = array_map(static fn ($endpoint) => sprintf('%s/%s', $endpoint, $userId), $this->baseEndpoints);
        $this->logger->info('Fetching rate data from services');
        $responses = [];
        $result = [];
        $processed = [];

        foreach ($urls as $url) {
            try {
                $responses[] = $this->httpClient->request('GET', $url, ['max_duration' => 10]);
            } catch (\Throwable $failure) {
                $this->logger->error($failure->getMessage(), ['exception' => $failure]);
            }
        }

        $this->logger->info('Processing responses');

        try {
            foreach ($this->httpClient->stream($responses) as $response => $chunk) {
                $responseId = spl_object_id($response);

                try {
                    if ($chunk->isTimeout()) {
                        $this->logger->warning('Request timed out', ['url' => $response->getInfo('url')]);
                        $response->cancel();
                        $processed[$responseId] = true;
                        continue;
                    }

                    if ($chunk->isFirst()) {
                        $statusCode = $response->getStatusCode();
                        if (in_array($statusCode, [Response::HTTP_NOT_FOUND, Response::HTTP_GONE], true)) {
                            $this->logger->warning('Not found', [
                                'url' => $response->getInfo('url'),
                                'status' => $statusCode
                            ]);
                            $response->cancel();
                            $processed[$responseId] = true;
                            continue;
                        }
                    }

                    if ($chunk->isLast()) {
                        $content = $response->getContent(false);
                        $this->logger->debug('Response content processed', ['content' => $content]);
                        $result[] = $content;
                        $processed[$responseId] = true;
                    }

                    if (count($processed) === count($responses)) {
                        break;
                    }

                } catch (TransportExceptionInterface $e) {
                    $this->logger->error('Transport error in chunk processing', [
                        'url' => $response->getInfo('url'),
                        'exception' => $e
                    ]);
                    $response->cancel();
                    $processed[$responseId] = true;
                }
            }
        } catch (\Throwable $e) {
            $this->logger->error('Critical error during streaming', ['exception' => $e]);
        }

        $this->logger->info(sprintf('Finished %d processing responses', count($result)));
        return $result;
    }
}