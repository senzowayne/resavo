<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201029031016 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add google oauth + maintenance default false';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE config_merchant CHANGE maintenance maintenance TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE user ADD google_id VARCHAR(255) DEFAULT NULL, CHANGE hash hash VARCHAR(255) DEFAULT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
