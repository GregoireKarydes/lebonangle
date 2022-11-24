<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221124083037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advert DROP FOREIGN KEY FK_54F1F40B12469DE2');
        $this->addSql('ALTER TABLE advert ADD CONSTRAINT FK_54F1F40B12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89D07ECCB6');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89D07ECCB6 FOREIGN KEY (advert_id) REFERENCES advert (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advert DROP FOREIGN KEY FK_54F1F40B12469DE2');
        $this->addSql('ALTER TABLE advert ADD CONSTRAINT FK_54F1F40B12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89D07ECCB6');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89D07ECCB6 FOREIGN KEY (advert_id) REFERENCES advert (id)');
    }
}
