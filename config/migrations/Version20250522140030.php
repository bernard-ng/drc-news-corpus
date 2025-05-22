<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250522140030.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250522140030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create FollowedSource table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE followed_source (id BINARY(16) NOT NULL COMMENT '(DC2Type:followed_source_id)', follower_id BINARY(16) NOT NULL COMMENT '(DC2Type:user_id)', source VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_7A763A3EAC24F853 (follower_id), INDEX IDX_7A763A3E5F8A7F73 (source), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followed_source ADD CONSTRAINT FK_7A763A3EAC24F853 FOREIGN KEY (follower_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followed_source ADD CONSTRAINT FK_7A763A3E5F8A7F73 FOREIGN KEY (source) REFERENCES source (name) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE followed_source DROP FOREIGN KEY FK_7A763A3EAC24F853
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followed_source DROP FOREIGN KEY FK_7A763A3E5F8A7F73
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE followed_source
        SQL);
    }
}
