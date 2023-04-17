<?php

namespace App\Command;

use App\Repository\ParameterRepository;
use App\Repository\SocialPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'cron:publishposts',
    description: 'Command to publish posts on Facebook',
)]
class CronPublishPosts extends Command
{
    public function __construct(
        private SocialPostRepository $socialPostRepository,
        private ParameterRepository $parameterRepository,
        private HttpClientInterface $client,
        private EntityManagerInterface $entityManager,
    )
    {
        parent::__construct();
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $facebookToken = $this->parameterRepository->findOneBy(['code' => 'FBTO'])->getValue();
        $facebookUserId = $this->parameterRepository->findOneBy(['code' => 'FAPI'])->getValue();

        if (!empty($facebookToken)) {
            $response = $this->client->request('GET', 'https://graph.facebook.com/' . $facebookUserId . '/accounts', [
                'query' => [
                    'access_token' => $facebookToken,
                ],
            ]);

            $pageId = $response->toArray()['data'][0]['id'];
            $pageToken = $response->toArray()['data'][0]['access_token'];

            $postsToPublish = $this->socialPostRepository->findSocialPostsToSend();

            try {
                foreach ($postsToPublish as $post) {
                    $this->client->request('POST', 'https://graph.facebook.com/' . $pageId . '/feed', [
                        'query' => [
                            'message' => $post->getContent(),
                            'access_token' => $pageToken,
                        ],
                    ]);

                    $post->setIsSent(true);
                    $this->entityManager->flush();
                }

                return Command::SUCCESS;
            } catch (\Exception $e) {
                return Command::FAILURE;
            }
        }

        return Command::FAILURE;
    }
}
