<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250515023707.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250515023707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'date_immutable to datetime_immutable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookmark CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE login_attempt CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE login_history CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE source CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE verification_token CHANGE created_at created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE created_at created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', CHANGE updated_at updated_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookmark CHANGE created_at created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', CHANGE updated_at updated_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE source CHANGE updated_at updated_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE login_attempt CHANGE created_at created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE verification_token CHANGE created_at created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE login_history CHANGE created_at created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE updated_at updated_at DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)'
        SQL);
    }
}
