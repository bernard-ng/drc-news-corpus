<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250501143015.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250501143015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add sentiment score';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD sentiment VARCHAR(255) DEFAULT 'neutral' NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP sentiment
        SQL);
    }
}
