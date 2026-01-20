<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260120125932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Сначала конвертируем существующие данные в JSON формат
        $this->addSql("UPDATE \"user\" SET roles = '[]' WHERE roles IS NULL OR roles = ''");
        $this->addSql("UPDATE \"user\" SET roles = '[\"' || roles || '\"]' WHERE roles IS NOT NULL AND roles != '' AND roles NOT LIKE '[%'");

        // Затем изменяем тип колонки с явным преобразованием
        $this->addSql('ALTER TABLE "user" ALTER roles TYPE JSON USING roles::json');
        $this->addSql('ALTER TABLE "user" ALTER roles SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('ALTER TABLE "user" ALTER roles TYPE VARCHAR(255)');
    }
}
