<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251028120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add latitude and longitude columns to city table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE city ADD latitude DECIMAL(10, 6) DEFAULT NULL');
        $this->addSql('ALTER TABLE city ADD longitude DECIMAL(10, 6) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE city DROP COLUMN latitude');
        $this->addSql('ALTER TABLE city DROP COLUMN longitude');
    }
}

