<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\SharedKernel\Domain\DataTransfert\DataMapping;

/**
 * Class ExportedArticle.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ArticleForExport
{
    public function __construct(
        public ArticleId $id,
        public string $title,
        public string $link,
        public string $categories,
        public string $body,
        public string $source,
        public string $hash,
        public \DateTimeImmutable $publishedAt,
        public \DateTimeImmutable $crawledAt
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            ArticleId::fromBinary($item['article_id']),
            DataMapping::string($item, 'article_title'),
            DataMapping::string($item, 'article_link'),
            DataMapping::string($item, 'article_categories'),
            DataMapping::string($item, 'article_body'),
            DataMapping::string($item, 'article_source'),
            DataMapping::string($item, 'article_hash'),
            DataMapping::datetime($item, 'article_published_at'),
            DataMapping::datetime($item, 'article_crawled_at')
        );
    }
}
