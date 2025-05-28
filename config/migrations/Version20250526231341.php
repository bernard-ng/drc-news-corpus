<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Aggregator\Domain\Model\Identity\SourceId;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250526231341.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250526231341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'move from source_name to source_id in article and followed_source tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("SET FOREIGN_KEY_CHECKS = 0");

        // delete the old indexes and foreign keys
        $this->addSql("DROP INDEX `primary` ON source");
        $this->addSql("ALTER TABLE article DROP FOREIGN KEY FK_23A0E665F8A7F73");
        $this->addSql("ALTER TABLE followed_source DROP FOREIGN KEY FK_7A763A3E5F8A7F73");
        $this->addSql("DROP INDEX IDX_23A0E665F8A7F73 ON article");
        $this->addSql("DROP INDEX IDX_7A763A3E5F8A7F73 ON followed_source");

        // add the new id column to source table
        $this->addSql("ALTER TABLE source ADD id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:source_id)' FIRST");
        $sources = $this->connection
            ->executeQuery("SELECT name FROM source")
            ->fetchFirstColumn();

        foreach ($sources as $source) {
            $this->addSql("UPDATE source SET id = :id WHERE name = :name", [
                "id" => new SourceId()->toBinary(),
                "name" => $source,
            ]);
        }

        // set the id column as NOT NULL and create a unique index
        $this->addSql("ALTER TABLE source MODIFY id BINARY(16) NOT NULL COMMENT '(DC2Type:source_id)'");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_5F8A7F735E237E06 ON source (name)");
        $this->addSql("ALTER TABLE source ADD PRIMARY KEY (id)");

        // Update article table
        $this->addSql("ALTER TABLE article ADD source_id BINARY(16) NOT NULL COMMENT '(DC2Type:source_id)'");
        $this->addSql("UPDATE article JOIN source ON article.source = source.name SET article.source_id = source.id");
        $this->addSql("ALTER TABLE article DROP source");
        $this->addSql("ALTER TABLE article ADD CONSTRAINT FK_23A0E66953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) ON DELETE CASCADE");
        $this->addSql(" CREATE INDEX IDX_23A0E66953C1C61 ON article (source_id)");

        // Update followed_source table
        $this->addSql("ALTER TABLE followed_source ADD source_id BINARY(16) NOT NULL COMMENT '(DC2Type:source_id)'");
        $this->addSql("ALTER TABLE followed_source DROP source");
        $this->addSql("ALTER TABLE followed_source ADD CONSTRAINT FK_7A763A3E953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_7A763A3E953C1C61 ON followed_source (source_id)");

        // Re-enable foreign key checks
        $this->addSql("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('This migration is irreversible.');
    }
}
