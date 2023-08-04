<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE api (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, data_index VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE temperature (id INT AUTO_INCREMENT NOT NULL, api_id INT NOT NULL, query_time DATETIME NOT NULL, country VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    /**
     * Add the 2 default API data sources (OpenWeather, Weather API)
     */
    public function postUp(Schema $schema): void
    {
        $this->connection->executeQuery("INSERT INTO `api` (`id`, `name`, `url`, `data_index`) VALUES (NULL, 'Open Weather', 'http://api.openweathermap.org/data/2.5/weather?appid=2db943801f3e358e0177d89870e751b9&units=metric&q={CITY}', 'main,temp'), (NULL, 'Weather API', 'http://api.weatherapi.com/v1/current.json?key=4f536463e4cd4c5da28165415210410&q={CITY}', 'current,temp_c')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE api');
        $this->addSql('DROP TABLE temperature');
    }
}
