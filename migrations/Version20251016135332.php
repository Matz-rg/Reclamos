<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251016135332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reclamo (id INT AUTO_INCREMENT NOT NULL, servicio VARCHAR(255) NOT NULL, numero_cliente VARCHAR(255) NOT NULL, numero_medidor VARCHAR(255) NOT NULL, domicilio VARCHAR(255) NOT NULL, usuario VARCHAR(255) NOT NULL, motivo VARCHAR(255) NOT NULL, detalle LONGTEXT DEFAULT NULL, estado VARCHAR(255) NOT NULL, fecha_creacion DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reclamo');
    }
}
