#!/bin/bash
set -euo pipefail

##############################################
# Step 0: Safety Check – Do Not Run as Root! #
##############################################
if [ "$EUID" -eq 0 ]; then
    echo "Please do not run this script as root. Run it as your normal user." >&2
    exit 1
fi

# Save the base directory so we know where to clean up later.
BASE_DIR=$(pwd)
# BUILD_DIR will be set after extraction.
BUILD_DIR=""

# Define a cleanup function to remove the build directory on exit.
cleanup() {
    echo "Cleaning up build directory..."
    cd "$BASE_DIR"
    if [ -n "${BUILD_DIR:-}" ] && [ -d "$BUILD_DIR" ]; then
        rm -rf "$BUILD_DIR"
        echo "Removed build directory: $BUILD_DIR"
    fi
}
trap cleanup EXIT

##############################################
# Part 1: Build mkp224o from Latest Release  #
##############################################

echo "Fetching the latest release information from GitHub..."
LATEST_RELEASE=$(curl -s https://api.github.com/repos/cathugger/mkp224o/releases/latest | grep '"tag_name":' | sed -E 's/.*"([^"]+)".*/\1/')
if [ -z "$LATEST_RELEASE" ]; then
    echo "Failed to fetch the latest release version."
    exit 1
fi
echo "Latest release found: $LATEST_RELEASE"

# Prepare tarball name, download URL, and extraction folder name.
TARBALL="mkp224o-${LATEST_RELEASE}.tar.gz"
DOWNLOAD_URL="https://github.com/cathugger/mkp224o/archive/refs/tags/${LATEST_RELEASE}.tar.gz"
# GitHub archive names typically drop the leading "v"
EXTRACTED_DIR="mkp224o-${LATEST_RELEASE#v}"

# Set a default configure option; adjust as needed (for example, for a fast x86_64 build)
CONFIGURE_OPTIONS="--enable-amd64-51-30k"

echo "Updating package lists..."
sudo apt update
echo "Upgrading installed packages..."
sudo apt upgrade -y

echo "Installing build dependencies..."
sudo apt install -y gcc libc6-dev libsodium-dev make autoconf

# Remove any preexisting build directory (even if owned by root)
if [ -d "${EXTRACTED_DIR}" ]; then
    echo "Directory ${EXTRACTED_DIR} already exists."
    if [ "$(stat -c '%U' "${EXTRACTED_DIR}")" != "$(whoami)" ]; then
        echo "Directory ${EXTRACTED_DIR} is not owned by $(whoami). Removing it with sudo..."
        sudo rm -rf "${EXTRACTED_DIR}"
    else
        echo "Removing existing directory ${EXTRACTED_DIR}..."
        rm -rf "${EXTRACTED_DIR}"
    fi
fi

echo "Downloading mkp224o release ${LATEST_RELEASE}..."
if command -v wget >/dev/null 2>&1; then
    wget -O "${TARBALL}" "${DOWNLOAD_URL}"
elif command -v curl >/dev/null 2>&1; then
    curl -L -o "${TARBALL}" "${DOWNLOAD_URL}"
else
    echo "Error: Neither wget nor curl is installed."
    exit 1
fi

echo "Extracting ${TARBALL}..."
tar -xzvf "${TARBALL}"
echo "Deleting tarball ${TARBALL}..."
rm -f "${TARBALL}"

# Set the full path to the build directory for later cleanup.
BUILD_DIR="${BASE_DIR}/${EXTRACTED_DIR}"

cd "${EXTRACTED_DIR}"

if [ -x "./autogen.sh" ]; then
    echo "Running autogen.sh..."
    ./autogen.sh
fi

echo "Running configure with options: ${CONFIGURE_OPTIONS}..."
./configure ${CONFIGURE_OPTIONS}

echo "Building mkp224o..."
make

# Fix permissions in the build directory
echo "Fixing permissions in the build directory..."
sudo chown -R "$(whoami):$(whoami)" .

echo "mkp224o has been built successfully in $(pwd)"

##############################################
# Part 2: Interactive Vanity Key Generation  #
##############################################

# Loop until a valid vanity filter is entered (at least 3 alphanumeric characters)
while true; do
    read -rp "Enter your desired vanity filter (at least 3 alphanumeric characters): " filter
    if [[ ${#filter} -ge 3 ]]; then
        break
    fi
    echo "Invalid filter. It must be at least 3 characters. Please try again."
done

# Determine how many keys to generate based on filter length
case "${#filter}" in
  3) nkeys=32 ;;
  4) nkeys=16 ;;
  5) nkeys=8 ;;
  6) nkeys=4 ;;
  7) nkeys=2 ;;
  *) nkeys=1 ;;
esac
echo "For a filter of length ${#filter}, the target is to generate ${nkeys} vanity addresses."

# Create a work directory for keys (e.g. <filter>-keys)
work_dir="${filter}-keys"
if [ -d "${work_dir}" ]; then
    echo "Work directory '${work_dir}' already exists. Removing it..."
    rm -rf "${work_dir}"
fi

##############################################
# Start mkp224o in background to generate keys
##############################################
echo "Starting mkp224o to generate vanity addresses..."
# The '-d' option sets the work directory and we supply the filter as the search parameter.
./mkp224o -d "${work_dir}" "${filter}" &
mkp_pid=$!

# Monitor the work directory until we have reached (or exceeded) the desired number of addresses.
echo "Generating vanity addresses. Please wait..."
while true; do
    count=$(find "${work_dir}" -mindepth 1 -maxdepth 1 -type d | wc -l)
    echo "Currently generated: ${count} of ${nkeys}..."
    if [ "$count" -ge "$nkeys" ]; then
        echo "Target reached: ${count} addresses generated."
        kill "$mkp_pid" 2>/dev/null || true
        break
    fi
    sleep 1
done

# Give the process a moment to stop completely
sleep 1

##############################################
# Limit the generated keys to exactly the target number
##############################################
all_keys=( $(find "${work_dir}" -mindepth 1 -maxdepth 1 -type d -printf "%T@ %p\n" | sort -n | awk '{print $2}') )
if [ "${#all_keys[@]}" -gt "$nkeys" ]; then
    echo "Extra keys detected. Limiting keys to ${nkeys}..."
    # Keep the first $nkeys (oldest ones) and remove the rest.
    keep=("${all_keys[@]:0:$nkeys}")
    remove=("${all_keys[@]:$nkeys}")
    for keydir in "${remove[@]}"; do
        rm -rf "$keydir"
    done
fi

##############################################
# Let the user choose their preferred address
##############################################
echo ""
echo "The following vanity addresses have been generated in '${work_dir}':"
i=1
# Build an array of directories for selection
mapfile -t addr_arr < <(find "${work_dir}" -mindepth 1 -maxdepth 1 -type d | sort)
for addr in "${addr_arr[@]}"; do
    echo "  $i) $(basename "$addr")"
    ((i++))
done

# Loop until a valid selection is made.
while true; do
    read -rp "Enter the number corresponding to your preferred address: " choice
    if [[ "$choice" =~ ^[0-9]+$ ]] && [ "$choice" -ge 1 ] && [ "$choice" -le "${#addr_arr[@]}" ]; then
        break
    fi
    echo "Invalid selection. Please enter a valid number between 1 and ${#addr_arr[@]}."
done

selected="${addr_arr[$((choice-1))]}"
echo "You selected: $(basename "$selected")"

##############################################
# Delete all non-selected addresses to tidy up
##############################################
echo "Removing all other generated addresses..."
for addr in "${addr_arr[@]}"; do
    if [ "$addr" != "$selected" ]; then
        rm -rf "$addr"
    fi
done

echo "The final selected vanity address directory is: ${selected}"
echo "Vanity address generation complete."

##############################################
# Part 3: Activate the Selected Vanity Address
##############################################

echo "Stopping Tor service..."
sudo systemctl stop tor

hidden_service_dir="/var/lib/tor/hidden_service"
echo "Ensuring hidden service directory exists at ${hidden_service_dir}..."
if [ ! -d "$hidden_service_dir" ]; then
    sudo mkdir -p "$hidden_service_dir"
fi

echo "Cleaning up old hidden service files in ${hidden_service_dir}..."
for f in hostname hs_ed25519_public_key hs_ed25519_secret_key; do
    if [ -f "$hidden_service_dir/$f" ]; then
        sudo rm -f "$hidden_service_dir/$f"
    fi
done

echo "Moving selected vanity address files to ${hidden_service_dir}..."
# Move the key files from the selected folder to the hidden service directory.
sudo mv "${selected}/"* "$hidden_service_dir/"

echo "Setting correct ownership and permissions on ${hidden_service_dir}..."
# Adjust these if your Tor user is different (commonly "debian-tor" on Debian/Ubuntu).
sudo chown -R debian-tor:debian-tor "$hidden_service_dir"
sudo chmod -R 700 "$hidden_service_dir"

echo "Starting Tor service..."
sudo systemctl start tor

echo "Restarting Nginx service..."
sudo systemctl restart nginx

echo "Waiting for Tor to initialize the hidden service..."
timeout=60
elapsed=0
while [ $elapsed -lt $timeout ]; do
    if sudo test -f "$hidden_service_dir/hostname"; then
        break
    fi
    sleep 2
    elapsed=$((elapsed+2))
done

if sudo test -f "$hidden_service_dir/hostname"; then
    onion=$(sudo cat "$hidden_service_dir/hostname")
    echo "Your onion address is: $onion"
else
    echo "Error: Hidden service hostname not found in ${hidden_service_dir} after waiting."
fi

echo "Setup complete."
