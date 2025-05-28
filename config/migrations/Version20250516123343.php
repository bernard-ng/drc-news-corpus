<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;final class Version20250516123343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[source] add display_name and description';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE source ADD display_name VARCHAR(255) DEFAULT NULL, ADD description VARCHAR(2048) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE source DROP display_name, DROP description
        SQL);
    }
}
