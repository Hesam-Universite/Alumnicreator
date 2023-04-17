<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320145949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_area (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE applications (id INT AUTO_INCREMENT NOT NULL, job_id INT NOT NULL, user_id INT NOT NULL, creation_date DATE NOT NULL, additional_file_name VARCHAR(255) DEFAULT NULL, additional_file_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F7C966F0BE04EA9 (job_id), INDEX IDX_F7C966F0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, group_article_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) DEFAULT NULL, author VARCHAR(255) NOT NULL, published_at DATETIME DEFAULT NULL, content LONGTEXT DEFAULT NULL, status INT NOT NULL, meta_description VARCHAR(255) DEFAULT NULL, featured_image_name VARCHAR(255) DEFAULT NULL, featured_image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', tag VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_BFDD3168989D9B62 (slug), INDEX IDX_BFDD3168A559E72E (group_article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_requests (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, object VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, email VARCHAR(255) NOT NULL, creation_date DATE NOT NULL, INDEX IDX_E1A04AC6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contents (id INT AUTO_INCREMENT NOT NULL, logo_name VARCHAR(255) DEFAULT NULL, logo_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', favicon_name VARCHAR(255) DEFAULT NULL, favicon_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', hero_image_name VARCHAR(255) DEFAULT NULL, hero_image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', students_image_name VARCHAR(255) DEFAULT NULL, students_image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', companies_image_name VARCHAR(255) DEFAULT NULL, companies_image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', directory_image_name VARCHAR(255) DEFAULT NULL, directory_image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', career_image_name VARCHAR(255) DEFAULT NULL, career_image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', main_title VARCHAR(255) DEFAULT NULL, youtube_video_link VARCHAR(255) DEFAULT NULL, paragraph_one LONGTEXT DEFAULT NULL, paragraph_two LONGTEXT DEFAULT NULL, paragraph_three LONGTEXT DEFAULT NULL, paragraph_four LONGTEXT DEFAULT NULL, paragraph_five LONGTEXT DEFAULT NULL, paragraph_six LONGTEXT DEFAULT NULL, paragraph_seven LONGTEXT DEFAULT NULL, paragraph_eight LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conversations (id INT AUTO_INCREMENT NOT NULL, last_message DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE custom_fonts (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, font_file_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE directory_pages (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, class INT NOT NULL, email VARCHAR(255) NOT NULL, linkedin_link VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_6E06B128A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE directory_pages_instance (id INT AUTO_INCREMENT NOT NULL, instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', other_instance_id INT DEFAULT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, class INT NOT NULL, email VARCHAR(255) NOT NULL, linkedin_link VARCHAR(255) DEFAULT NULL, user_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, group_event_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, start DATETIME DEFAULT NULL, end DATETIME DEFAULT NULL, all_day TINYINT(1) NOT NULL, start_fullday DATE DEFAULT NULL, end_fullday DATE DEFAULT NULL, INDEX IDX_5387574A78C7A4F4 (group_event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feeds (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE footer_columns (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groups (id INT AUTO_INCREMENT NOT NULL, activity_area_id INT NOT NULL, name VARCHAR(255) NOT NULL, visibility INT NOT NULL, location VARCHAR(255) DEFAULT NULL, is_approved TINYINT(1) NOT NULL, approval_notification_sent TINYINT(1) NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_F06D3970BD5D367C (activity_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE instances (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, local_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', external_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', instance_url VARCHAR(255) NOT NULL, allow_other_instance TINYINT(1) NOT NULL, allow_share_jobs TINYINT(1) NOT NULL, allow_share_resumes TINYINT(1) NOT NULL, allow_share_companies TINYINT(1) NOT NULL, allow_share_students TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_7A2700695D5A2101 (local_id), UNIQUE INDEX UNIQ_7A2700699F75D7B0 (external_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jobs (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, activity_area_id INT NOT NULL, title VARCHAR(255) NOT NULL, company_presentation VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, desired_level LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', type_of_contract INT NOT NULL, city VARCHAR(255) NOT NULL, remuneration NUMERIC(10, 2) DEFAULT NULL, contact_email VARCHAR(255) NOT NULL, link_to_the_job_offer VARCHAR(255) NOT NULL, start_date DATE DEFAULT NULL, deadline_job_offer DATE DEFAULT NULL, company_logo_name VARCHAR(255) DEFAULT NULL, company_logo_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', attachment_name VARCHAR(255) DEFAULT NULL, attachment_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_approved TINYINT(1) NOT NULL, creation_date DATE NOT NULL, postal_code VARCHAR(255) NOT NULL, INDEX IDX_A8936DC5F675F31B (author_id), INDEX IDX_A8936DC5BD5D367C (activity_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jobs_instance (id INT AUTO_INCREMENT NOT NULL, instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, company_presentation VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, desired_level LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', type_of_contract INT NOT NULL, city VARCHAR(255) NOT NULL, remuneration VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) NOT NULL, link_to_the_job_offer VARCHAR(255) DEFAULT NULL, start_date DATETIME DEFAULT NULL, deadline_job_offer DATETIME DEFAULT NULL, creation_date DATETIME NOT NULL, postal_code VARCHAR(255) NOT NULL, other_instance_id INT DEFAULT NULL, picture_name VARCHAR(255) DEFAULT NULL, company_name VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, author_id INT NOT NULL, activity_area VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_group (id INT AUTO_INCREMENT NOT NULL, media_group_id INT NOT NULL, media_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_72D98074F135F7F5 (media_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, conversation_id INT NOT NULL, content LONGTEXT NOT NULL, sending_time DATETIME NOT NULL, INDEX IDX_DB021E96F675F31B (author_id), INDEX IDX_DB021E969AC0396 (conversation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter_campaigns (id INT AUTO_INCREMENT NOT NULL, subject VARCHAR(255) NOT NULL, sending_time DATETIME DEFAULT NULL, content LONGTEXT DEFAULT NULL, sending_email VARCHAR(255) NOT NULL, recipient_email VARCHAR(255) NOT NULL, is_sent TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pages (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, status INT NOT NULL, meta_description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parameters (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(4) NOT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_69348FE77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resumes (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, activity_area_id INT NOT NULL, skill_id INT NOT NULL, presentation VARCHAR(255) NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', additional_file_name VARCHAR(255) DEFAULT NULL, resume_name VARCHAR(255) NOT NULL, resume_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', activity_area_other VARCHAR(255) DEFAULT NULL, status INT NOT NULL, UNIQUE INDEX UNIQ_CDB8AD33A76ED395 (user_id), INDEX IDX_CDB8AD33BD5D367C (activity_area_id), INDEX IDX_CDB8AD335585C142 (skill_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resumes_instance (id INT AUTO_INCREMENT NOT NULL, other_instance_id INT DEFAULT NULL, instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, presentation VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, birthday DATETIME DEFAULT NULL, status INT NOT NULL, user_postal_code VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, resume_name VARCHAR(255) NOT NULL, additional_file_name VARCHAR(255) DEFAULT NULL, activity_area VARCHAR(255) DEFAULT NULL, activity_area_other VARCHAR(255) DEFAULT NULL, skill VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skills (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE social_posts (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, time_to_published DATETIME NOT NULL, is_sent TINYINT(1) NOT NULL, post_image_name VARCHAR(255) DEFAULT NULL, post_image_updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_conversation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, conversation_id INT NOT NULL, last_visit DATETIME DEFAULT NULL, last_notification DATETIME DEFAULT NULL, INDEX IDX_A425AEBA76ED395 (user_id), INDEX IDX_A425AEB9AC0396 (conversation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group (id INT AUTO_INCREMENT NOT NULL, user_group_id INT NOT NULL, user_id INT NOT NULL, role_in_group INT NOT NULL, accepted_the_invitation TINYINT(1) NOT NULL, INDEX IDX_8F02BF9D1ED93D47 (user_group_id), INDEX IDX_8F02BF9DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, activity_area_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) DEFAULT NULL, status INT DEFAULT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, birthday DATE DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, class INT DEFAULT NULL, training VARCHAR(255) DEFAULT NULL, personal_link VARCHAR(255) DEFAULT NULL, siret VARCHAR(255) DEFAULT NULL, company_name VARCHAR(255) DEFAULT NULL, role_in_the_company VARCHAR(255) DEFAULT NULL, company_address VARCHAR(255) DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, profile_completed TINYINT(1) NOT NULL, linkedin_id VARCHAR(255) DEFAULT NULL, microsoft_id VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, activity_area_other VARCHAR(255) DEFAULT NULL, is_approved TINYINT(1) NOT NULL, registration_date DATE NOT NULL, picture_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', postal_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, accepted_notifications TINYINT(1) NOT NULL, linkedin_link VARCHAR(255) DEFAULT NULL, receive_message_notifications_by_email TINYINT(1) NOT NULL, last_connection DATE DEFAULT NULL, accepted_candidacy_notification TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), INDEX IDX_1483A5E9BD5D367C (activity_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_instance (id INT AUTO_INCREMENT NOT NULL, instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', other_instance_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', status INT DEFAULT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, birthday DATE DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, class INT DEFAULT NULL, training VARCHAR(255) DEFAULT NULL, personal_link VARCHAR(255) DEFAULT NULL, siret VARCHAR(255) DEFAULT NULL, company_name VARCHAR(255) DEFAULT NULL, role_in_the_company VARCHAR(255) DEFAULT NULL, company_address VARCHAR(255) DEFAULT NULL, activity_area_other VARCHAR(255) DEFAULT NULL, registration_date DATE NOT NULL, picture_name VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, linkedin_link VARCHAR(255) DEFAULT NULL, activity_area VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F0BE04EA9 FOREIGN KEY (job_id) REFERENCES jobs (id)');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F0A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168A559E72E FOREIGN KEY (group_article_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE contact_requests ADD CONSTRAINT FK_E1A04AC6A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE directory_pages ADD CONSTRAINT FK_6E06B128A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A78C7A4F4 FOREIGN KEY (group_event_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE groups ADD CONSTRAINT FK_F06D3970BD5D367C FOREIGN KEY (activity_area_id) REFERENCES activity_area (id)');
        $this->addSql('ALTER TABLE jobs ADD CONSTRAINT FK_A8936DC5F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE jobs ADD CONSTRAINT FK_A8936DC5BD5D367C FOREIGN KEY (activity_area_id) REFERENCES activity_area (id)');
        $this->addSql('ALTER TABLE media_group ADD CONSTRAINT FK_72D98074F135F7F5 FOREIGN KEY (media_group_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E969AC0396 FOREIGN KEY (conversation_id) REFERENCES conversations (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE resumes ADD CONSTRAINT FK_CDB8AD33A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE resumes ADD CONSTRAINT FK_CDB8AD33BD5D367C FOREIGN KEY (activity_area_id) REFERENCES activity_area (id)');
        $this->addSql('ALTER TABLE resumes ADD CONSTRAINT FK_CDB8AD335585C142 FOREIGN KEY (skill_id) REFERENCES skills (id)');
        $this->addSql('ALTER TABLE user_conversation ADD CONSTRAINT FK_A425AEBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_conversation ADD CONSTRAINT FK_A425AEB9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversations (id)');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9D1ED93D47 FOREIGN KEY (user_group_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9BD5D367C FOREIGN KEY (activity_area_id) REFERENCES activity_area (id)');

        $this->addSql("
            INSERT INTO parameters (id, code, name, value) VALUES 
                (1, 'ADEM', 'ADMIN_EMAIL', ''),
                (2, 'SMUT', 'SMTP_USER', ''),
                (3, 'SMMP', 'SMTP_PASSWORD', ''),
                (4, 'SMSE', 'SMTP_SERVER', ''),
                (5, 'SMPO', 'SMTP_PORT', ''),
                (6, 'SMFR', 'SMTP_FROM', ''),
                (7, 'DIIM', 'DIRECTORY_IMPORT', '2'),
                (8, 'NWPR', 'NEWS_PRIORITY', '2'),
                (9, 'NLGB', 'NEWSLETTER_GABARIT', ''),
                (10, 'NLST', 'NEWSLETTER_STATUS', '0'),
                (11, 'NPMN', 'NAME_PAGE_MENU', 'Notre école'),
                (13, 'PRCO', 'PRIMARY_COLOR', '#D3343A'),
                (14, 'SECO', 'SECONDARY_COLOR', '#AC2A2F'),
                (15, 'FOCO', 'FOOTER_COLOR', '#343434'),
                (16, 'MNPO', 'MENU_POSITION', '1'),
                (17, 'FOH1', 'FONT_H1', '[''Open Sans'', ''https://fonts.googleapis.com/css?family=Open+Sans'']'),
                (18, 'FOH2', 'FONT_H2', '[''Open Sans'', ''https://fonts.googleapis.com/css?family=Open+Sans'']'),
                (19, 'FOPA', 'FONT_PARAGRAPH', '[''Open Sans'', ''https://fonts.googleapis.com/css?family=Open+Sans'']'),
                (20, 'PHON', 'PHONE', '0187392020'),
                (21, 'ADRS', 'ADDRESS', '15 rue Soufflot 75005 Paris'),
                (22, 'MAIL', 'EMAIL', 'contact@hesam.com'),
                (23, 'MECO', 'MENU_COLOR', '#EEEEEE'),
                (24, 'FACE', 'FACEBOOK', '#'),
                (25, 'INST', 'INSTAGRAM', '#'),
                (26, 'TWIT', 'TWITTER', '#'),
                (27, 'GOFT', 'GOOGLE_FONTS_TOKEN', ''),
                (28, 'FAPI', 'FACEBOOK_PAGE_ID', ''),
                (29, 'COPP', 'CONTENT_PRIVACY_POLICY', ''),
                (30, 'COPC', 'CONTENT_PRIVACY_AND_COOKIES', ''),
                (31, 'MEIC', 'MENU_ICONS', '2'),
                (32, 'MTMO', 'CODE_MATOMO', ''),
                (33, 'FBTO', 'FACEBOOK_TOKEN', '')
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F0BE04EA9');
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F0A76ED395');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168A559E72E');
        $this->addSql('ALTER TABLE contact_requests DROP FOREIGN KEY FK_E1A04AC6A76ED395');
        $this->addSql('ALTER TABLE directory_pages DROP FOREIGN KEY FK_6E06B128A76ED395');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A78C7A4F4');
        $this->addSql('ALTER TABLE groups DROP FOREIGN KEY FK_F06D3970BD5D367C');
        $this->addSql('ALTER TABLE jobs DROP FOREIGN KEY FK_A8936DC5F675F31B');
        $this->addSql('ALTER TABLE jobs DROP FOREIGN KEY FK_A8936DC5BD5D367C');
        $this->addSql('ALTER TABLE media_group DROP FOREIGN KEY FK_72D98074F135F7F5');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96F675F31B');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E969AC0396');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE resumes DROP FOREIGN KEY FK_CDB8AD33A76ED395');
        $this->addSql('ALTER TABLE resumes DROP FOREIGN KEY FK_CDB8AD33BD5D367C');
        $this->addSql('ALTER TABLE resumes DROP FOREIGN KEY FK_CDB8AD335585C142');
        $this->addSql('ALTER TABLE user_conversation DROP FOREIGN KEY FK_A425AEBA76ED395');
        $this->addSql('ALTER TABLE user_conversation DROP FOREIGN KEY FK_A425AEB9AC0396');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9D1ED93D47');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DA76ED395');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9BD5D367C');
        $this->addSql('DROP TABLE activity_area');
        $this->addSql('DROP TABLE applications');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE contact_requests');
        $this->addSql('DROP TABLE contents');
        $this->addSql('DROP TABLE conversations');
        $this->addSql('DROP TABLE custom_fonts');
        $this->addSql('DROP TABLE directory_pages');
        $this->addSql('DROP TABLE directory_pages_instance');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE feeds');
        $this->addSql('DROP TABLE footer_columns');
        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE instances');
        $this->addSql('DROP TABLE jobs');
        $this->addSql('DROP TABLE jobs_instance');
        $this->addSql('DROP TABLE media_group');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE newsletter_campaigns');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE parameters');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE resumes');
        $this->addSql('DROP TABLE resumes_instance');
        $this->addSql('DROP TABLE skills');
        $this->addSql('DROP TABLE social_posts');
        $this->addSql('DROP TABLE user_conversation');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_instance');
    }
}
