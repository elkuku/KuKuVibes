<?php

namespace App\Repository;

use App\Entity\Feed;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SimplePie\SimplePie;

/**
 * @extends ServiceEntityRepository<Feed>
 */
class FeedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feed::class);
    }

    /**
     * @return Feed[] Returns an array of Feed objects
     */
    public function findUserFeeds(User $user): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.owner = :val')
            ->setParameter('val', $user)
            ->orderBy('f.id', 'ASC')
            //  ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Feed
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function fetch(Feed $feed): \stdClass
    {
        $pie = new SimplePie;

        // I may decide to enable this later, for now it's fine the way it is
        $pie->enable_cache(false);

        // the original feed URL that I want to retrieve
        $pie->set_feed_url($feed->getUrl());

        if (false === $pie->init()) {
            $message=$pie->error()?$pie->error():'Could not initialize Pie library';
            throw new \RuntimeException($message);
        }

        $DD = new \stdClass();
        $DD->title = $pie->get_title();
        $DD->description = $pie->get_description();
        $DD->last_fetched_at = 'now...';
        $DD->link = $pie->subscribe_url(); // can be different than the original feed URL
        $DD->site_link = $pie->get_base();

        $feed_items = [];

        foreach ($pie->get_items() as $item) {
            $feed_items[] = [
                'title' => $item->get_title(),
                'link' => $item->get_permalink(),
                'description' => $item->get_description(),
                'author' => $item->get_author()->name,
                'guid' => $item->get_id(),
                'published_date' => $item->get_date(),
                'updated_date' => $item->get_updated_date(),
            ];
        }
        $DD->feed_items = $feed_items;

        return $DD;
    }
}
