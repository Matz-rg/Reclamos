<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251203153250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reclamo (id INT AUTO_INCREMENT NOT NULL, siniestro_id INT DEFAULT NULL, servicio VARCHAR(255) NOT NULL, numero_cliente BIGINT NOT NULL, numero_medidor VARCHAR(255) NOT NULL, domicilio VARCHAR(255) NOT NULL, usuario VARCHAR(255) NOT NULL, motivo VARCHAR(255) NOT NULL, detalle LONGTEXT DEFAULT NULL, estado VARCHAR(255) NOT NULL, fecha_creacion DATETIME NOT NULL, fecha_de_visualizacion DATETIME DEFAULT NULL, fecha_cierre DATETIME DEFAULT NULL, user_cierre VARCHAR(255) DEFAULT NULL, causa VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_558B0D8732488584 (numero_cliente), INDEX IDX_558B0D8790195D8C (siniestro_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE siniestro (id INT AUTO_INCREMENT NOT NULL, servicio VARCHAR(255) NOT NULL, causa LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_user VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reclamo ADD CONSTRAINT FK_558B0D8790195D8C FOREIGN KEY (siniestro_id) REFERENCES siniestro (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamo DROP FOREIGN KEY FK_558B0D8790195D8C');
        $this->addSql('DROP TABLE reclamo');
        $this->addSql('DROP TABLE siniestro');
        $this->addSql('DROP TABLE user');
    }
}
