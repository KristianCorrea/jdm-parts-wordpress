#!/usr/bin/env bash
#
# JDM Miami — seed runner (local Docker)
#
# Runs the entire seed in a single WP-CLI container boot.
# No repeated docker compose run overhead.
#
# Usage:
#   chmod +x seed_data.sh
#   ./seed_data.sh
#
# On a remote/managed host with WP-CLI via SSH, run the PHP file directly:
#   wp eval-file wp-content/themes/jdm-miami-theme/seed_data.php
#
set -euo pipefail

SEED_FILE="/var/www/html/wp-content/themes/jdm-miami-theme/seed_data.php"

echo "Running JDM Miami seed..."

docker compose run --rm wpcli wp --allow-root eval-file "$SEED_FILE"

echo "Done."
