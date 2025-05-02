<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250502181706.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250502181706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add metadata column to article table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD metadata JSON DEFAULT NULL COMMENT '(DC2Type:open_graph)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP metadata
        SQL);
    }
}
