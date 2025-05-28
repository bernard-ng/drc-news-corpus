<?php

declare(strict_types=1);

namespace App\SharedKernel\Presentation\Web\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class DefaultController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class DefaultController extends AbstractController
{
    #[Route(
        path: '',
        name: 'default',
        methods: ['GET']
    )]
    public function __invoke(): JsonResponse
    {
        return $this->json([
            'repository' => 'https://github.com/bernard-ng/drc-news-corpus',
            'title' => 'DRC News Corpus : Towards a scalable and intelligent system for Congolese News curation',
            'description' => 'The DRC News Corpus is a structured and scalable dataset of news articles sourced from major media outlets covering diverse aspects of the Democratic Republic of Congo (DRC). Designed for efficiency, this system enables the automated collection, processing, and organization of news stories spanning politics, economy, society, culture, environment, and international affairs.',
            'status' => 200,
        ]);
    }
}
