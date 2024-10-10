<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20241010041217.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20241010041217 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'remove unique index on article link';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_23A0E6636AC99F1 ON article');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E6636AC99F1 ON article (link)');
    }
}
