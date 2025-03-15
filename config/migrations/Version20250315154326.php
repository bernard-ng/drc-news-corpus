<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250315154326 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'fix password_reset_token_generated_at column type';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user CHANGE password_reset_token_generated_at password_reset_token_generated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user CHANGE password_reset_token_generated_at password_reset_token_generated_at DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\'');
    }
}
