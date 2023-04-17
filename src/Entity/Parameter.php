<?php

namespace App\Entity;

use App\Repository\ParameterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParameterRepository::class)]
#[ORM\Table(name: 'parameters')]
class Parameter
{
    public const ADMIN_EMAIL = 'ADEM';
    public const SMTP_USER = 'SMUT';
    public const SMTP_PASSWORD = 'SMMP';
    public const SMTP_SERVER = 'SMSE';
    public const SMTP_PORT = 'SMPO';
    public const SMTP_FROM = 'SMFR';
    public const DIRECTORY_IMPORT = 'DIIM';
    public const NAME_PAGE_MENU = 'NPMN';
    public const NEWSLETTER_GABARIT = 'NLGB';
    public const NEWSLETTER_STATUS = 'NLST';
    public const NEWS_PRIORITY = 'NWPR';
    public const PRIMARY_COLOR = 'PRCO';
    public const SECONDARY_COLOR = 'SECO';
    public const FOOTER_COLOR = 'FOCO';
    public const MENU_COLOR = 'MECO';
    public const MENU_POSITION = 'MNPO';
    public const MENU_ICONS = 'MEIC';
    public const FONT_H1 = 'FOH1';
    public const FONT_H2 = 'FOH2';
    public const FONT_PARAGRAPH = 'FOPA';
    public const PHONE = 'PHON';
    public const ADDRESS = 'ADRS';
    public const EMAIL = 'MAIL';
    public const FACEBOOK = 'FACE';
    public const FACEBOOK_PAGE_TOKEN = 'FBTO';
    public const INSTAGRAM = 'INST';
    public const TWITTER = 'TWIT';
    public const GOOGLE_FONTS_TOKEN = 'GOFT';
    public const FACEBOOK_PAGE_ID = 'FAPI';
    public const CONTENT_PRIVACY_POLICY = 'COPP';
    public const CONTENT_PRIVACY_AND_COOKIES = 'COPC';
    public const CODE_MATOMO = 'MTMO';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
