# DRC News Corpus

![Lint](https://github.com/bernard-ng/drc-news-corpus/actions/workflows/lint.yaml/badge.svg)
![Test](https://github.com/bernard-ng/drc-news-corpus/actions/workflows/test.yaml/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/bernard-ng/drc-news-corpus/version)](https://packagist.org/packages/bernard-ng/drc-news-corpus)
[![License](https://poser.pugx.org/bernard-ng/drc-news-corpus/license)](https://packagist.org/packages/bernard-ng/drc-news-corpus)

The "DRC News Corpus" is a curated collection of news articles sourced from major media outlets covering a wide spectrum of topics related to the Democratic Republic of Congo (DRC). 
This dataset encompasses a diverse range of news stories, including but not limited to politics, economy, social issues, culture, environment, and international relations, providing comprehensive coverage of events and developments within the country.


The dataset comprises articles gathered from prominent news sources operating within the DRC, offering insights into the nation's dynamic socio-political landscape, economic trends, humanitarian affairs, and more. 
The articles are primarily in French, representing a variety of perspectives and reporting styles from respected journalistic platforms. Spanning a time range from 2004 to november 2023, the dataset encapsulates news reports, analyses, opinion pieces, and feature stories published within this period, offering a snapshot of the media discourse surrounding the DRC during this timeframe.

## Use Cases:

Researchers, journalists, policymakers, and data enthusiasts interested in understanding the socio-political climate, economic dynamics, and other facets of the DRC will find this dataset valuable. It serves as a resource for sentiment analysis, trend identification, language modeling, and other natural language processing (NLP) tasks.

Efforts have been made to ensure the dataset's integrity and quality by including articles from reputable news outlets. However, users are encouraged to exercise discretion and validate the information independently as journalistic standards and perspectives may vary among sources.

# Download the dataset
[DRC News Corpus on Kaggle](https://www.kaggle.com/datasets/bernardngandu/drc-news-corpus)

# Rebuild the dataset
if you want to rebuild the dataset follow the steps bellow : 

```bash
git clone https://github.com/bernard-ng/drc-news-corpus.git && cd drc-news-corpus
composer install
```

## Usage
See supported sources below. you can also add your own source by implementing the `SourceInterface` interface. 
for instance, if you want to crawl `radiookapi.net` you can run the following command:

```bash
php bin/console app:crawl radiookapi.net --start=0 --end=9881 --filename=radiookapi.net
```
notice that this can take a while depending on the number of articles you want to crawl and will generate a csv file in the `data` directory.
running this command in the background is recommended. by default no output is generated, you can add the `-v` option to see the progress.

**running in the background**
```bash
nohup php bin/console app:crawl radiookapi.net --start=0 --end=9881 --filename=radiookapi.net -v > radiookapi.net.log
```

some sources require a `--category` option to specify the category to crawl. for instance, to crawl `7sur7.cd` you can run the following command:

```bash
php bin/console app:crawl 7sur7.cd --start=0 --end=100 --filename=7sur7.cd --category=politique -v > 7sur7.cd.log
```

### Available Sources
- [x] [radiookapi.net](https://www.radiookapi.net/actualite)
- [x] [actualite.cd](https://actualite.cd/)
- [x] [7sur7.cd](https://7sur7.cd/index.php/category/politique)
- [ ] [juricaf.org](https://juricaf.org/recherche/+/facet_pays%3ACongo_d%C3%A9mocratique)
- [ ] [congoprofond.net](https://congoprofond.net/category/actualite)
- [x] [politico.cd](https://www.politico.cd/rubrique/encontinu/)

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## contributors

<a href="https://github.com/bernard-ng/drc-news-corpus/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=bernard/drc-news-corpus"/>
</a>

## Acknowledgment:

The compilation and curation of the "DRC News Corpus" were conducted by Tshabu Ngandu Bernard with the primary objective of facilitating research and analysis related to the Democratic Republic of Congo. 
I don't forget to cite this repository if you consider to use the data or this software. 
