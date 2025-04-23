<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423190105 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'remove column prefix';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE verification_token CHANGE token_token token VARCHAR(255) DEFAULT NULL
        SQL);
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE verification_token CHANGE token token_token VARCHAR(255) DEFAULT NULL
        SQL);
    }
}
