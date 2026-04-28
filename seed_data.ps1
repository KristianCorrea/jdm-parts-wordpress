# JDM Miami — seed runner (local Docker, Windows)
#
# Runs the entire seed in a single WP-CLI container boot.
# No repeated docker compose run overhead.
#
# Usage (PowerShell):
#   .\seed_data.ps1
#
# If execution policy blocks the script, run once:
#   Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned
#
# On a remote/managed host with WP-CLI via SSH, run the PHP file directly:
#   wp eval-file wp-content/themes/jdm-miami-theme/seed_data.php

$ErrorActionPreference = "Stop"

$SEED_FILE = "/var/www/html/wp-content/themes/jdm-miami-theme/seed_data.php"

Write-Host "Running JDM Miami seed..."

docker compose run --rm wpcli wp --allow-root eval-file $SEED_FILE

Write-Host "Done."
