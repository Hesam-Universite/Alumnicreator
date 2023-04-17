<?php

namespace App\Entity;

use App\Enum\StatusAlumni;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use SLLH\IsoCodesValidator\Constraints as IsoCodesAssert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Serializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Assert\Email(message: 'Cette adresse n\'est pas valide')]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Positive(message: 'Ce statut n\'est pas valide.')]
    private ?int $status = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[AssertPhoneNumber(defaultRegion: 'FR', message: 'Ce numéro de téléphone n\'est pas valide.')]
    private ?string $phone = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire', allowNull: true)]
    #[Assert\Range([
        'min' => 1900,
        'max' => 2100,
        'notInRangeMessage' => 'Cette promotion n\'est pas valide.',
    ])]
    private ?int $class = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire', allowNull: true)]
    private ?string $training = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'Cette URL n\'est pas valide')]
    private ?string $personalLink = null;

    #[ORM\Column(length: 255, nullable: true)]
    /**
     * @IsoCodesAssert\Siret(message="Ce numéro de SIRET n'est pas valide.")
     */
    private ?string $siret = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $roleInTheCompany = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyAddress = null;

    #[Assert\NotCompromisedPassword(message: 'Votre mot de passe est trop faible, merci d\'en choisir un plus robuste', threshold: 50000)]
    private ?string $plainPassword = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $googleId = null;

    #[ORM\Column]
    private ?bool $profileCompleted = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedinId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $microsoftId = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ActivityAreaOther = null;

    #[ORM\Column]
    private ?bool $isApproved = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $registrationDate = null;

    #[Assert\File(maxSize: '2048k', mimeTypes: ['image/png', 'image/jpeg'], mimeTypesMessage: 'Seuls les png et jpg sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'user_pictures', fileNameProperty: 'pictureName')]
    #[Ignore]
    private ?File $pictureFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $pictureName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Job::class, orphanRemoval: true)]
    private Collection $jobs;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Positive(message: 'Ce code postal n\'est pas valide.')]
    #[Assert\Length(min: 5, max: 5, exactMessage: 'Votre code postal n\'est pas valide.')]
    private ?string $postalCode = null;

    #[Assert\NotBlank(message: 'Ce champ est obligatoire', allowNull: true)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Application::class, orphanRemoval: true)]
    private Collection $applications;

    #[ORM\Column]
    private ?bool $acceptedNotifications = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire', allowNull: true)]
    private ?string $linkedinLink = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?DirectoryPage $directoryPage = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RequestContact::class, orphanRemoval: true)]
    private Collection $requestContacts;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserConversation::class, orphanRemoval: true)]
    private Collection $userConversations;

    #[ORM\Column]
    private ?bool $receiveMessageNotificationsByEmail = false;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastConnection = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserGroup::class, orphanRemoval: true)]
    private Collection $userGroups;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Resume $resume = null;

    #[ORM\Column(nullable: true)]
    private ?bool $acceptedCandidacyNotification = null;

    #[ORM\ManyToOne]
    private ?ActivityArea $activityArea = null;

    public function __toString(): string
    {
        return $this->getFirstname().' '.$this->getName();
    }

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->requestContacts = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->userConversations = new ArrayCollection();
        $this->userGroups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getStatus(): ?StatusAlumni
    {
        return StatusAlumni::tryFrom($this->status);
    }

    public function setStatus(?StatusAlumni $status): self
    {
        $this->status = $status->value;

        return $this;
    }

    public function getName(): ?string
    {
        return strtoupper($this->name);
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return ucfirst(strtolower($this->firstname));
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getClass(): ?int
    {
        return $this->class;
    }

    public function setClass(?int $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getTraining(): ?string
    {
        return $this->training;
    }

    public function setTraining(?string $training): self
    {
        $this->training = $training;

        return $this;
    }

    public function getPersonalLink(): ?string
    {
        return $this->personalLink;
    }

    public function setPersonalLink(?string $personalLink): self
    {
        $this->personalLink = $personalLink;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getRoleInTheCompany(): ?string
    {
        return $this->roleInTheCompany;
    }

    public function setRoleInTheCompany(?string $roleInTheCompany): self
    {
        $this->roleInTheCompany = $roleInTheCompany;

        return $this;
    }

    public function getCompanyAddress(): ?string
    {
        return $this->companyAddress;
    }

    public function setCompanyAddress(?string $companyAddress): self
    {
        $this->companyAddress = $companyAddress;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function isProfileCompleted(): ?bool
    {
        return $this->profileCompleted;
    }

    public function setProfileCompleted(bool $profileCompleted): self
    {
        $this->profileCompleted = $profileCompleted;

        return $this;
    }

    public function getLinkedinId(): ?string
    {
        return $this->linkedinId;
    }

    public function setLinkedinId(?string $linkedinId): self
    {
        $this->linkedinId = $linkedinId;

        return $this;
    }

    public function getMicrosoftId(): ?string
    {
        return $this->microsoftId;
    }

    public function setMicrosoftId(?string $microsoftId): self
    {
        $this->microsoftId = $microsoftId;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getActivityAreaOther(): ?string
    {
        return $this->ActivityAreaOther;
    }

    public function setActivityAreaOther(?string $ActivityAreaOther): self
    {
        $this->ActivityAreaOther = $ActivityAreaOther;

        return $this;
    }

    public function isApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): self
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function setPictureFile(?File $pictureFile = null): void
    {
        $this->pictureFile = $pictureFile;

        if (null !== $pictureFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    public function setPictureName(?string $pictureName): void
    {
        $this->pictureName = $pictureName;
    }

    public function getPictureName(): ?string
    {
        return $this->pictureName;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs->add($job);
            $job->setAuthor($this);
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getAuthor() === $this) {
                $job->setAuthor(null);
            }
        }

        return $this;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
        ]);
    }

    public function unserialize(string $data)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
        ) = unserialize($data);
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setUser($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getUser() === $this) {
                $application->setUser(null);
            }
        }

        return $this;
    }

    public function hasAcceptedNotifications(): ?bool
    {
        return $this->acceptedNotifications;
    }

    public function setAcceptedNotifications(bool $acceptedNotifications): self
    {
        $this->acceptedNotifications = $acceptedNotifications;

        return $this;
    }

    public function getLinkedinLink(): ?string
    {
        return $this->linkedinLink;
    }

    public function setLinkedinLink(?string $linkedinLink): self
    {
        if (str_starts_with($linkedinLink, 'https://www.linkedin.com/in/')) {
            $linkedinLink = substr($linkedinLink, 28);
        }

        $this->linkedinLink = $linkedinLink;

        return $this;
    }

    public function getDirectoryPage(): ?DirectoryPage
    {
        return $this->directoryPage;
    }

    public function setDirectoryPage(?DirectoryPage $directoryPage): self
    {
        // unset the owning side of the relation if necessary
        if ($directoryPage === null && $this->directoryPage !== null) {
            $this->directoryPage->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($directoryPage !== null && $directoryPage->getUser() !== $this) {
            $directoryPage->setUser($this);
        }

        $this->directoryPage = $directoryPage;

        return $this;
    }

    /**
     * @return Collection<int, RequestContact>
     */
    public function getRequestContacts(): Collection
    {
        return $this->requestContacts;
    }

    public function addRequestContact(RequestContact $requestContact): self
    {
        if (!$this->requestContacts->contains($requestContact)) {
            $this->requestContacts->add($requestContact);
            $requestContact->setUser($this);
        }

        return $this;
    }

    public function removeRequestContact(RequestContact $requestContact): self
    {
        if ($this->requestContacts->removeElement($requestContact)) {
            // set the owning side to null (unless already changed)
            if ($requestContact->getUser() === $this) {
                $requestContact->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserConversation>
     */
    public function getUserConversations(): Collection
    {
        return $this->userConversations;
    }

    public function addUserConversation(UserConversation $userConversation): self
    {
        if (!$this->userConversations->contains($userConversation)) {
            $this->userConversations->add($userConversation);
            $userConversation->setUser($this);
        }

        return $this;
    }

    public function removeUserConversation(UserConversation $userConversation): self
    {
        if ($this->userConversations->removeElement($userConversation)) {
            // set the owning side to null (unless already changed)
            if ($userConversation->getUser() === $this) {
                $userConversation->setUser(null);
            }
        }

        return $this;
    }

    public function receiveMessageNotificationsByEmail(): ?bool
    {
        return $this->receiveMessageNotificationsByEmail;
    }

    public function setReceiveMessageNotificationsByEmail(bool $receiveMessageNotificationsByEmail): self
    {
        $this->receiveMessageNotificationsByEmail = $receiveMessageNotificationsByEmail;

        return $this;
    }

    public function getLastConnection(): ?\DateTimeInterface
    {
        return $this->lastConnection;
    }

    public function setLastConnection(?\DateTimeInterface $lastConnection): self
    {
        $this->lastConnection = $lastConnection;

        return $this;
    }

    /**
     * @return Collection<int, UserGroup>
     */
    public function getUserGroups(): Collection
    {
        return $this->userGroups;
    }

    public function addUserGroup(UserGroup $userGroup): self
    {
        if (!$this->userGroups->contains($userGroup)) {
            $this->userGroups->add($userGroup);
            $userGroup->setUser($this);
        }

        return $this;
    }

    public function removeUserGroup(UserGroup $userGroup): self
    {
        if ($this->userGroups->removeElement($userGroup)) {
            // set the owning side to null (unless already changed)
            if ($userGroup->getUser() === $this) {
                $userGroup->setUser(null);
            }
        }

        return $this;
    }

    public function getResume(): ?Resume
    {
        return $this->resume;
    }

    public function setResume(Resume $resume): self
    {
        // set the owning side of the relation if necessary
        if ($resume->getUser() !== $this) {
            $resume->setUser($this);
        }

        $this->resume = $resume;

        return $this;
    }

    public function acceptedCandidacyNotification(): ?bool
    {
        return $this->acceptedCandidacyNotification;
    }

    public function setAcceptedCandidacyNotification(?bool $acceptedCandidacyNotification): self
    {
        $this->acceptedCandidacyNotification = $acceptedCandidacyNotification;

        return $this;
    }

    public function getActivityArea(): ?ActivityArea
    {
        return $this->activityArea;
    }

    public function setActivityArea(?ActivityArea $activityArea): self
    {
        $this->activityArea = $activityArea;

        return $this;
    }
}
