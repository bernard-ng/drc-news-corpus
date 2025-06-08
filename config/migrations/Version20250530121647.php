<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250530121647.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250530121647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create index on article table for published_at and id columns';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD INDEX IDX_PUBLISHED_AT_ID (published_at DESC, id DESC);
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_PUBLISHED_AT_ID ON article
        SQL);
    }
}
