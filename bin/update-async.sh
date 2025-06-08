#!/usr/bin/env bash

SOURCES=("7sur7.cd" "actualite.cd" "radiookapi.net" "mediacongo.net" "newscd.net")
BASE_CMD="/usr/local/bin/php /home/eshimate/devscast.org/bin/console app:update"
LOG_DIR="/home/eshimate/devscast.org/var"

mkdir -p "$LOG_DIR"
rm -f "${LOG_DIR}"/*.log

for SOURCE in "${SOURCES[@]}"; do
    if [[ "$SOURCE" == "7sur7.cd" ]]; then
        CATEGORIES=("politique" "economie" "culture" "sport" "societe")

        for CATEGORY in "${CATEGORIES[@]}"; do
            LOG_FILE="${LOG_DIR}/${SOURCE}.${CATEGORY}.log"
            nohup $BASE_CMD "$SOURCE" --direction=forward -vvv --category="$CATEGORY" > "$LOG_FILE" 2>&1 &
        done
    else
        LOG_FILE="${LOG_DIR}/${SOURCE}.log"
        nohup $BASE_CMD "$SOURCE" --direction=forward -vvv > "$LOG_FILE" 2>&1 &
    fi
done

echo "All crawlers started in the background."
