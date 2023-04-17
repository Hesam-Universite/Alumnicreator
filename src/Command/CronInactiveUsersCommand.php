<?php

namespace App\Command;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'cron:inactiveusers',
    description: 'Command to delete inactive users',
)]
class CronInactiveUsersCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private ParameterRepository $parameterRepository,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    /**
     * send emails and delete inactive accounts
     * PLEASE EXECUTE THAT CRON ONE TIME PER DAY.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $emailFrom = $this->parameterRepository->findOneBy(['code' => Parameter::SMTP_FROM])->getValue();

        if (null !== $emailFrom) {
            $usersTenDaysBeforeDeletion = $this->userRepository->findWhereLastConnectionIs(1085);
            foreach ($usersTenDaysBeforeDeletion as $userId) {
                $user = $this->userRepository->findOneBy(['id' => $userId]);
                $notification = (new Email())
                    ->from($emailFrom)
                    ->to($user->getEmail())
                    ->subject('Risque de suppression de compte | Alumni Creator')
                    ->html('Cela va faire 3 ans que votre compte utilisateur est inactif sur la plateforme Alumni Creator. Veuillez vous reconnecter si vous souhaitez conserver les données associées à ce dernier. Une simple connexion est suffisante à la non suppression de votre compte.');
                $this->mailer->send($notification);
            }

            $usersThreeDaysBeforeDeletion = $this->userRepository->findWhereLastConnectionIs(1092);
            foreach ($usersThreeDaysBeforeDeletion as $userId) {
                $user = $this->userRepository->findOneBy(['id' => $userId]);
                $notification = (new Email())
                    ->from($emailFrom)
                    ->to($user->getEmail())
                    ->subject('Risque de suppression de compte | Alumni Creator')
                    ->html('DERNIER RAPPEL : Cela va faire 3 ans que votre compte utilisateur est inactif sur la plateforme Alumni Creator. Veuillez vous reconnecter si vous souhaitez conserver les données associées à ce dernier. Une simple connexion est suffisante à la non suppression de votre compte.');
                $this->mailer->send($notification);
            }
        }

        $usersToDelete = $this->userRepository->findWhereLastConnectionIsMoreThan(1095);
        foreach ($usersToDelete as $userId) {
            $user = $this->userRepository->findOneBy(['id' => $userId]);
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
