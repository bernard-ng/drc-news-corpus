<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250314140326.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250314140326 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'add user table';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:user_id)\', name VARCHAR(255) NOT NULL, email VARCHAR(500) NOT NULL, password VARCHAR(4098) NOT NULL, created_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', updated_at DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password_reset_token_token VARCHAR(255) DEFAULT NULL, password_reset_token_generated_at DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user');
    }
}
