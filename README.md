# DRC News Corpus

![Coding Standard](https://github.com/bernard-ng/drc-news-corpus/actions/workflows/quality.yaml/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/bernard-ng/drc-news-corpus/version)](https://packagist.org/packages/bernard-ng/drc-news-corpus)
[![License](https://poser.pugx.org/bernard-ng/drc-news-corpus/license)](https://packagist.org/packages/bernard-ng/drc-news-corpus)

The "DRC News Corpus" is a curated collection of news articles sourced from major media outlets covering a wide spectrum of topics related to the Democratic Republic of Congo (DRC). 
This dataset encompasses a diverse range of news stories, including but not limited to politics, economy, social issues, culture, environment, and international relations, providing comprehensive coverage of events and developments within the country.

**Use Cases:**

Researchers, journalists, policymakers, and data enthusiasts interested in understanding the socio-political climate, economic dynamics, and other facets of the DRC will find this dataset valuable. It serves as a resource for sentiment analysis, trend identification, language modeling, and other natural language processing (NLP) tasks.

Efforts have been made to ensure the dataset's integrity and quality by including articles from reputable news outlets. However, users are encouraged to exercise discretion and validate the information independently as journalistic standards and perspectives may vary among sources.

## Sources

| Source         | Supported | Articles | Link                                        | Last Crawled |
|----------------|-----------|----------|---------------------------------------------|--------------|
| radiookapi.net | Yes       | +90k     | https://www.radiookapi.net/actualite        | NA           |
| actualite.cd   | Yes       | +16k     | https://actualite.cd/                       | NA           |
| 7sur7.cd       | Yes       | +16k     | https://7sur7.cd                            | NA           |
| politico.cd    | Yes       | +50k     | https://www.politico.cd/rubrique/encontinu/ | NA           |
| mediacongo.cd  | Yes       | +25k     | https://www.mediacongo.net/                 | NA           |
| rfi.fr         | No        | +13k     | https://www.rfi.fr/fr/tag/rdc               | NA           |
| lemonde.fr     | NO        |          | https://www.lemonde.fr/congo-rdc/2          | NA           |
| lefigaro.fr    | NO        |          | https://www.lefigaro.fr/tag/rdc             |              |


## Download the dataset
- timespan : 2004-2023
- last update : 2023-11-30

[DRC News Corpus on Kaggle](https://www.kaggle.com/datasets/bernardngandu/drc-news-corpus)

## Build the dataset
If you want to rebuild the dataset follow the steps bellow : 

### Installation
```bash
git clone https://github.com/bernard-ng/drc-news-corpus.git && cd drc-news-corpus
make build
make start
```

**Database Configuration**
If you're not using docker, you can configure the database connection in the `.env` file.
then run the following command to create the database schema:
```bash
composer corpus:migrations
```

### Usage
See supported sources above. you can also add your own source by extending the `Source` abstract class.
if you want to crawl `radiookapi.net` you can run the following command:

1. **Crawling**
```bash
php bin/console app:crawl radiookapi.net

# You can specify a date range to crawl articles.
php bin/console app:crawl politico.cd --date="2022-01-01:2022-12-31"

# You can specify a page range to crawl articles.
php bin/console app:crawl mediacongo.net --page="0:6" 

# You can specify both date and page range.
php bin/console app:crawl actualite.cd --date="2022-01-01:2022-12-31" --page="0:6"

# some sources require a category to crawl articles.
php bin/console app:crawl 7sur7.cd --category=politique

# You can crawl multiple pages in parallel.
php bin/console app:crawl radiookapi.net --parallel=20
```

2. **Updating**
```bash
# Update the database with the latest articles.
php bin/console app:update radiookapi.net
```

Notice that this can take a while depending on the number of articles you want to crawl and will store the articles in the database.
running this command in the background is recommended. by default no output is generated, you can add the `-v` option to see the progress.

```bash
nohup php bin/console app:crawl radiookapi.net -v > crawling.log
```

## Export the dataset
You can export the dataset to a CSV file by running the following command:

```bash
php bin/console app:export

# You can specify a date range to export articles.
php bin/console app:export --date="2022-01-01:2022-12-31"

# You can specify a source to export articles.
php bin/console app:export --source=radiookapi.net

# you can specify both date and source.
php bin/console app:export --date="2022-01-01:2022-12-31" --source=radiookapi.net
```
a CSV file will be generated in the `data` directory.

## Contributors
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

<a href="https://github.com/bernard-ng/drc-news-corpus/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=bernard-ng/drc-news-corpus"/>
</a>

## Acknowledgment:

The compilation and curation of the "DRC News Corpus" were conducted by Tshabu Ngandu Bernard with the primary objective of facilitating research and analysis related to the Democratic Republic of Congo. 
I don't forget to cite this repository if you consider to use the data or this software. 
