#!/usr/local/cpanel/bin/jailshell

SOURCES=("7sur7.cd" "actualite.cd" "beto.cd" "radiookapi.net" "mediacongo.net")

# Define the base command
BASE_CMD="/usr/local/bin/php /home/eshimate/repositories/drc-news-corpus/bin/console app:update"

# Define the log directory
LOG_DIR="/home/eshimate/repositories/drc-news-corpus/var"

# Ensure the log directory exists
mkdir -p "$LOG_DIR"

# Clean old log files first
echo "Cleaning old log files in $LOG_DIR..."
rm -f "${LOG_DIR}"/*.log

# Define categories for 7sur7.cd
CATEGORIES=("politique" "economie" "culture" "sport" "societe")

for SOURCE in "${SOURCES[@]}"; do
    if [[ "$SOURCE" == "7sur7.cd" ]]; then
        # Handle 7sur7.cd separately with categories
        for CATEGORY in "${CATEGORIES[@]}"; do
            LOG_FILE="${LOG_DIR}/${SOURCE}.${CATEGORY}.log"
            nohup $BASE_CMD "$SOURCE" --direction=backward -vvv --category="$CATEGORY" > "$LOG_FILE" 2>&1 &
            echo "Started crawling $SOURCE category $CATEGORY... Logs: $LOG_FILE"
        done
    else
        # Handle other sources normally
        LOG_FILE="${LOG_DIR}/${SOURCE}.log"
        nohup $BASE_CMD "$SOURCE" --direction=backward -vvv > "$LOG_FILE" 2>&1 &
        echo "Started crawling $SOURCE... Logs: $LOG_FILE"
    fi
done

echo "All crawlers started in the background."
