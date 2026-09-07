<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260907131613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE click (id BINARY(16) NOT NULL, clicked_at DATETIME NOT NULL, url_id BINARY(16) NOT NULL, INDEX IDX_BAF6C22081CFDAE7 (url_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE click ADD CONSTRAINT FK_BAF6C22081CFDAE7 FOREIGN KEY (url_id) REFERENCES url (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE click DROP FOREIGN KEY FK_BAF6C22081CFDAE7');
        $this->addSql('DROP TABLE click');
    }
}
