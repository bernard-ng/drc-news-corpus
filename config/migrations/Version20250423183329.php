<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250423183329.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250423183329 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'refactoring identity and access module';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE login_attempt (id BINARY(16) NOT NULL COMMENT '(DC2Type:login_attempt_id)', user_id BINARY(16) NOT NULL COMMENT '(DC2Type:user_id)', created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', INDEX IDX_8C11C1BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE login_history (id BINARY(16) NOT NULL COMMENT '(DC2Type:login_history_id)', user_id BINARY(16) NOT NULL COMMENT '(DC2Type:user_id)', created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', device_operating_system VARCHAR(255) DEFAULT NULL, device_client VARCHAR(255) DEFAULT NULL, device_device VARCHAR(255) DEFAULT NULL, device_is_bot TINYINT(1) DEFAULT 0 NOT NULL, location_time_zone VARCHAR(255) DEFAULT NULL, location_longitude DOUBLE PRECISION DEFAULT NULL, location_latitude DOUBLE PRECISION DEFAULT NULL, location_accuracy_radius INT DEFAULT NULL, INDEX IDX_37976E36A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE verification_token (id BINARY(16) NOT NULL COMMENT '(DC2Type:verification_token_id)', user_id BINARY(16) NOT NULL COMMENT '(DC2Type:user_id)', purpose VARCHAR(255) NOT NULL, created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', token_token VARCHAR(255) DEFAULT NULL, INDEX IDX_C1CC006BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE login_attempt ADD CONSTRAINT FK_8C11C1BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE login_history ADD CONSTRAINT FK_37976E36A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE verification_token ADD CONSTRAINT FK_C1CC006BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE id id BINARY(16) NOT NULL COMMENT '(DC2Type:article_id)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD is_locked TINYINT(1) DEFAULT 0 NOT NULL, ADD is_confirmed TINYINT(1) DEFAULT 0 NOT NULL, DROP password_reset_token_token, DROP password_reset_token_generated_at
        SQL);

        $this->addSql(<<<'SQL'
            UPDATE user SET is_locked = 0, is_confirmed = 1
        SQL);
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE login_attempt DROP FOREIGN KEY FK_8C11C1BA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE login_history DROP FOREIGN KEY FK_37976E36A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE verification_token DROP FOREIGN KEY FK_C1CC006BA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE login_attempt
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE login_history
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE verification_token
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD password_reset_token_token VARCHAR(255) DEFAULT NULL, ADD password_reset_token_generated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', DROP is_locked, DROP is_confirmed
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE id id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)'
        SQL);
    }
}
