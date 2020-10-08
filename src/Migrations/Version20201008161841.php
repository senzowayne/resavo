<?php

declare(strict_types=1);

namespace DoctrineMigrationsdc ;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201008161841 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add config merchant + interval date + isActive meeting + isCapture paiement';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE config_merchant (id INT AUTO_INCREMENT NOT NULL, name_merchant VARCHAR(255) NOT NULL, payment_service VARCHAR(255) NOT NULL, pattern_color VARCHAR(255) NOT NULL, maintenance TINYINT(1) DEFAULT \'1\' NOT NULL, created_at DATETIME NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE date_blocked ADD end DATE NOT NULL, CHANGE blocked_date start DATE NOT NULL');
        $this->addSql('ALTER TABLE meeting ADD is_active TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE paiements CHANGE capture capture TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE config_merchant');
        $this->addSql('ALTER TABLE date_blocked ADD blocked_date DATE NOT NULL, DROP start, DROP end');
        $this->addSql('ALTER TABLE meeting DROP is_active');
        $this->addSql('ALTER TABLE paiements CHANGE capture capture TINYINT(1) DEFAULT \'0\'');
    }
}
