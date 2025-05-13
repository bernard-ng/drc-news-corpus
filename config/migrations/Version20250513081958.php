<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250513081958.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250513081958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'adding reading time to articles';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD reading_time INT DEFAULT NULL
        SQL);

        $this->addSql(<<<'SQL'
            UPDATE article SET reading_time = FLOOR(LENGTH(body) - LENGTH(REPLACE(body, ' ', '')) + 1) / 200
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP reading_time
        SQL);
    }
}
