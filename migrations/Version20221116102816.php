<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221116102816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP INDEX UNIQ_8D93D649166D1F9C, ADD INDEX IDX_8D93D649166D1F9C (project_id)');
        $this->addSql('ALTER TABLE user DROP INDEX UNIQ_8D93D649296CD8AE, ADD INDEX IDX_8D93D649296CD8AE (team_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP INDEX IDX_8D93D649296CD8AE, ADD UNIQUE INDEX UNIQ_8D93D649296CD8AE (team_id)');
        $this->addSql('ALTER TABLE user DROP INDEX IDX_8D93D649166D1F9C, ADD UNIQUE INDEX UNIQ_8D93D649166D1F9C (project_id)');
    }
}
