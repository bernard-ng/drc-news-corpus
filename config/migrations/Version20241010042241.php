<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20241010042241.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20241010042241 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'add hash column to article';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article ADD hash VARCHAR(32) NOT NULL');
        $this->addSql('UPDATE article SET hash = MD5(link)');
        $this->addSql('CREATE INDEX IDX_23A0E66D1B862B8 ON article (hash)');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_23A0E66D1B862B8 ON article');
        $this->addSql('ALTER TABLE article DROP hash');
    }
}
