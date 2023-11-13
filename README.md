# DRC Data Aggregator

![Lint](https://github.com/bernard-ng/cod-data-aggregator/actions/workflows/lint.yaml/badge.svg)
![Test](https://github.com/bernard-ng/cod-data-aggregator/actions/workflows/test.yaml/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/bernard-ng/cod-data-aggregator/version)](https://packagist.org/packages/bernard-ng/cod-data-aggregator)
[![License](https://poser.pugx.org/bernard-ng/cod-data-aggregator/license)](https://packagist.org/packages/bernard-ng/cod-data-aggregator)

Congo Data Aggregator provides a simple PHP api that aggregates multiple sources of data from the Democratic Republic of Congo.

## Installation
```bash
git clone https://github.com/bernard-ng/cod-data-aggregator.git && cd cod-data-aggregator
composer install
```

## Usage
See supported sources below. you can also add your own source by implementing the `SourceInterface` interface. 
for instance, if you want to crawl `radiookapi.net` you can run the following command:

```bash
php bin/console app:crawl radiookapi.net --start=0 --end=9881 --filename=radiookapi
```
notice that this can take a while depending on the number of articles you want to crawl and will generate a csv file in the `data` directory.
running this command in the background is recommended.

### Available Sources
- [x] [radiookapi.net](https://www.radiookapi.net/actualite)
- [ ] [actualite.cd](https://actualite.cd/)
- [x] [7sur7.cd](https://7sur7.cd/index.php/category/politique)
- [ ] [juricaf.org](https://juricaf.org/recherche/+/facet_pays%3ACongo_d%C3%A9mocratique)
- [ ] [leganews.pro](https://leganews.pro/)
- [ ] [congoprofond.net](https://congoprofond.net/category/actualite)
- [ ] [politico.cd](https://www.politico.cd/rubrique/encontinu/)

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

### contributors

<a href="https://github.com/bernard-ng/cod-data-aggregator/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=bernard/cod-data-aggregator"/>
</a>
