<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Feed;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class ImportController extends BaseController
{
    #[Route('/import', name: 'app_import', methods: ['GET'])]
    public function index(
        #[Autowire('%kernel.project_dir%')] string $projectDir,
        EntityManagerInterface                     $entityManager,
    ): Response
    {
        $path = $projectDir . '/public/netvibes-elkuku.xml';

        $feedObjects = [];

        if (file_exists($path)) {
            $xml = simplexml_load_string(file_get_contents($path));

            foreach ($xml->body->outline as $category) {
                $feeds = [];
                foreach ($category->outline as $feed) {
                    $feeds[] = new \App\DTT\Feed(
                        (string)$feed->attributes()->{'title'},
                        (string)$feed->attributes()->{'xmlUrl'},
                        (string)$feed->attributes()->{'htmlUrl'},
                    );
                }

                $feedObjects[(string)$category->attributes()->{'title'}] = $feeds;
            }


            dump($feedObjects);

            $user = $this->getUser();

            foreach ($user->getfeeds() as $object) {
                $entityManager->remove($object);
            }

            foreach ($user->getCategories() as $object) {
                $entityManager->remove($object);
            }

            $entityManager->flush();

            foreach ($feedObjects as $categoryName => $feeds) {
                $category = new Category();
                $category->setName($categoryName);
                $category->setOwner($user);
                $entityManager->persist($category);

                foreach ($feeds as $feedObject) {
                    $feed = new Feed();
                    $feed->setOwner($user);
                    $feed->setCategory($category);
                    $feed->setName($feedObject->title);
                    $feed->setUrl($feedObject->xmlUrl);
                    $entityManager->persist($feed);

                }
            }

            $entityManager->flush();

        } else {
            exit('Failed to open: ' . $path);
        }

        return $this->render('import/index.html.twig',
            [
                'feeds' => $feedObjects,
            ]
        );
    }
}
