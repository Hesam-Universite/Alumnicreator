<?php

namespace App\Command;

use App\Entity\Parameter;
use App\Repository\NewsletterCampaignRepository;
use App\Repository\ParameterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'cron:newsletter',
    description: 'Command to send a newsletter campaign',
)]
class CronNewsletterCommand extends Command
{
    public function __construct(
        private NewsletterCampaignRepository $newsletterCampaignRepository,
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private ParameterRepository $parameterRepository,
    ) {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paramImport = $this->parameterRepository->findOneBy(['code' => Parameter::NEWSLETTER_STATUS])->getValue();

        if ($paramImport == 1) {
            $newslettersToSend = $this->newsletterCampaignRepository->findNewslettersToSend();

            foreach ($newslettersToSend as $newsletter) {
                $notification = (new Email())
                    ->from($newsletter->getSendingEmail())
                    ->to($newsletter->getRecipientEmail())
                    ->subject($newsletter->getSubject())
                    ->html($newsletter->getContent());
                $this->mailer->send($notification);

                $newsletter->setIsSent(true);
                $this->entityManager->persist($newsletter);
                $this->entityManager->flush();
            }

            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
