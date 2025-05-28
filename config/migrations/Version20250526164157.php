<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250526164157.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250526164157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image and excerpt columns to article table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article 
                ADD image VARCHAR(1024) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.image'))) STORED, 
                ADD excerpt VARCHAR(255) GENERATED ALWAYS AS (CONCAT(LEFT(body, 200), '...')) STORED
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP image, DROP excerpt
        SQL);
    }
}
