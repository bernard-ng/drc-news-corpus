<?php

declare(strict_types=1);

namespace App\SharedKernel\Presentation\Console;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Trait AskArgumentFeature.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
trait AskArgumentFeature
{
    private function askArgument(InputInterface $input, string $name, bool $hidden = false): void
    {
        $value = \strval($input->getArgument($name));
        if ($value !== '') {
            $this->io->text(\sprintf(' > <info>%s</info>: %s', $name, $value));
        } else {
            $value = match ($hidden) {
                false => $this->io->ask(\strtoupper($name)),
                default => $this->io->askHidden(\strtoupper($name))
            };
            $input->setArgument($name, $value);
        }
    }

    private function askOption(InputInterface $input, string $name): void
    {
        $value = \strval($input->getOption($name));
        if ($value !== '') {
            $this->io->text(\sprintf(' > <info>%s</info>: %s', $name, $value));
        } else {
            $value = $this->io->ask(\strtoupper($name));
            $input->setOption($name, $value);
        }
    }
}
