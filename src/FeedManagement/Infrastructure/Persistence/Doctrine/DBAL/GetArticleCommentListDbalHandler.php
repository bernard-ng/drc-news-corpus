<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL;

use App\FeedManagement\Application\ReadModel\CommentList;
use App\FeedManagement\Application\UseCase\Query\GetArticleCommentList;
use App\FeedManagement\Application\UseCase\QueryHandler\GetArticleCommentListHandler;
use App\SharedKernel\Domain\Model\Pagination\PaginatorKeyset;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Features\PaginationQuery;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

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

        $qb = $this->applyCursorPagination($qb, $query->page, new PaginatorKeyset('c.id', 'c.created_at'));

        try {
            $data = $qb->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        $pagination = $this->createPaginationInfo($data, $query->page, new PaginatorKeyset('comment_id', 'comment_created_at'));
        return CommentList::create($data, $pagination);
    }
}
