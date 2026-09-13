#!/usr/bin/env bash
set -euo pipefail

php artisan migrate --force
php artisan db:seed --class='Database\\Seeders\\DemoDataSeeder' --force

echo "Demo data seeded successfully."
