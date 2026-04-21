#!/usr/bin/env bash
set -euo pipefail

WP="docker compose run --rm wpcli wp --allow-root"

echo "Checking WordPress..."
$WP core is-installed >/dev/null

echo "Checking WooCommerce..."
$WP plugin is-active woocommerce >/dev/null

echo "Seeding product categories..."

create_term_if_missing() {
  local taxonomy="$1"
  local name="$2"
  local slug="$3"
  local description="$4"

  if ! $WP term list "$taxonomy" --field=slug | grep -qx "$slug"; then
    $WP term create "$taxonomy" "$name" --slug="$slug" --description="$description" >/dev/null
    echo "  Created term: $name"
  else
    echo "  Term exists: $name"
  fi
}

# -----------------------------
# Product categories
# -----------------------------
create_term_if_missing "product_cat" "Engine & Engine Parts" "engine-parts" "Used OEM engines and engine components from JDM imports."
create_term_if_missing "product_cat" "Transmissions & Drivetrain" "transmissions-drivetrain" "Transmissions, differentials, axles, and drivetrain components."
create_term_if_missing "product_cat" "Suspension & Steering" "suspension-steering" "Suspension systems, control arms, racks, and steering components."
create_term_if_missing "product_cat" "Brakes" "brakes" "Brake calipers, rotors, master cylinders, and braking components."
create_term_if_missing "product_cat" "Cooling System" "cooling-system" "Radiators, fans, hoses, and cooling-related parts."
create_term_if_missing "product_cat" "Electrical & Sensors" "electrical-sensors" "Wiring, sensors, alternators, starters, and electrical components."
create_term_if_missing "product_cat" "ECU / Modules" "ecu-modules" "Engine control units and electronic modules."
create_term_if_missing "product_cat" "Interior Parts" "interior-parts" "Seats, dashboards, trims, and interior components."
create_term_if_missing "product_cat" "Exterior / Body Parts" "exterior-body" "Body panels, mirrors, bumpers, and exterior components."
create_term_if_missing "product_cat" "Wheels & Tires" "wheels-tires" "OEM wheels, tires, and related hardware."
create_term_if_missing "product_cat" "Lighting" "lighting" "Headlights, tail lights, and other lighting components."
create_term_if_missing "product_cat" "Accessories / Misc" "accessories-misc" "Miscellaneous parts and accessories not categorized elsewhere."

echo "Seeding WooCommerce global attributes..."

$WP eval '
$attributes = [
  ["name" => "Year",      "slug" => "vehicle_year"],
  ["name" => "Make",      "slug" => "make"],
  ["name" => "Model",     "slug" => "model"],
  ["name" => "Engine",    "slug" => "engine"],
  ["name" => "Condition", "slug" => "condition"],
  ["name" => "Side",      "slug" => "side"],
];

foreach ($attributes as $attr) {
  $existing_id = wc_attribute_taxonomy_id_by_name($attr["slug"]);
  if (!$existing_id) {
    $result = wc_create_attribute([
      "name"         => $attr["name"],
      "slug"         => $attr["slug"],
      "type"         => "select",
      "order_by"     => "menu_order",
      "has_archives" => false,
    ]);

    if (is_wp_error($result)) {
      echo "Error creating attribute {$attr["name"]}: " . $result->get_error_message() . PHP_EOL;
    } else {
      echo "Created attribute: {$attr["name"]}" . PHP_EOL;
    }
  } else {
    echo "Attribute exists: {$attr["name"]}" . PHP_EOL;
  }
}

delete_transient("wc_attribute_taxonomies");
if (function_exists("wc_recount_all_terms")) {
  wc_recount_all_terms();
}
flush_rewrite_rules();
'

echo "Seeding attribute terms..."

# Year terms
create_term_if_missing "pa_vehicle_year" "2005-2007" "2005-2007" ""
create_term_if_missing "pa_vehicle_year" "2008-2012" "2008-2012" ""
create_term_if_missing "pa_vehicle_year" "2013-2017" "2013-2017" ""
create_term_if_missing "pa_vehicle_year" "2018-2022" "2018-2022" ""

# Make terms
create_term_if_missing "pa_make" "Honda" "honda" ""
create_term_if_missing "pa_make" "Toyota" "toyota" ""
create_term_if_missing "pa_make" "Nissan" "nissan" ""
create_term_if_missing "pa_make" "Subaru" "subaru" ""
create_term_if_missing "pa_make" "Mazda" "mazda" ""
create_term_if_missing "pa_make" "Mitsubishi" "mitsubishi" ""
create_term_if_missing "pa_make" "Lexus" "lexus" ""
create_term_if_missing "pa_make" "Acura" "acura" ""

# Model terms
create_term_if_missing "pa_model" "Accord" "accord" ""
create_term_if_missing "pa_model" "Civic" "civic" ""
create_term_if_missing "pa_model" "Camry" "camry" ""
create_term_if_missing "pa_model" "Corolla" "corolla" ""
create_term_if_missing "pa_model" "350Z" "350z" ""
create_term_if_missing "pa_model" "WRX" "wrx" ""
create_term_if_missing "pa_model" "RX-8" "rx-8" ""
create_term_if_missing "pa_model" "Evo X" "evo-x" ""
create_term_if_missing "pa_model" "IS250" "is250" ""

# Engine terms
create_term_if_missing "pa_engine" "K24" "k24" ""
create_term_if_missing "pa_engine" "K20" "k20" ""
create_term_if_missing "pa_engine" "2GR-FE" "2gr-fe" ""
create_term_if_missing "pa_engine" "SR20DET" "sr20det" ""
create_term_if_missing "pa_engine" "RB26DETT" "rb26dett" ""
create_term_if_missing "pa_engine" "EJ20" "ej20" ""
create_term_if_missing "pa_engine" "EJ25" "ej25" ""
create_term_if_missing "pa_engine" "4B11T" "4b11t" ""

# Condition terms
create_term_if_missing "pa_condition" "Used - Good" "used-good" ""
create_term_if_missing "pa_condition" "Used - Fair" "used-fair" ""
create_term_if_missing "pa_condition" "Refurbished" "refurbished" ""
create_term_if_missing "pa_condition" "For Parts" "for-parts" ""

# Side terms
create_term_if_missing "pa_side" "Left" "left" ""
create_term_if_missing "pa_side" "Right" "right" ""
create_term_if_missing "pa_side" "Front" "front" ""
create_term_if_missing "pa_side" "Rear" "rear" ""
create_term_if_missing "pa_side" "Driver Side" "driver-side" ""
create_term_if_missing "pa_side" "Passenger Side" "passenger-side" ""

echo "Seeding sample products..."

$WP eval '
function seed_simple_product($data) {
  $existing = get_page_by_title($data["title"], OBJECT, "product");
  if ($existing) {
    echo "Product exists: {$data["title"]}" . PHP_EOL;
    return;
  }

  $product_id = wp_insert_post([
    "post_title"   => $data["title"],
    "post_content" => $data["description"],
    "post_status"  => "publish",
    "post_type"    => "product",
  ]);

  if (is_wp_error($product_id)) {
    echo "Error creating product {$data["title"]}: " . $product_id->get_error_message() . PHP_EOL;
    return;
  }

  wp_set_object_terms($product_id, $data["category"], "product_cat");

  foreach ($data["tax_terms"] as $taxonomy => $terms) {
    wp_set_object_terms($product_id, $terms, $taxonomy);
  }

  update_post_meta($product_id, "_sku", $data["sku"]);
  update_post_meta($product_id, "_regular_price", $data["price"]);
  update_post_meta($product_id, "_price", $data["price"]);
  update_post_meta($product_id, "_stock_status", "instock");
  update_post_meta($product_id, "_manage_stock", "yes");
  update_post_meta($product_id, "_stock", "1");
  update_post_meta($product_id, "_visibility", "visible");

  $product_attributes = [];
  foreach ($data["tax_terms"] as $taxonomy => $terms) {
    $product_attributes[$taxonomy] = [
      "name"         => $taxonomy,
      "value"        => "",
      "position"     => 0,
      "is_visible"   => 1,
      "is_variation" => 0,
      "is_taxonomy"  => 1,
    ];
  }
  update_post_meta($product_id, "_product_attributes", $product_attributes);

  echo "Created product: {$data["title"]}" . PHP_EOL;
}

$products = [
  [
    "title" => "2008-2012 Honda Accord Alternator OEM",
    "sku" => "JDM-HON-ACC-ALT-001",
    "price" => "149.99",
    "category" => ["Engine & Engine Parts"],
    "description" => "Pulled from a JDM Honda Accord donor vehicle. Tested and working. Approx. 65k miles with normal cosmetic wear.",
    "tax_terms" => [
      "pa_vehicle_year" => ["2008-2012"],
      "pa_make" => ["Honda"],
      "pa_model" => ["Accord"],
      "pa_engine" => ["K24"],
      "pa_condition" => ["Used - Good"],
    ],
  ],
  [
    "title" => "2015 Subaru WRX ECU OEM",
    "sku" => "JDM-SUB-WRX-ECU-001",
    "price" => "249.99",
    "category" => ["ECU / Modules"],
    "description" => "OEM ECU removed from imported Subaru WRX clip. Good used condition.",
    "tax_terms" => [
      "pa_vehicle_year" => ["2013-2017"],
      "pa_make" => ["Subaru"],
      "pa_model" => ["WRX"],
      "pa_engine" => ["EJ20"],
      "pa_condition" => ["Used - Good"],
    ],
  ],
  [
    "title" => "2006-2008 Nissan 350Z Driver Side Mirror",
    "sku" => "JDM-NIS-350Z-MIR-001",
    "price" => "119.99",
    "category" => ["Exterior / Body Parts"],
    "description" => "OEM driver side mirror from imported Nissan 350Z. Normal wear from use.",
    "tax_terms" => [
      "pa_vehicle_year" => ["2005-2007"],
      "pa_make" => ["Nissan"],
      "pa_model" => ["350Z"],
      "pa_condition" => ["Used - Good"],
      "pa_side" => ["Driver Side"],
    ],
  ],
];

foreach ($products as $product) {
  seed_simple_product($product);
}
'

echo "Done."