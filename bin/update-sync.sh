#!/usr/local/cpanel/bin/jailshell

SOURCES=("7sur7.cd" "actualite.cd" "beto.cd" "radiookapi.net" "mediacongo.net", "newscd.net")
BASE_CMD="/usr/local/bin/php /home/eshimate/devscast.org/bin/console app:update"
LOG_DIR="/home/eshimate/devscast.org/var"

mkdir -p "$LOG_DIR"
rm -f "${LOG_DIR}"/*.log

for SOURCE in "${SOURCES[@]}"; do
    if [[ "$SOURCE" == "7sur7.cd" ]]; then
        CATEGORIES=("politique" "economie" "culture" "sport" "societe")

        for CATEGORY in "${CATEGORIES[@]}"; do
            LOG_FILE="${LOG_DIR}/${SOURCE}.${CATEGORY}.log"
            $BASE_CMD "$SOURCE" --direction=forward -vvv --category="$CATEGORY" 2>&1 | tee "$LOG_FILE"
        done
    else
        LOG_FILE="${LOG_DIR}/${SOURCE}.log"
        $BASE_CMD "$SOURCE" --direction=forward -vvv 2>&1 | tee "$LOG_FILE"
    fi
done

echo "All crawlers finished."
