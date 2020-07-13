<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200712162321 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("insert into user set nickname = :nickname, roles = :roles, password = :password, email = :email, created_at = now(), updated_at = now(); ", [
            'nickname' => 'Admin_test',
            'roles' => json_encode(['ROLE_ADMIN']),
            'password' => password_hash('hrr6324047JG5U43Oe48../,.a=1pza;a3p',3),
            'email' => 'admin@test.com',
        ]);

        $this->addSql("insert into user set nickname = :nickname, roles = :roles, password = :password, email = :email, created_at = now(), updated_at = now(); ", [
            'nickname' => 'User_test',
            'roles' => json_encode(['ROLE_USER']),
            'password' => password_hash('hrr6324047JGv5U43Oe48../,.a=1pza;a3p',3),
            'email' => 'user@test.com',
        ]);
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
