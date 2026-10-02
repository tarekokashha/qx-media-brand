#!/usr/bin/env bash
# Installs WordPress into the local Docker stack, activates the QX theme and creates the pages
# the theme takes over, so http://localhost:8088 shows a complete demo.
#
#   docker compose up -d && ./scripts/wp-setup.sh
#
# Local use only. The stack binds to 127.0.0.1 and uses throwaway credentials
# (admin / admin). Re-running is safe: every step checks before it acts.
set -euo pipefail
cd "$(dirname "$0")/.."

URL="http://localhost:8088"
wp() { docker compose run --rm -T wpcli "$@"; }

echo "Starting the stack..."
docker compose up -d

echo "Waiting for WordPress and the database..."
until docker compose exec -T wordpress test -f /var/www/html/wp-config.php 2>/dev/null; do sleep 2; done
until wp db check >/dev/null 2>&1; do sleep 3; done

if ! wp core is-installed >/dev/null 2>&1; then
  echo "Installing WordPress..."
  wp core install --url="$URL" --title="Demo Agency" \
    --admin_user=admin --admin_password=admin --admin_email=admin@example.com --skip-email
fi

wp option update blogdescription "A short line about your agency"
wp rewrite structure '/%postname%/' --hard >/dev/null
wp theme activate qx-theme

# The three interior pages are matched by slug (see qx_page_map() in functions.php), so
# creating them is all it takes for the designed layouts to appear.
create_page() {
  local title="$1" slug="$2"
  if [ -z "$(wp post list --post_type=page --name="$slug" --format=ids)" ]; then
    wp post create --post_type=page --post_status=publish --post_title="$title" --post_name="$slug" --porcelain >/dev/null
    echo "  created page: $slug"
  fi
}
create_page "About us"    "about-us"
create_page "Services"    "services"
create_page "Contact us"  "contact-us"
create_page "من نحن"      "من-نحن"
create_page "خدماتنا"     "خدماتنا"
create_page "تواصل معنا"  "تواصل-معنا"

echo
echo "Ready: $URL   (admin login: $URL/wp-admin, admin / admin, local only)"
