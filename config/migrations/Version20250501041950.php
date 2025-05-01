<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250501041950.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250501041950 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'increase title length';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE title title VARCHAR(2048) NOT NULL
        SQL);
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE title title VARCHAR(255) NOT NULL
        SQL);
    }
}
