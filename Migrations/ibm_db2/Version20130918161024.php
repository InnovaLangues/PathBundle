<?php

namespace Innova\PathBundle\Migrations\ibm_db2;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2013/09/18 04:10:24
 */
class Version20130918161024 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE innova_path 
            DROP COLUMN \"user\" 
            DROP COLUMN edit_date RENAME workspace_id TO resourceNode_id
        ");
        $this->addSql("
            ALTER TABLE innova_path 
            DROP FOREIGN KEY FK_CE19F05482D40A1F
        ");
        $this->addSql("
            DROP INDEX IDX_CE19F05482D40A1F
        ");
        $this->addSql("
            ALTER TABLE innova_path 
            ADD CONSTRAINT FK_CE19F054B87FAB32 FOREIGN KEY (resourceNode_id) 
            REFERENCES claro_resource_node (id) 
            ON DELETE CASCADE
        ");
        $this->addSql("
            CREATE UNIQUE INDEX UNIQ_CE19F054B87FAB32 ON innova_path (resourceNode_id)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE innova_path 
            ADD COLUMN \"user\" VARCHAR(255) NOT NULL 
            ADD COLUMN edit_date TIMESTAMP(0) NOT NULL RENAME resourcenode_id TO workspace_id
        ");
        $this->addSql("
            ALTER TABLE innova_path 
            DROP FOREIGN KEY FK_CE19F054B87FAB32
        ");
        $this->addSql("
            DROP INDEX UNIQ_CE19F054B87FAB32
        ");
        $this->addSql("
            ALTER TABLE innova_path 
            ADD CONSTRAINT FK_CE19F05482D40A1F FOREIGN KEY (workspace_id) 
            REFERENCES claro_workspace (id)
        ");
        $this->addSql("
            CREATE INDEX IDX_CE19F05482D40A1F ON innova_path (workspace_id)
        ");
    }
}