# DRC News Corpus : Towards a scalable and efficient system for Congolese news dataset curation

![Coding Standard](https://github.com/bernard-ng/drc-news-corpus/actions/workflows/quality.yaml/badge.svg)
![Tests](https://github.com/bernard-ng/drc-news-corpus/actions/workflows/tests.yaml/badge.svg)
![Security](https://github.com/bernard-ng/drc-news-corpus/actions/workflows/audit.yaml/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/bernard-ng/drc-news-corpus/version)](https://packagist.org/packages/bernard-ng/drc-news-corpus)
[![License](https://poser.pugx.org/bernard-ng/drc-news-corpus/license)](https://packagist.org/packages/bernard-ng/drc-news-corpus)

```tex
@misc {bernard_ngandu_2025,
	author       = { {Bernard Ngandu} },
	title        = { drc-news-corpus (Revision 2b3b24c) },
	year         = 2025,
	url          = { https://huggingface.co/datasets/bernard-ng/drc-news-corpus },
	doi          = { 10.57967/hf/4662 },
	publisher    = { Hugging Face }
}
```

**Dataset is available on Hugging Face Datasets: [drc-news-corpus](https://huggingface.co/datasets/bernard-ng/drc-news-corpus)**

---
## Introduction

The **"DRC News Corpus"** is a structured and scalable dataset of news articles sourced from major media outlets covering diverse aspects of the Democratic Republic of Congo (DRC). Designed for efficiency, this system enables the automated collection, processing, and organization of news stories spanning politics, economy, society, culture, environment, and international affairs.

## Scalability and Use Cases:

This dataset is built to support large-scale text analysis, making it a valuable resource for researchers, journalists, policymakers, and data scientists. It facilitates tasks such as sentiment analysis, trend detection, entity recognition, and language modeling, providing deep insights into the evolving socio-political and economic landscape of the DRC.

To ensure quality and reliability, the dataset prioritizes reputable news sources while maintaining an adaptable framework for continuous expansion. However, users are encouraged to critically assess the content, as journalistic standards and perspectives may vary.

## Sources

| Source         | Supported | Articles | Link                                 | Last Crawled |
|----------------|-----------|----------|--------------------------------------|--------------|
| radiookapi.net | Yes       | +100k    | https://www.radiookapi.net/actualite | 2025-02-28   |
| mediacongo.cd  | Yes       | +100k    | https://www.mediacongo.net/          | 2025-02-28   |
| beto.cd        | Yes       | +30k     | https://www.beto.cd/                 | 2025-02-28   |
| actualite.cd   | Yes       | +57k     | https://actualite.cd/                | 2025-02-28   |
| 7sur7.cd       | Yes       | NA       | https://7sur7.cd                     | NA           |


## Build the dataset
If you want to rebuild the dataset follow the steps bellow : 

### Installation
```bash
git clone https://github.com/bernard-ng/drc-news-corpus.git && cd drc-news-corpus
make build
make start
```

### Usage
See supported sources above. you can also add your own source by extending the `App/Aggregator/Infrastructure/Crawler/Source/Source` abstract class.
if you want to crawl `radiookapi.net` you can run the following command:

#### 1. **Crawling**
```bash
php bin/console app:crawl radiookapi.net

# You can specify a date range to crawl articles.
php bin/console app:crawl beto.cd --date="2022-01-01:2022-12-31"

# You can specify a page range to crawl articles.
php bin/console app:crawl mediacongo.net --page="0:6" 

# You can specify both date and page range.
php bin/console app:crawl actualite.cd --date="2022-01-01:2022-12-31" --page="0:6"

# some sources require a category to crawl articles.
php bin/console app:crawl 7sur7.cd --category=politique

# You can crawl multiple pages in parallel (WIP - not stable).
php bin/console app:crawl radiookapi.net --parallel=20
```

#### 2. **Updating**
```bash
# Update the database with the latest articles.
php bin/console app:update radiookapi.net
```

Notice that this can take a while depending on the number of articles you want to crawl and will store the articles in the database.
running this command in the background is recommended. by default no output is generated, you can add the `-v` option to see the progress.

```bash
nohup php bin/console app:crawl radiookapi.net -v > crawling.log
```

#### 3. **Statistics**
```bash
# Get the number of articles in the database.
php bin/console app:stats
```

## Export the dataset
You can export the dataset to a CSV file by running the following command:

```bash
php bin/console app:export radiookapi.net
```

a CSV file will be generated in the `data` directory.


## Acknowledgment:
The compilation and curation of the "DRC News Corpus" were conducted by Tshabu Ngandu Bernard with the primary objective of facilitating research and analysis related to the Democratic Republic of Congo. 
I do not own the content of the articles, and all rights belong to the respective publishers. The dataset is intended for non-commercial research purposes only.
