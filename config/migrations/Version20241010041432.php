<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20241010041432.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20241010041432 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'increase link column size';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article CHANGE link link VARCHAR(2048) NOT NULL');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article CHANGE link link VARCHAR(255) NOT NULL');
    }
}
