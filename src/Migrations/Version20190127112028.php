<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127112028 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX filter_idx ON author (name, surname, country)');
        $this->addSql('CREATE INDEX filter_idx ON book (title, author_id)');
        $this->addSql('ALTER TABLE translations DROP FOREIGN KEY FK_C6B7DA87C83F1AF1');
        $this->addSql('DROP INDEX IDX_C6B7DA87C83F1AF1 ON translations');
        $this->addSql('ALTER TABLE translations ADD book_id INT DEFAULT NULL, DROP id_book_id');
        $this->addSql('ALTER TABLE translations ADD CONSTRAINT FK_C6B7DA8716A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('CREATE INDEX IDX_C6B7DA8716A2B381 ON translations (book_id)');
        $this->addSql('CREATE INDEX filter_idx ON translations (book_id, name)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX filter_idx ON author');
        $this->addSql('DROP INDEX filter_idx ON book');
        $this->addSql('ALTER TABLE translations DROP FOREIGN KEY FK_C6B7DA8716A2B381');
        $this->addSql('DROP INDEX IDX_C6B7DA8716A2B381 ON translations');
        $this->addSql('DROP INDEX filter_idx ON translations');
        $this->addSql('ALTER TABLE translations ADD id_book_id INT DEFAULT NULL, DROP book_id');
        $this->addSql('ALTER TABLE translations ADD CONSTRAINT FK_C6B7DA87C83F1AF1 FOREIGN KEY (id_book_id) REFERENCES book (id)');
        $this->addSql('CREATE INDEX IDX_C6B7DA87C83F1AF1 ON translations (id_book_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
