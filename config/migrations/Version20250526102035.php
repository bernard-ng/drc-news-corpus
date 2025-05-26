<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250526102035.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250526102035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'optimize sentiment column in article table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE sentiment sentiment VARCHAR(30) DEFAULT 'neutral' NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE sentiment sentiment VARCHAR(255) DEFAULT 'neutral' NOT NULL
        SQL);
    }
}
