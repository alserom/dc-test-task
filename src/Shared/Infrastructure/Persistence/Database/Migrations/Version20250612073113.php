<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612073113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates `products` table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE products (
                id CHAR(26) NOT NULL COMMENT '(DC2Type:product_id)',
                title VARCHAR(255) NOT NULL,
                price DOUBLE PRECISION NOT NULL,
                image_url VARCHAR(255) DEFAULT NULL,
                source_url VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE products
        SQL);
    }
}
