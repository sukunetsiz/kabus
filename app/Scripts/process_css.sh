#!/bin/bash
set -euo pipefail

##############################################
# Safety Check – Do Not Run as Root!         #
##############################################
if [ "$EUID" -eq 0 ]; then
    echo "Please do not run this script as root. Run it as your normal user." >&2
    exit 1
fi

##############################################
# Set Up Directories and Environment         #
##############################################
BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
CSS_DIR="$BASE_DIR/../../public/css"

# Navigate to the CSS directory or exit on failure.
cd "$CSS_DIR" || { echo "Error: Could not navigate to public/css directory"; exit 1; }

# Initialize temporary file variable for cleanup.
TEMP_FILE=""

cleanup() {
    [ -n "$TEMP_FILE" ] && [ -f "$TEMP_FILE" ] && rm -f "$TEMP_FILE"
}
trap cleanup EXIT

##############################################
# Function: Compress CSS                     #
##############################################
compress_css() {
    echo "Compressing CSS..."

    if [ ! -f "styles.css" ]; then
        echo "Error: styles.css not found in public/css directory"
        exit 1
    fi

    # Create a backup of the original file.
    cp styles.css styles.css.bak

    # Compress CSS to a single line.
    sed -e 's!/\*.*\*/!!g' \
        -e 's/^[ \t]*//g' \
        -e 's/[ \t]*$//g' \
        -e 's/\([:{;,]\) /\1/g' \
        -e 's/ {/{/g' \
        -e 's/\/\*.*\*\///g' \
        -e '/^$/d' styles.css | tr -d '\n\r' > styles.css.min

    # Replace original file with compressed version.
    mv styles.css.min styles.css

    ORIGINAL_SIZE=$(wc -c < styles.css.bak)
    COMPRESSED_SIZE=$(wc -c < styles.css)
    SAVED=$((ORIGINAL_SIZE - COMPRESSED_SIZE))
    PERCENT=$((SAVED * 100 / ORIGINAL_SIZE))

    echo "CSS Compression Complete!"
    echo "Original size: $ORIGINAL_SIZE bytes"
    echo "Compressed size: $COMPRESSED_SIZE bytes"
    echo "Saved: $SAVED bytes ($PERCENT%)"
    echo "Original file replaced with compressed version"
    echo "Backup of original saved as: styles.css.bak"
}

##############################################
# Function: Uncompress CSS                   #
##############################################
uncompress_css() {
    echo "Uncompressing CSS..."

    if [ ! -f "styles.css" ]; then
        echo "Error: styles.css not found in public/css directory"
        exit 1
    fi

    # Create a backup of the current file.
    cp styles.css styles.css.bak

    # Create a temporary file.
    TEMP_FILE=$(mktemp)

    # First pass: Insert newlines after key characters.
    sed 's/}/}\n/g; s/{/{\n/g; s/;/;\n/g' styles.css > "$TEMP_FILE"

    # Second pass: Fine-tune formatting.
    sed 's/\([^ ]\){/\1 {/g; s/}\([^,]*\),\([^ ]\)/}\1, \2/g; s/"\([^"]*\)",\s*/"\1",/g; s/\([^}]\)}/\1\n}/g; s/}/}\n/g' "$TEMP_FILE" \
        | sed -z 's/}\n\([A-Za-z\.#:][^{]*{.*\)/}\n\n\1/g' \
        | sed -z 's/}\n\n}/}\n}/g' \
        | sed 's/@keyframes /@keyframes /g' > styles.css.unmin

    # Remove trailing newline if it exists.
    if [ -s "styles.css.unmin" ] && [ "$(tail -c 1 styles.css.unmin | wc -l)" -eq 1 ]; then
        truncate -s -1 styles.css.unmin
    fi

    # Replace original file with uncompressed version.
    mv styles.css.unmin styles.css

    echo "CSS Uncompression Complete!"
    echo "Original file replaced with uncompressed version"
    echo "Backup of previous version saved as: styles.css.bak"
}

##############################################
# Main Menu and User Interaction             #
##############################################
echo "CSS Processor"
echo "-------------"
echo "1. Compress CSS"
echo "2. Uncompress CSS"
echo "-------------"
read -rp "Enter your choice (1 or 2): " choice

case $choice in
    1)
        compress_css
        ;;
    2)
        uncompress_css
        ;;
    *)
        echo "Invalid choice. Please enter 1 for compress or 2 for uncompress."
        exit 1
        ;;
esac

exit 0
