<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250514211949.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250514211949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[FeedManagement] add bookmark';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE bookmark (id BINARY(16) NOT NULL COMMENT '(DC2Type:bookmark_id)', user_id BINARY(16) NOT NULL COMMENT '(DC2Type:user_id)', name VARCHAR(255) NOT NULL, description VARCHAR(2048) DEFAULT NULL, is_public TINYINT(1) DEFAULT 0 NOT NULL, created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', updated_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)', INDEX IDX_DA62921DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE bookmark_article (bookmark_id BINARY(16) NOT NULL COMMENT '(DC2Type:bookmark_id)', article_id BINARY(16) NOT NULL COMMENT '(DC2Type:article_id)', INDEX IDX_6FE2655D92741D25 (bookmark_id), INDEX IDX_6FE2655D7294869C (article_id), PRIMARY KEY(bookmark_id, article_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookmark ADD CONSTRAINT FK_DA62921DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookmark_article ADD CONSTRAINT FK_6FE2655D92741D25 FOREIGN KEY (bookmark_id) REFERENCES bookmark (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookmark_article ADD CONSTRAINT FK_6FE2655D7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE bookmark DROP FOREIGN KEY FK_DA62921DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookmark_article DROP FOREIGN KEY FK_6FE2655D92741D25
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookmark_article DROP FOREIGN KEY FK_6FE2655D7294869C
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE bookmark
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE bookmark_article
        SQL);
    }
}
