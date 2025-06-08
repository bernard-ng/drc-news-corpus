#!/usr/bin/env bash

SOURCES=("7sur7.cd" "actualite.cd" "radiookapi.net" "mediacongo.net" "newscd.net")
BASE_CMD="/usr/bin/php /var/www/html/news.devscast.tech/bin/console app:open-graph"
LOG_DIR="/var/www/html/news.devscast.tech/var"

mkdir -p "$LOG_DIR"
rm -f "${LOG_DIR}"/*.log

for SOURCE in "${SOURCES[@]}"; do
    LOG_FILE="${LOG_DIR}/${SOURCE}.log"
    nohup $BASE_CMD "$SOURCE" -vvv --no-interaction > "$LOG_FILE" 2>&1 &
done

echo "All open graph crawlers started in the background."
