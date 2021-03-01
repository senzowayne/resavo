<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200209113219 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Update with englishes variables';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955DC304035');
        $this->addSql('ALTER TABLE seance DROP FOREIGN KEY FK_DF7DFD0EDC304035');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955E3797A94');
        $this->addSql(
            'CREATE TABLE booking (
                id INT AUTO_INCREMENT NOT NULL, 
                user_id INT NOT NULL, 
                room_id INT NOT NULL, 
                meeting_id INT NOT NULL, 
                payment_id INT DEFAULT NULL, 
                create_at DATETIME NOT NULL, 
                booking_date DATE NOT NULL, 
                nb_person INT DEFAULT NULL, 
                name VARCHAR(255) DEFAULT NULL, 
                notices VARCHAR(255) DEFAULT NULL, 
                total VARCHAR(255) NOT NULL, 
                INDEX IDX_E00CEDDEA76ED395 (user_id), 
                INDEX IDX_E00CEDDE54177093 (room_id), 
                INDEX IDX_E00CEDDE67433D9C (meeting_id), 
                UNIQUE INDEX UNIQ_E00CEDDE4C3A3BB (payment_id), 
                PRIMARY KEY(id)
             ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE room (
                id INT AUTO_INCREMENT NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                description LONGTEXT NOT NULL, 
                price INT NOT NULL, 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE meeting (
                id INT AUTO_INCREMENT NOT NULL, 
                room_id INT NOT NULL, 
                label VARCHAR(255) NOT NULL, 
                INDEX IDX_F515E13954177093 (room_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)'
        );
        $this->addSql(
            'ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE54177093 FOREIGN KEY (room_id) REFERENCES room (id)'
        );
        $this->addSql(
            'ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE67433D9C FOREIGN KEY (meeting_id) REFERENCES meeting (id)'
        );
        $this->addSql(
            'ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE4C3A3BB FOREIGN KEY (payment_id) REFERENCES paiements (id)'
        );
        $this->addSql(
            'ALTER TABLE meeting ADD CONSTRAINT FK_F515E13954177093 FOREIGN KEY (room_id) REFERENCES room (id)'
        );
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE salle');
        $this->addSql('DROP TABLE seance');
        $this->addSql(
            'ALTER TABLE user 
                ADD name VARCHAR(255) NOT NULL, 
                ADD first_name VARCHAR(255) NOT NULL, 
                DROP nom, 
                DROP prenom, 
                CHANGE numero number VARCHAR(255) DEFAULT NULL'
        );
        $this->addSql(
            'ALTER TABLE date_blocked 
                CHANGE raison cause VARCHAR(255) NOT NULL, 
                CHANGE date_blocked blocked_date DATE NOT NULL'
        );
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE54177093');
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E13954177093');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE67433D9C');
        $this->addSql(
            'CREATE TABLE reservation (
                id INT AUTO_INCREMENT NOT NULL, 
                user_id INT NOT NULL, 
                salle_id INT NOT NULL, 
                seance_id INT NOT NULL, 
                paiement_id INT DEFAULT NULL, 
                create_at DATETIME NOT NULL, 
                date_reservation DATE NOT NULL, 
                nb_personne INT DEFAULT NULL, 
                nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, 
                remarques VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, 
                total VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, 
                UNIQUE INDEX UNIQ_42C849552A4C4478 (paiement_id), 
                INDEX IDX_42C84955DC304035 (salle_id), 
                INDEX IDX_42C84955A76ED395 (user_id), 
                INDEX IDX_42C84955E3797A94 (seance_id), 
                PRIMARY KEY(id)
             ) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
        );
        $this->addSql(
            'CREATE TABLE salle (
                id INT AUTO_INCREMENT NOT NULL, 
                nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, 
                description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, 
                prix INT NOT NULL, PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
        );
        $this->addSql(
            'CREATE TABLE seance (
                id INT AUTO_INCREMENT NOT NULL, 
                salle_id INT NOT NULL, 
                libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, 
                INDEX IDX_DF7DFD0EDC304035 (salle_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
        );
        $this->addSql(
            'ALTER TABLE reservation ADD CONSTRAINT FK_42C849552A4C4478 FOREIGN KEY (paiement_id) REFERENCES paiements (id)'
        );
        $this->addSql(
            'ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)'
        );
        $this->addSql(
            'ALTER TABLE reservation ADD CONSTRAINT FK_42C84955DC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)'
        );
        $this->addSql(
            'ALTER TABLE reservation ADD CONSTRAINT FK_42C84955E3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id)'
        );
        $this->addSql(
            'ALTER TABLE seance ADD CONSTRAINT FK_DF7DFD0EDC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)'
        );
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE meeting');
        $this->addSql(
            'ALTER TABLE date_blocked 
                CHANGE cause raison VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, 
                CHANGE blocked_date date_blocked DATE NOT NULL'
        );
        $this->addSql(
            'ALTER TABLE user 
                ADD nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, 
                ADD prenom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, 
                DROP name, 
                DROP first_name, 
                CHANGE number numero VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`'
        );
    }
}
