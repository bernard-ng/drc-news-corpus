<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Presentation\Console;

use App\IdentityAndAccess\Application\UseCase\Command\Register;
use App\IdentityAndAccess\Domain\Model\ValueObject\Roles;
use App\SharedKernel\Application\Messaging\CommandBus;
use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;
use App\SharedKernel\Presentation\Console\AskArgumentFeature;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class RegisterConsole.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsCommand('app:user-register', 'register a new user')]
final class RegisterConsole extends Command
{
    use AskArgumentFeature;

    private SymfonyStyle $io;

    public function __construct(
        private readonly CommandBus $commandBus,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->setDescription('Creates users and stores them in the database')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the new user')
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the new user')
            ->addArgument('password', InputArgument::OPTIONAL, 'The plain password of the new user')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'If set, the user is created as an administrator');
    }

    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    #[\Override]
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (
            $input->getArgument('name') !== null &&
            $input->getArgument('email') !== null &&
            $input->getArgument('password') !== null
        ) {
            return;
        }

        $this->askArgument($input, 'name');
        $this->askArgument($input, 'email');
        $this->askArgument($input, 'password', true);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $name */
        $name = $input->getArgument('name');

        /** @var string $email */
        $email = $input->getArgument('email');

        /** @var string $password */
        $password = $input->getArgument('password');

        /** @var bool $admin */
        $admin = $input->getOption('admin');

        $command = new Register($name, EmailAddress::from($email), $password, $admin ? Roles::admin() : Roles::user());
        $this->commandBus->handle($command);
        $this->io->success(\sprintf('%s was created: %s', $admin ? 'ADMIN' : 'USER', $email));

        return Command::SUCCESS;
    }
}
