<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source;

use App\Aggregator\Domain\Exception\ArticleOutOfRange;
use App\Aggregator\Domain\Model\ValueObject\DateRange;
use App\Aggregator\Domain\Model\ValueObject\FetchConfig;
use App\Aggregator\Domain\Model\ValueObject\PageRange;

/**
 * Class WordPressJson.
 *
 * Some WordPress websites expose their data in JSON format,
 * this class will help to fetch data from those websites.
 *
 * @see https://developer.wordpress.org/rest-api/
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class WordPressJson extends Source
{
    public const string POST_QUERY = '_fields=date,slug,link,title.rendered,content.rendered,categories&orderby=date&order=desc';

    public const string CATEGORY_QUERY = '_fields=id,slug,count&orderby=count&order=desc&per_page=100';

    public const string TOTAL_PAGES_HEADER = 'x-wp-totalpages';

    public const string TOTAL_POSTS_HEADER = 'x-wp-total';

    private array $categoryMap = [];

    #[\Override]
    public function getPagination(?string $category = null): PageRange
    {
        $response = $this->client->request('GET', sprintf('%s/wp-json/wp/v2/posts?_fields=id&per_page=100', static::URL));
        $headers = $response->getHeaders();
        $pages = (int) $headers[self::TOTAL_PAGES_HEADER][0];
        $posts = (int) $headers[self::TOTAL_POSTS_HEADER][0];

        $this->logger->debug(sprintf('WordPressJson %d posts, %d pages', $posts, $pages));
        return PageRange::from(sprintf('1:%d', $pages));
    }

    #[\Override]
    public function fetch(FetchConfig $config): void
    {
        $this->initialize();
        $page = $config->pageRange ?? $this->getPagination();

        for ($i = $page->start; $i <= $page->end; $i++) {
            try {
                $response = $this->client->request(
                    method: 'GET',
                    url: sprintf('%s/wp-json/wp/v2/posts?%s&page=%d&per_page=100', static::URL, self::POST_QUERY, $i)
                );

                /** @var array $articles */
                $articles = json_decode($this->removeMisconfigurationError($response->getContent()), true);
            } catch (\Throwable $e) {
                $this->logger->error("> page {$i} => {$e->getMessage()} [Failed] ❌");
                continue;
            }

            foreach ($articles as $article) {
                try {
                    $this->fetchOne((string) json_encode($article), $config->dateRange);
                } catch (ArticleOutOfRange) {
                    $this->logger->info('No more articles to fetch in this range.');
                    break;
                }
            }
        }

        $this->completed();
    }

    #[\Override]
    public function fetchOne(string $html, ?DateRange $dateRange = null): void
    {
        try {
            /**
             * @var array{
             *     link:string,
             *     title:array{rendered:string},
             *     content:array{rendered:string},
             *     date:string,
             *     categories:int[]
             * } $data
             */
            $data = json_decode($html, true);

            $link = str_replace(static::URL, '', $data['link']);
            $title = strip_tags($data['title']['rendered']);
            $body = strip_tags($data['content']['rendered']);
            $timestamp = $this->dateParser->createTimeStamp($data['date'], format: 'c');
            $categories = $this->mapCategories($data['categories']);

            if ($dateRange === null || $dateRange->inRange((int) $timestamp)) {
                $this->save($title, $link, $categories, $body, $timestamp);
            } else {
                $this->skip($dateRange, $timestamp, $title, $data['date']);
            }
        } catch (\Throwable $e) {
            $this->logger->error("> {$e->getMessage()} [Failed] ❌");
            return;
        }
    }

    /**
     * edge case for some politico.cd website
     * this invalidates the json, so we have to remove it
     */
    private function removeMisconfigurationError(string $content): string
    {
        $error = '<br />
<b>Notice</b>:  ob_end_flush(): Failed to send buffer of zlib output compression (0) in <b>/home/politico/public_html/wp-includes/functions.php</b> on line <b>5427</b><br />';
        return str_replace($error, '', $content);
    }

    private function fetchCategories(): void
    {
        $response = $this->client->request('GET', sprintf('%s/wp-json/wp/v2/categories?%s', static::URL, self::CATEGORY_QUERY));

        /** @var array{id: int, slug: string}[] $categories */
        $categories = json_decode($response->getContent(), true);

        foreach ($categories as $category) {
            $this->categoryMap[$category['id']] = $category['slug'];
        }
    }

    private function mapCategories(array $categories): string
    {
        if (empty($this->categoryMap)) {
            $this->fetchCategories();
        }

        return strtolower(implode(',', array_map(fn ($category) => $this->categoryMap[$category], $categories)));
    }
}
