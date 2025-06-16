#!/usr/bin/env bash

SOURCES=(
  "africanewsrdc.net"
  "angazainstitute.ac.cd"
  "b-onetv.cd"
  "bukavufm.com"
  "changement7.net"
  "congoactu.net"
  "congoindependant.com"
  "congoquotidien.com"
  "cumulard.cd"
  "environews-rdc.net"
  "freemediardc.info"
  "geopolismagazine.org"
  "habarirdc.net"
  "infordc.com"
  "kilalopress.net"
  "laprosperiteonline.net"
  "laprunellerdc.cd"
  "lesmedias.net"
  "lesvolcansnews.net"
  "netic-news.net"
  "objectif-infos.cd"
  "scooprdc.net"
  "journaldekinshasa.com"
  "lepotentiel.cd"
  "acturdc.com"
  "matininfos.net"
)
BASE_CMD="/usr/bin/php /var/www/html/news.devscast.tech/bin/console app:crawl"
LOG_DIR="/var/www/html/news.devscast.tech/var"

mkdir -p "$LOG_DIR"
rm -f "${LOG_DIR}"/*.log

for SOURCE in "${SOURCES[@]}"; do
    LOG_FILE="${LOG_DIR}/crawling-${SOURCE}.log"
    nohup $BASE_CMD "$SOURCE" -vvv > "$LOG_FILE" 2>&1 &
done

echo "All crawlers started in the background."
