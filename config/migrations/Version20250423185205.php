<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20250423185205.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Version20250423185205 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'add ip to login history';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE login_history ADD ip VARCHAR(45) DEFAULT NULL
        SQL);
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE login_history DROP ip
        SQL);
    }
}
