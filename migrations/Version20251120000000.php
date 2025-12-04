<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image_url column to hotel table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE hotel ADD image_url VARCHAR(500) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE hotel DROP COLUMN image_url');
    }
}

