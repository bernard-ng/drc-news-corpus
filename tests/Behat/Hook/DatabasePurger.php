<?php

declare(strict_types=1);

namespace Tests\Behat\Hook;

use Behat\Behat\Context\Context;
use Behat\Hook\BeforeScenario;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DatabasePurger implements Context
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[BeforeScenario]
    public function purge(): void
    {
        $this->entityManager->getConnection()
            ->getConfiguration()
            ->setMiddlewares([]);

        $purger = new ORMPurger($this->entityManager);
        $purger->purge();

        $this->entityManager->clear();
    }
}
