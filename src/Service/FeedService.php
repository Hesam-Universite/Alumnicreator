<?php

namespace App\Service;

use App\Entity\Article;
use App\Repository\FeedRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class FeedService
{
    public function __construct(
        private FeedRepository $feedRepository,
        private CacheInterface $cache,
    ) {
    }

    public function addExternalFeeds(array &$articles)
    {
        $feeds = $this->cache->get('external_feeds', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $feeds = $this->feedRepository->findAll();
            $art = [];
            foreach ($feeds as $feed) {
                try {
                    $this->downloadAndParse($feed->getUrl(), $art);
                } catch (\Exception $e) {
                    continue;
                }
            }

            return $art;
        });

        $articles += $feeds;
    }

    private function downloadAndParse(string $url, array &$articles)
    {
        $rss = \Feed::loadRss($url);
        foreach ($rss->item as $item) {
            $content = 'content:encoded';

            $article = (new Article())
                ->setTitle($item->title)
                ->setContent($item->description ?? $item->content ?? $item->$content ?? null)
                ->setSlug($item->link)
                ->setPublishedAt(new \DateTime($item->pubDate))
                ->setIsFromExternalFeed(true);

            $articles[] = $article;
        }
    }
}
