<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\Repository;

use App\Aggregator\Domain\Model\Entity\Article;
use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Crawling\DateRange;

/**
 * Interface ArticleRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface ArticleRepository
{
    public function add(Article $article): void;

    public function remove(Article $article): void;

    public function getById(ArticleId $id): ?Article;

    public function getByHash(string $hash): ?Article;

    public function export(?string $source, ?DateRange $date): \Generator;

    public function clear(string $source, ?string $category): int;
}
