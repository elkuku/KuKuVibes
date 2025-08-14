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

        if ($user) {
            $categories = $user->getCategories();
            $selectedCategory = $categories[0];//TODO: user select category

            return $this->render('default/index.html.twig', [
                'categories' => $categories,
                'selectedCategory' => $selectedCategory,
            ]);
        }

        return $this->render('default/index.html.twig', [
            'feeds' => [],
            'categories' => [],
        ]);
    }
}
