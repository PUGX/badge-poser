#!/bin/bash
for FILE in $@; do
    DATE=$(grep -i "date:" "$FILE" | cut -d: -f2-)
    TIMESTAMP=$(date -d "$DATE" +"%s")
    # TIMESTAMP=$(date -jf " %a, %d %b %Y %T %Z" "$DATE" "+%s")
    TTL=$(grep -i "cache-control:" "$FILE" | cut -d: -f2 | cut -d= -f2 | cut -d, -f1)
    EXPIRE=$((TTL+TIMESTAMP))
    NOW=$(date +%s)
    if [ "$NOW" -gt "$EXPIRE" ]; then
        rm -f "$FILE"
    fi
done
