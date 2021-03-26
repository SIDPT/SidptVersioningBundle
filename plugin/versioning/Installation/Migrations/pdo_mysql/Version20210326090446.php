<?php

namespace Sidpt\VersioningBundle\Installation\Migrations\pdo_mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated migration based on mapping information: modify it with caution.
 *
 * Generation date: 2021/03/26 09:04:48
 */
class Version20210326090446 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE sidpt__resource_version (
                id INT AUTO_INCREMENT NOT NULL, 
                branch_id INT DEFAULT NULL, 
                resource_type_id INT NOT NULL, 
                user_id INT DEFAULT NULL, 
                previous_id INT DEFAULT NULL, 
                version VARCHAR(255) DEFAULT NULL, 
                resourceId VARCHAR(36) NOT NULL, 
                creationDate DATETIME NOT NULL, 
                lastModificationDate DATETIME NOT NULL, 
                uuid VARCHAR(36) NOT NULL, 
                UNIQUE INDEX UNIQ_21D5DE15D17F50A6 (uuid), 
                INDEX IDX_21D5DE15DCD6CC49 (branch_id), 
                INDEX IDX_21D5DE1598EC6B7B (resource_type_id), 
                INDEX IDX_21D5DE15A76ED395 (user_id), 
                INDEX IDX_21D5DE152DE62210 (previous_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE sidpt__resource_node_branch (
                id INT AUTO_INCREMENT NOT NULL, 
                parent_id INT DEFAULT NULL, 
                head_id INT NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                uuid VARCHAR(36) NOT NULL, 
                resourceNode_id INT DEFAULT NULL, 
                UNIQUE INDEX UNIQ_9FCA9E8D17F50A6 (uuid), 
                UNIQUE INDEX UNIQ_9FCA9E8B87FAB32 (resourceNode_id), 
                INDEX IDX_9FCA9E8727ACA70 (parent_id), 
                UNIQUE INDEX UNIQ_9FCA9E8F41A619E (head_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB
        ");
        $this->addSql("
            ALTER TABLE sidpt__resource_version 
            ADD CONSTRAINT FK_21D5DE15DCD6CC49 FOREIGN KEY (branch_id) 
            REFERENCES sidpt__resource_node_branch (id)
        ");
        $this->addSql("
            ALTER TABLE sidpt__resource_version 
            ADD CONSTRAINT FK_21D5DE1598EC6B7B FOREIGN KEY (resource_type_id) 
            REFERENCES claro_resource_type (id) 
            ON DELETE CASCADE
        ");
        $this->addSql("
            ALTER TABLE sidpt__resource_version 
            ADD CONSTRAINT FK_21D5DE15A76ED395 FOREIGN KEY (user_id) 
            REFERENCES claro_user (id)
        ");
        $this->addSql("
            ALTER TABLE sidpt__resource_version 
            ADD CONSTRAINT FK_21D5DE152DE62210 FOREIGN KEY (previous_id) 
            REFERENCES sidpt__resource_version (id)
        ");
        $this->addSql("
            ALTER TABLE sidpt__resource_node_branch 
            ADD CONSTRAINT FK_9FCA9E8B87FAB32 FOREIGN KEY (resourceNode_id) 
            REFERENCES claro_resource_node (id)
        ");
        $this->addSql("
            ALTER TABLE sidpt__resource_node_branch 
            ADD CONSTRAINT FK_9FCA9E8727ACA70 FOREIGN KEY (parent_id) 
            REFERENCES sidpt__resource_node_branch (id)
        ");
        $this->addSql("
            ALTER TABLE sidpt__resource_node_branch 
            ADD CONSTRAINT FK_9FCA9E8F41A619E FOREIGN KEY (head_id) 
            REFERENCES sidpt__resource_version (id)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE sidpt__resource_version 
            DROP FOREIGN KEY FK_21D5DE152DE62210
        ");
        $this->addSql("
            ALTER TABLE sidpt__resource_node_branch 
            DROP FOREIGN KEY FK_9FCA9E8F41A619E
        ");
        $this->addSql("
            ALTER TABLE sidpt__resource_version 
            DROP FOREIGN KEY FK_21D5DE15DCD6CC49
        ");
        $this->addSql("
            ALTER TABLE sidpt__resource_node_branch 
            DROP FOREIGN KEY FK_9FCA9E8727ACA70
        ");
        $this->addSql("
            DROP TABLE sidpt__resource_version
        ");
        $this->addSql("
            DROP TABLE sidpt__resource_node_branch
        ");
    }
}
