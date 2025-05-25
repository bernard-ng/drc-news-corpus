<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250525183408.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250525183408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'optimize data lengths for various fields in the database schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE title title VARCHAR(1024) NOT NULL, CHANGE link link VARCHAR(1024) NOT NULL, CHANGE bias bias VARCHAR(30) DEFAULT 'neutral' NOT NULL, CHANGE reliability reliability VARCHAR(30) DEFAULT 'reliable' NOT NULL, CHANGE transparency transparency VARCHAR(30) DEFAULT 'medium' NOT NULL, CHANGE reading_time reading_time INT UNSIGNED DEFAULT 1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookmark CHANGE description description VARCHAR(512) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE login_history ADD ip_address VARCHAR(15) DEFAULT NULL, DROP ip
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE source CHANGE bias bias VARCHAR(30) DEFAULT 'neutral' NOT NULL, CHANGE reliability reliability VARCHAR(30) DEFAULT 'reliable' NOT NULL, CHANGE transparency transparency VARCHAR(30) DEFAULT 'medium' NOT NULL, CHANGE description description VARCHAR(1024) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE email email VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(512) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE verification_token CHANGE token token VARCHAR(60) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE email email VARCHAR(500) NOT NULL, CHANGE password password VARCHAR(4098) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookmark CHANGE description description VARCHAR(2048) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE source CHANGE description description VARCHAR(2048) DEFAULT NULL, CHANGE bias bias VARCHAR(255) DEFAULT 'neutral' NOT NULL, CHANGE reliability reliability VARCHAR(255) DEFAULT 'reliable' NOT NULL, CHANGE transparency transparency VARCHAR(255) DEFAULT 'medium' NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE verification_token CHANGE token token VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE login_history ADD ip VARCHAR(45) DEFAULT NULL, DROP ip_address
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE title title VARCHAR(2048) NOT NULL, CHANGE link link VARCHAR(2048) NOT NULL, CHANGE bias bias VARCHAR(255) DEFAULT 'neutral' NOT NULL, CHANGE reliability reliability VARCHAR(255) DEFAULT 'reliable' NOT NULL, CHANGE transparency transparency VARCHAR(255) DEFAULT 'medium' NOT NULL, CHANGE reading_time reading_time INT DEFAULT NULL
        SQL);
    }
}
