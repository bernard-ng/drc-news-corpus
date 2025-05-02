<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250502184108.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250502184108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'relative url to absolue';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE article SET link = CONCAT("https://", source, "/", TRIM(BOTH "/" FROM link)) WHERE link NOT LIKE "http%"');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException(
            'This migration is irreversible. You cannot revert the link to relative url.'
        );
    }
}
