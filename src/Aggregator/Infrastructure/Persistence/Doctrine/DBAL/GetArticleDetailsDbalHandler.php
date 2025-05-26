<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\ArticleDetails;
use App\Aggregator\Application\UseCase\Query\GetArticleDetails;
use App\Aggregator\Application\UseCase\QueryHandler\GetArticleDetailsHandler;
use App\Aggregator\Domain\Exception\ArticleNotFound;
use App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Features\BookmarkQuery;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

/**
 * Class GetArticleDetailsDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticleDetailsDbalHandler implements GetArticleDetailsHandler
{
    use BookmarkQuery;

    public function __construct(
        private Connection $connection,
    ) {
    }

    #[\Override]
    public function __invoke(GetArticleDetails $query): ArticleDetails
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'a.id as article_id',
                'a.title as article_title',
                'a.link as article_link',
                'a.categories as article_categories',
                'a.body as article_body',
                'a.hash as article_hash',
                'a.published_at as article_published_at',
                'a.crawled_at as article_crawled_at',
                'a.updated_at as article_updated_at',
                'a.bias as article_bias',
                'a.reliability as article_reliability',
                'a.transparency as article_transparency',
                'a.sentiment as article_sentiment',
                'a.metadata as article_metadata',
                'a.reading_time as article_reading_time',
            )
            ->addSelect(
                's.display_name as source_display_name',
                "CONCAT('https://devscast.org/images/sources/', s.name, '.png') as source_image",
                's.url as source_url',
                's.name as source_name'
            )
            ->addSelect(sprintf('%s as article_is_bookmarked', $this->isArticleBookmarkedQuery()))
            ->innerJoin('a', 'source', 's', 'a.source = s.name')
            ->from('article', 'a')
            ->where('a.id = :articleId')
            ->setParameter('articleId', $query->id->toBinary(), ParameterType::BINARY)
            ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY)
        ;

        try {
            /** @var array<string, mixed>|false $data */
            $data = $qb->executeQuery()->fetchAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        if ($data === false) {
            throw ArticleNotFound::withId($query->id);
        }

        return ArticleDetails::create($data);
    }
}
