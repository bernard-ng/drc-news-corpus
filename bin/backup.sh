#!/usr/bin/env bash

# Get the script directory and define data directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DATA_DIR="${SCRIPT_DIR}/../data"
DUMP_FILE="${DATA_DIR}/data.sql"
ARCHIVE_FILE="${DUMP_FILE}.gz"

# Ensure data directory exists
mkdir -p "$DATA_DIR"

# Load environment variables from .env.local
set -a
if [ -f "${SCRIPT_DIR}/../.env.local" ]; then
  export "$(grep -v '^#' "${SCRIPT_DIR}/../.env.local" | grep '=' | xargs)"
fi
set +a

# Parse DATABASE_URL into components
if [[ -z "$DATABASE_URL" ]]; then
  echo "DATABASE_URL is not set in .env.local"
  exit 1
fi

regex="^mysql:\/\/([^:]+):([^@]+)@([^:]+):([0-9]+)\/([^?]+)"
if [[ "$DATABASE_URL" =~ $regex ]]; then
  DB_USER="${BASH_REMATCH[1]}"
  DB_PASSWORD="${BASH_REMATCH[2]}"
  DB_HOST="${BASH_REMATCH[3]}"
  DB_PORT="${BASH_REMATCH[4]}"
  DB_NAME="${BASH_REMATCH[5]}"
else
  echo "Invalid DATABASE_URL format"
  exit 1
fi

# Step 1: Dump the database
mysqldump --host="${DB_HOST}" --port="${DB_PORT}" \
  --user="${DB_USER}" --password="${DB_PASSWORD}" \
  --max_allowed-packet=1G --net-buffer-length=32704 --skip-extended-insert \
  "${DB_NAME}" > "$DUMP_FILE"

gzip -f "$DUMP_FILE"

# Step 2: Send the file to Telegram
curl -F "chat_id=${DEVY_CHANNEL}" \
     -F "message_thread_id=${DEVY_TOPIC}" \
     -F "document=@${ARCHIVE_FILE}" \
     "https://api.telegram.org/bot${DEVY_TOKEN}/sendDocument"

# Step 3: Clean up
rm -f "$ARCHIVE_FILE" "$DUMP_FILE"
