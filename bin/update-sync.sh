#!/usr/local/cpanel/bin/jailshell

BASE_CMD="/usr/local/bin/php /home/eshimate/repositories/drc-news-corpus/bin/console app:update"
LOG_DIR="/home/eshimate/repositories/drc-news-corpus/var"

mkdir -p "$LOG_DIR"
rm -f "${LOG_DIR}"/*.log

SOURCES=("7sur7.cd" "actualite.cd" "beto.cd" "radiookapi.net" "mediacongo.net", "newscd.net")
for SOURCE in "${SOURCES[@]}"; do
    if [[ "$SOURCE" == "7sur7.cd" ]]; then
        CATEGORIES=("politique" "economie" "culture" "sport" "societe")

        for CATEGORY in "${CATEGORIES[@]}"; do
            LOG_FILE="${LOG_DIR}/${SOURCE}.${CATEGORY}.log"
            echo "Starting crawling $SOURCE category $CATEGORY..."
            echo "Command: $BASE_CMD \"$SOURCE\" --direction=forward -vvv --category=\"$CATEGORY\""
            $BASE_CMD "$SOURCE" --direction=forward -vvv --category="$CATEGORY" 2>&1 | tee "$LOG_FILE"
        done
    else
        LOG_FILE="${LOG_DIR}/${SOURCE}.log"
        echo "Starting crawling $SOURCE..."
        echo "Command: $BASE_CMD \"$SOURCE\" --direction=forward -vvv"
        $BASE_CMD "$SOURCE" --direction=forward -vvv 2>&1 | tee "$LOG_FILE"
    fi
done

echo "All crawlers finished."
