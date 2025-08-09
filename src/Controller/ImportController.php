<?php

namespace App\Controller;

use App\DTT\Feed;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class ImportController extends AbstractController
{
    #[Route('/import', name: 'app_import', methods: ['GET'])]
    public function index(
        #[Autowire('%kernel.project_dir%')] string $projectDir,
    ): Response
    {
        $path = $projectDir . '/public/netvibes-elkuku.xml';

        $feedObjects = [];

        if (file_exists($path)) {
            $xml = simplexml_load_string(file_get_contents($path));

            foreach ($xml->body->outline as $category) {
                $feeds = [];
                foreach ($category->outline as $feed) {
                    $feeds[] = new Feed(
                        (string)$feed->attributes()->{'title'},
                        (string)$feed->attributes()->{'xmlUrl'},
                        (string)$feed->attributes()->{'htmlUrl'},
                    );
                }

                $feedObjects[(string)$category->attributes()->{'title'}] = $feeds;
            }

var_dump($feedObjects);
        } else {
            exit('Failed to open examples/book.xml.');
        }

        return $this->render('import/index.html.twig', [
            'controller_name' => 'ImportController',
        ]);
    }
}
