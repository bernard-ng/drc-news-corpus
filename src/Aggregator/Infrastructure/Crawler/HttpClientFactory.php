<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class HttpClientFactory.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class HttpClientFactory
{
    public function __construct(
        private string $projectDir,
        private Filesystem $filesystem,
        private HttpClientInterface $client,
        private LoggerInterface $logger
    ) {
    }

    public function create(): HttpClientInterface
    {
        $proxy = $this->getProxy();

        return $this->client->withOptions([
            'headers' => [
                'User-Agent' => UserAgents::random(),
            ],
            'proxy' => $proxy !== null ? 'https://' . $proxy : null,
        ]);
    }

    private function getProxy(): ?string
    {
        $flag = boolval(getenv('USE_PROXY'));
        if ($flag === false) {
            return null;
        }

        try {
            $filename = sprintf('%s/data/proxies.txt', $this->projectDir);
            $content = $this->filesystem->readFile($filename);

            /** @var list<string> $proxies */
            $proxies = preg_split('/\r\n|\n|\r/', $content);
            $proxies = array_filter($proxies, static fn ($proxy): bool => $proxy !== '' && $proxy !== '0');

            $proxy = $proxies[array_rand($proxies)];
            $this->logger->info('HttpClient is using proxy: ' . $proxy);

            return $proxy;
        } catch (\Throwable $e) {
            $this->logger->error('Unable to read proxy file', [
                'exception' => $e,
            ]);

            return null;
        }
    }
}
