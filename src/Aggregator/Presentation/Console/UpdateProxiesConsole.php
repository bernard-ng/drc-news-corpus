<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\SharedKernel\Domain\Assert;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:update-proxies',
    description: 'get an updated list of proxies',
)]
final class UpdateProxiesConsole extends Command
{
    private const string UPDATE_URL = 'https://github.com/zloi-user/hideip.me/raw/refs/heads/master/https.txt';

    private SymfonyStyle $io;

    public function __construct(
        private readonly string $projectDir,
        private readonly HttpClientInterface $client,
        private readonly Filesystem $filesystem,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $response = $this->client->request('GET', self::UPDATE_URL);

            $content = $response->getContent();
            $content = preg_replace('/^([0-9\.]+:[0-9]+):.*$/m', '$1', $content);
            Assert::string($content);

            $this->filesystem->dumpFile(
                filename: $this->projectDir . '/data/proxies.txt',
                content: $content
            );
        } catch (\Throwable $e) {
            $this->logger->critical('Failed to update proxies', [
                'exception' => $e,
            ]);
            return Command::FAILURE;
        }

        $this->io->success('Proxies updated successfully.');
        return Command::SUCCESS;
    }
}
