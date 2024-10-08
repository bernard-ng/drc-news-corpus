<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Repository;

use App\Aggregator\Domain\Entity\Article;
use App\Aggregator\Domain\ValueObject\DateRange;

/**
 * Interface ArticleRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface ArticleRepository
{
    public function add(Article $article): void;

    public function remove(Article $article): void;

    public function countBySource(string $source): int;

    public function getById(string $id): ?Article;

    public function getByLink(string $link): ?Article;

    public function export(?string $source, ?DateRange $date): array;

    public function getLastCrawlDate(string $source, ?string $category): string;
}
