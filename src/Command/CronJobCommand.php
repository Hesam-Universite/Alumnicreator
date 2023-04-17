<?php

namespace App\Command;

use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'cron:job',
    description: 'Command to delete job older than 90 days',
)]
class CronJobCommand extends Command
{
    public function __construct(
        private JobRepository $jobRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jobs = $this->jobRepository->findJobOlderThanNinetyDays();

        foreach ($jobs as $job) {
            $this->entityManager->remove($job);
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
