<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add boundary column to city table for storing GeoJSON polygon data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE city ADD boundary LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE city DROP COLUMN boundary');
    }
}

