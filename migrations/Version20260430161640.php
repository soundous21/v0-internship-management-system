<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260430161640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE applications CHANGE status status VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE applications RENAME INDEX fk_app_offer_id TO IDX_F7C966F053C674EE');
        $this->addSql('ALTER TABLE applications RENAME INDEX fk_app_student_id TO IDX_F7C966F0CB944F1A');
        $this->addSql('ALTER TABLE offers DROP skills, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE duration duration VARCHAR(100) DEFAULT NULL, CHANGE location_type location_type VARCHAR(20) NOT NULL, CHANGE wilaya wilaya VARCHAR(100) DEFAULT NULL, CHANGE deadline deadline DATE DEFAULT NULL, CHANGE latitude latitude NUMERIC(10, 8) DEFAULT NULL, CHANGE longitude longitude NUMERIC(11, 8) DEFAULT NULL, CHANGE status status VARCHAR(20) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE start_date start_date DATE DEFAULT NULL, CHANGE internship_start internship_start DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE offers RENAME INDEX fk_offer_user TO IDX_DA460427979B1AD6');
        $this->addSql('ALTER TABLE offers_skills DROP FOREIGN KEY `fk_offer`');
        $this->addSql('ALTER TABLE offers_skills DROP FOREIGN KEY `fk_skill`');
        $this->addSql('ALTER TABLE offers_skills ADD CONSTRAINT FK_23D340E353C674EE FOREIGN KEY (offer_id) REFERENCES offers (id)');
        $this->addSql('ALTER TABLE offers_skills ADD CONSTRAINT FK_23D340E35585C142 FOREIGN KEY (skill_id) REFERENCES skills (id_skill)');
        $this->addSql('ALTER TABLE offers_skills RENAME INDEX idx_offer TO IDX_23D340E353C674EE');
        $this->addSql('ALTER TABLE offers_skills RENAME INDEX idx_skill TO IDX_23D340E35585C142');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY `FK_7CE748AA76ED395`');
        $this->addSql('ALTER TABLE reset_password_request CHANGE requested_at requested_at DATETIME NOT NULL, CHANGE expires_at expires_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE skills DROP skill_name, CHANGE id_tag id_tag INT NOT NULL, CHANGE tag_name tag_name VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE user ADD portfolio_link VARCHAR(255) DEFAULT NULL, DROP skills, CHANGE roles roles JSON NOT NULL, CHANGE first_name first_name VARCHAR(100) DEFAULT NULL, CHANGE last_name last_name VARCHAR(100) DEFAULT NULL, CHANGE company_name company_name VARCHAR(150) DEFAULT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE github_link github_link VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(20) DEFAULT NULL, CHANGE wilaya wilaya VARCHAR(100) DEFAULT NULL, CHANGE specialty specialty VARCHAR(100) DEFAULT NULL, CHANGE level level VARCHAR(50) DEFAULT NULL, CHANGE bio bio LONGTEXT DEFAULT NULL, CHANGE industry industry VARCHAR(100) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL, CHANGE latitude latitude VARCHAR(255) DEFAULT NULL, CHANGE longitude longitude VARCHAR(255) DEFAULT NULL, CHANGE verification_file verification_file VARCHAR(255) DEFAULT NULL, CHANGE profile_picture profile_picture VARCHAR(255) DEFAULT NULL, CHANGE university university VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_identifier_email TO UNIQ_8D93D649E7927C74');
        $this->addSql('ALTER TABLE user_skills DROP FOREIGN KEY `FK_SKILL_ID`');
        $this->addSql('ALTER TABLE user_skills DROP FOREIGN KEY `FK_USER_ID`');
        $this->addSql('ALTER TABLE user_skills ADD CONSTRAINT FK_B0630D4DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_skills ADD CONSTRAINT FK_B0630D4D5585C142 FOREIGN KEY (skill_id) REFERENCES skills (id_skill)');
        $this->addSql('ALTER TABLE user_skills RENAME INDEX idx_462ce6f5a76ed395 TO IDX_B0630D4DA76ED395');
        $this->addSql('ALTER TABLE user_skills RENAME INDEX idx_462ce6f55585c142 TO IDX_B0630D4D5585C142');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE applications CHANGE status status VARCHAR(50) DEFAULT \'\'\'pending\'\'\' NOT NULL');
        $this->addSql('ALTER TABLE applications RENAME INDEX idx_f7c966f053c674ee TO FK_APP_OFFER_ID');
        $this->addSql('ALTER TABLE applications RENAME INDEX idx_f7c966f0cb944f1a TO FK_APP_STUDENT_ID');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE offers ADD skills TEXT DEFAULT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE duration duration VARCHAR(100) DEFAULT \'NULL\', CHANGE location_type location_type ENUM(\'Hybrid\', \'On-site\', \'Remote\') DEFAULT \'\'\'On-site\'\'\', CHANGE wilaya wilaya VARCHAR(100) DEFAULT \'NULL\', CHANGE deadline deadline DATE DEFAULT \'NULL\', CHANGE latitude latitude NUMERIC(10, 8) DEFAULT \'NULL\', CHANGE longitude longitude NUMERIC(11, 8) DEFAULT \'NULL\', CHANGE status status ENUM(\'Active\', \'Draft\', \'Closed\') DEFAULT \'\'\'Active\'\'\', CHANGE created_at created_at DATETIME DEFAULT \'current_timestamp()\' NOT NULL, CHANGE start_date start_date DATE DEFAULT \'NULL\', CHANGE internship_start internship_start DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE offers RENAME INDEX idx_da460427979b1ad6 TO fk_offer_user');
        $this->addSql('ALTER TABLE offers_skills DROP FOREIGN KEY FK_23D340E353C674EE');
        $this->addSql('ALTER TABLE offers_skills DROP FOREIGN KEY FK_23D340E35585C142');
        $this->addSql('ALTER TABLE offers_skills ADD CONSTRAINT `fk_offer` FOREIGN KEY (offer_id) REFERENCES offers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offers_skills ADD CONSTRAINT `fk_skill` FOREIGN KEY (skill_id) REFERENCES skills (id_skill) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offers_skills RENAME INDEX idx_23d340e353c674ee TO IDX_offer');
        $this->addSql('ALTER TABLE offers_skills RENAME INDEX idx_23d340e35585c142 TO IDX_skill');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE reset_password_request CHANGE requested_at requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE expires_at expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skills ADD skill_name VARCHAR(255) NOT NULL, CHANGE id_tag id_tag INT DEFAULT NULL, CHANGE tag_name tag_name VARCHAR(100) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE `user` ADD skills LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, DROP portfolio_link, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE first_name first_name VARCHAR(100) DEFAULT \'NULL\', CHANGE last_name last_name VARCHAR(100) DEFAULT \'NULL\', CHANGE company_name company_name VARCHAR(150) DEFAULT \'NULL\', CHANGE created_at created_at DATETIME DEFAULT \'current_timestamp()\' NOT NULL, CHANGE github_link github_link VARCHAR(255) DEFAULT \'NULL\', CHANGE phone phone VARCHAR(20) DEFAULT \'NULL\', CHANGE wilaya wilaya VARCHAR(100) DEFAULT \'NULL\', CHANGE specialty specialty VARCHAR(100) DEFAULT \'NULL\', CHANGE level level VARCHAR(50) DEFAULT \'NULL\', CHANGE bio bio TEXT DEFAULT NULL, CHANGE industry industry VARCHAR(100) DEFAULT \'NULL\', CHANGE website website VARCHAR(255) DEFAULT \'NULL\', CHANGE logo logo VARCHAR(255) DEFAULT \'NULL\', CHANGE university university VARCHAR(255) DEFAULT \'NULL\', CHANGE profile_picture profile_picture VARCHAR(255) DEFAULT \'NULL\', CHANGE latitude latitude NUMERIC(10, 8) DEFAULT \'NULL\', CHANGE longitude longitude NUMERIC(11, 8) DEFAULT \'NULL\', CHANGE verification_file verification_file VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE `user` RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_IDENTIFIER_EMAIL');
        $this->addSql('ALTER TABLE user_skills DROP FOREIGN KEY FK_B0630D4DA76ED395');
        $this->addSql('ALTER TABLE user_skills DROP FOREIGN KEY FK_B0630D4D5585C142');
        $this->addSql('ALTER TABLE user_skills ADD CONSTRAINT `FK_SKILL_ID` FOREIGN KEY (skill_id) REFERENCES skills (id_skill) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_skills ADD CONSTRAINT `FK_USER_ID` FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_skills RENAME INDEX idx_b0630d4da76ed395 TO IDX_462CE6F5A76ED395');
        $this->addSql('ALTER TABLE user_skills RENAME INDEX idx_b0630d4d5585c142 TO IDX_462CE6F55585C142');
    }
}
