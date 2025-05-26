<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL;

use App\FeedManagement\Application\ReadModel\CommentList;
use App\FeedManagement\Application\UseCase\Query\GetArticleCommentList;
use App\FeedManagement\Application\UseCase\QueryHandler\GetArticleCommentListHandler;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Features\PaginationQuery;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class GetArticleCommentListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticleCommentListDbalHandler implements GetArticleCommentListHandler
{
    use PaginationQuery;

    public function __construct(
        private Connection $connection,
    ) {
    }

    public function __invoke(GetArticleCommentList $query): CommentList
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'c.id as comment_id',
                'c.content as comment_content',
                'c.sentiment as comment_sentiment',
                'c.created_at as comment_created_at'
            )
            ->addSelect('u.id as user_id, u.name as user_name')
            ->from('comment', 'c')
            ->innerJoin('c', 'user', 'u', 'c.user_id = u.id')
            ->where('c.article_id = :articleId')
            ->orderBy('c.created_at', 'DESC')
            ->setParameter('articleId', $query->articleId->toBinary(), ParameterType::BINARY);

        $qb = $this->paginate($qb, $query);

        try {
            /** @var array<int, array<string, mixed>> $data */
            $data = $qb->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        $pagination = $this->getPagination($data, $query->page, 'comment_id');
        return CommentList::create($data, $pagination);
    }

    private function paginate(QueryBuilder $qb, GetArticleCommentList $query): QueryBuilder
    {
        return $this->applyCursorPagination($qb, $query->page, 'c.id', fn () => $this->connection->createQueryBuilder()
            ->select('c.id')
            ->from('comment', 'c')
            ->where('c.article_id = :articleId')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('articleId', $query->articleId->toBinary(), ParameterType::BINARY)
            ->executeQuery()
            ->fetchOne());
    }
}
