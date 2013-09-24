<?php

namespace Innova\PathBundle\Migrations\mysqli;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2013/09/23 11:57:40
 */
class Version20130923115740 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE innova_stepType (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE innova_pathtemplate (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                description LONGTEXT NOT NULL,
                step LONGTEXT NOT NULL,
                user VARCHAR(255) NOT NULL,
                edit_date DATETIME NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE innova_stepWhere (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE innova_path (
                id INT AUTO_INCREMENT NOT NULL,
                path LONGTEXT NOT NULL,
                resourceNode_id INT DEFAULT NULL,
                UNIQUE INDEX UNIQ_CE19F054B87FAB32 (resourceNode_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE innova_stepWho (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE innova_nonDigitalResource (
                id INT AUTO_INCREMENT NOT NULL,
                description LONGTEXT NOT NULL,
                type VARCHAR(255) NOT NULL,
                resourceNode_id INT DEFAULT NULL,
                UNIQUE INDEX UNIQ_305E9E56B87FAB32 (resourceNode_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE innova_step (
                id INT AUTO_INCREMENT NOT NULL,
                path_id INT DEFAULT NULL,
                stepOrder INT NOT NULL,
                parent VARCHAR(255) DEFAULT NULL,
                expanded TINYINT(1) NOT NULL,
                instructions LONGTEXT NOT NULL,
                withTutor TINYINT(1) NOT NULL,
                withComputer TINYINT(1) NOT NULL,
                duration DATETIME NOT NULL,
                stepType_id INT DEFAULT NULL,
                stepWho_id INT DEFAULT NULL,
                stepWhere_id INT DEFAULT NULL,
                resourceNode_id INT DEFAULT NULL,
                INDEX IDX_86F48567D96C566B (path_id),
                INDEX IDX_86F48567DEDC9FF6 (stepType_id),
                INDEX IDX_86F4856765544574 (stepWho_id),
                INDEX IDX_86F485678FE76F3 (stepWhere_id),
                UNIQUE INDEX UNIQ_86F48567B87FAB32 (resourceNode_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            ALTER TABLE innova_path
            ADD CONSTRAINT FK_CE19F054B87FAB32 FOREIGN KEY (resourceNode_id)
            REFERENCES claro_resource_node (id)
            ON DELETE CASCADE
        ");
        $this->addSql("
            ALTER TABLE innova_nonDigitalResource
            ADD CONSTRAINT FK_305E9E56B87FAB32 FOREIGN KEY (resourceNode_id)
            REFERENCES claro_resource_node (id)
            ON DELETE CASCADE
        ");
        $this->addSql("
            ALTER TABLE innova_step
            ADD CONSTRAINT FK_86F48567D96C566B FOREIGN KEY (path_id)
            REFERENCES innova_path (id)
        ");
        $this->addSql("
            ALTER TABLE innova_step
            ADD CONSTRAINT FK_86F48567DEDC9FF6 FOREIGN KEY (stepType_id)
            REFERENCES innova_stepType (id)
        ");
        $this->addSql("
            ALTER TABLE innova_step
            ADD CONSTRAINT FK_86F4856765544574 FOREIGN KEY (stepWho_id)
            REFERENCES innova_stepWho (id)
        ");
        $this->addSql("
            ALTER TABLE innova_step
            ADD CONSTRAINT FK_86F485678FE76F3 FOREIGN KEY (stepWhere_id)
            REFERENCES innova_stepWhere (id)
        ");
        $this->addSql("
            ALTER TABLE innova_step
            ADD CONSTRAINT FK_86F48567B87FAB32 FOREIGN KEY (resourceNode_id)
            REFERENCES claro_resource_node (id)
            ON DELETE CASCADE
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE innova_step
            DROP FOREIGN KEY FK_86F48567DEDC9FF6
        ");
        $this->addSql("
            ALTER TABLE innova_step
            DROP FOREIGN KEY FK_86F485678FE76F3
        ");
        $this->addSql("
            ALTER TABLE innova_step
            DROP FOREIGN KEY FK_86F48567D96C566B
        ");
        $this->addSql("
            ALTER TABLE innova_step
            DROP FOREIGN KEY FK_86F4856765544574
        ");
        $this->addSql("
            DROP TABLE innova_stepType
        ");
        $this->addSql("
            DROP TABLE innova_pathtemplate
        ");
        $this->addSql("
            DROP TABLE innova_stepWhere
        ");
        $this->addSql("
            DROP TABLE innova_path
        ");
        $this->addSql("
            DROP TABLE innova_stepWho
        ");
        $this->addSql("
            DROP TABLE innova_nonDigitalResource
        ");
        $this->addSql("
            DROP TABLE innova_step
        ");
    }
}
