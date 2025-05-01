<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

/**
 * Class Version20250501041246.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250501041246 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'introduce new source entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE source (name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, updated_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)', bias VARCHAR(255) DEFAULT 'neutral' NOT NULL, reliability VARCHAR(255) DEFAULT 'reliable' NOT NULL, transparency VARCHAR(255) DEFAULT 'medium' NOT NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD updated_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)', ADD bias VARCHAR(255) DEFAULT 'neutral' NOT NULL, ADD reliability VARCHAR(255) DEFAULT 'reliable' NOT NULL, ADD transparency VARCHAR(255) DEFAULT 'medium' NOT NULL
        SQL);

        $this->write("Fetching sources from crawled articles...");
        $sources = $this->connection
            ->executeQuery("SELECT DISTINCT source FROM article WHERE source IS NOT NULL")
            ->fetchFirstColumn();

        $this->write(sprintf("%d unique sources found", count($sources)));

        foreach ($sources as $sourceName) {
            $this->addSql("INSERT INTO source (name, url) VALUES (:name, :url)", [
                "name" => $sourceName,
                "url" => 'https://' . $sourceName
            ]);
        }
        $this->addSql("UPDATE article SET categories = LOWER(categories)");

        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E665F8A7F73 FOREIGN KEY (source) REFERENCES source (name) ON DELETE RESTRICT
        SQL);
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E665F8A7F73
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE source
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP updated_at, DROP bias, DROP reliability, DROP transparency
        SQL);
    }
}
