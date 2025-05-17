<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250517055913.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250517055913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[article] add index on publication date';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_23A0E66E0D4FDE1 ON article (published_at)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_23A0E66E0D4FDE1 ON article
        SQL);
    }
}
