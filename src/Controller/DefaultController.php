<?php

namespace App\Controller;

use App\Repository\FeedRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'app_default', methods: ['GET'])]
class DefaultController extends BaseController
{
    public function __invoke(
        FeedRepository $feedRepository,
    ): Response
    {
        $user = $this->getUser();

        return $this->render('default/index.html.twig', [
            'feeds' => $user ? $feedRepository->findUserFeeds($user) : [],
        ]);
    }
}
