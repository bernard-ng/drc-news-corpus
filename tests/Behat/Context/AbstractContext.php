<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use App\SharedKernel\Application\Messaging\CommandBus;
use App\SharedKernel\Application\Messaging\QueryBus;
use Behat\Behat\Context\Context;
use Symfony\Contracts\Service\ServiceMethodsSubscriberTrait;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Tests\Behat\State\SharedStorage;

/**
 * Class AbstractContext.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class AbstractContext implements Context, ServiceSubscriberInterface
{
    use ServiceMethodsSubscriberTrait;

    #[\Override]
    public static function getSubscribedServices(): array
    {
        return [CommandBus::class, QueryBus::class, SharedStorage::class];
    }
}
