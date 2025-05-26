<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL;

use App\FeedManagement\Application\ReadModel\CommentList;
use App\FeedManagement\Application\UseCase\Query\GetArticleCommentList;
use App\FeedManagement\Application\UseCase\QueryHandler\GetArticleCommentListHandler;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class GetArticleCommentListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticleCommentListDbalHandler implements GetArticleCommentListHandler
{
    public function __construct(
        private Connection $connection,
        private PaginatorInterface $paginator
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

        try {
            /** @var SlidingPaginationInterface<int, array<string, mixed>> $data */
            $data = $this->paginator->paginate($qb, $query->page->page, $query->page->limit);
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        return CommentList::create($data->getItems(), $data->getPaginationData());
    }
}
