Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Invoke-Wp {
    param(
        [Parameter(Mandatory = $true, Position = 0)]
        [string[]]$Arguments
    )

    $output = & docker compose run --rm wpcli wp --allow-root @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "WP-CLI command failed (exit code $LASTEXITCODE): wp $($Arguments -join ' ')"
    }

    return $output
}

function Invoke-WpEval {
    param(
        [Parameter(Mandatory = $true)]
        [string]$PhpCode
    )

    # Avoid native argument quoting issues by sending base64-safe payload to wp eval.
    $encoded = [Convert]::ToBase64String([System.Text.Encoding]::UTF8.GetBytes($PhpCode))
    $wrapper = "eval(base64_decode('$encoded'));"
    Invoke-Wp @("eval", $wrapper)
}

function Test-TermExists {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Taxonomy,
        [Parameter(Mandatory = $true)]
        [string]$Slug
    )

    $slugs = Invoke-Wp @("term", "list", $Taxonomy, "--field=slug")
    if (-not $slugs) {
        return $false
    }

    foreach ($existingSlug in $slugs) {
        if ($existingSlug.Trim() -eq $Slug) {
            return $true
        }
    }

    return $false
}

function New-TermIfMissing {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Taxonomy,
        [Parameter(Mandatory = $true)]
        [string]$Name,
        [Parameter(Mandatory = $true)]
        [string]$Slug,
        [Parameter()]
        [string]$Description = ""
    )

    if (-not (Test-TermExists -Taxonomy $Taxonomy -Slug $Slug)) {
        Invoke-Wp @("term", "create", $Taxonomy, $Name, "--slug=$Slug", "--description=$Description") | Out-Null
        Write-Host "  Created term: $Name"
    } else {
        Write-Host "  Term exists: $Name"
    }
}

Write-Host "Checking WordPress..."
Invoke-Wp @("core", "is-installed") | Out-Null

Write-Host "Checking WooCommerce..."
Invoke-Wp @("plugin", "is-active", "woocommerce") | Out-Null

Write-Host "Seeding product categories..."

# Product categories
New-TermIfMissing -Taxonomy "product_cat" -Name "Engine & Engine Parts" -Slug "engine-parts" -Description "Used OEM engines and engine components from JDM imports."
New-TermIfMissing -Taxonomy "product_cat" -Name "Transmissions & Drivetrain" -Slug "transmissions-drivetrain" -Description "Transmissions, differentials, axles, and drivetrain components."
New-TermIfMissing -Taxonomy "product_cat" -Name "Suspension & Steering" -Slug "suspension-steering" -Description "Suspension systems, control arms, racks, and steering components."
New-TermIfMissing -Taxonomy "product_cat" -Name "Brakes" -Slug "brakes" -Description "Brake calipers, rotors, master cylinders, and braking components."
New-TermIfMissing -Taxonomy "product_cat" -Name "Cooling System" -Slug "cooling-system" -Description "Radiators, fans, hoses, and cooling-related parts."
New-TermIfMissing -Taxonomy "product_cat" -Name "Electrical & Sensors" -Slug "electrical-sensors" -Description "Wiring, sensors, alternators, starters, and electrical components."
New-TermIfMissing -Taxonomy "product_cat" -Name "ECU / Modules" -Slug "ecu-modules" -Description "Engine control units and electronic modules."
New-TermIfMissing -Taxonomy "product_cat" -Name "Interior Parts" -Slug "interior-parts" -Description "Seats, dashboards, trims, and interior components."
New-TermIfMissing -Taxonomy "product_cat" -Name "Exterior / Body Parts" -Slug "exterior-body" -Description "Body panels, mirrors, bumpers, and exterior components."
New-TermIfMissing -Taxonomy "product_cat" -Name "Wheels & Tires" -Slug "wheels-tires" -Description "OEM wheels, tires, and related hardware."
New-TermIfMissing -Taxonomy "product_cat" -Name "Lighting" -Slug "lighting" -Description "Headlights, tail lights, and other lighting components."
New-TermIfMissing -Taxonomy "product_cat" -Name "Accessories / Misc" -Slug "accessories-misc" -Description "Miscellaneous parts and accessories not categorized elsewhere."

Write-Host "Seeding WooCommerce global attributes..."

$attributesPhp = @'
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
'@

Invoke-WpEval -PhpCode $attributesPhp

Write-Host "Seeding attribute terms..."

# Year terms
New-TermIfMissing -Taxonomy "pa_vehicle_year" -Name "2005-2007" -Slug "2005-2007"
New-TermIfMissing -Taxonomy "pa_vehicle_year" -Name "2008-2012" -Slug "2008-2012"
New-TermIfMissing -Taxonomy "pa_vehicle_year" -Name "2013-2017" -Slug "2013-2017"
New-TermIfMissing -Taxonomy "pa_vehicle_year" -Name "2018-2022" -Slug "2018-2022"

# Make terms
New-TermIfMissing -Taxonomy "pa_make" -Name "Honda" -Slug "honda"
New-TermIfMissing -Taxonomy "pa_make" -Name "Toyota" -Slug "toyota"
New-TermIfMissing -Taxonomy "pa_make" -Name "Nissan" -Slug "nissan"
New-TermIfMissing -Taxonomy "pa_make" -Name "Subaru" -Slug "subaru"
New-TermIfMissing -Taxonomy "pa_make" -Name "Mazda" -Slug "mazda"
New-TermIfMissing -Taxonomy "pa_make" -Name "Mitsubishi" -Slug "mitsubishi"
New-TermIfMissing -Taxonomy "pa_make" -Name "Lexus" -Slug "lexus"
New-TermIfMissing -Taxonomy "pa_make" -Name "Acura" -Slug "acura"

# Model terms
New-TermIfMissing -Taxonomy "pa_model" -Name "Accord" -Slug "accord"
New-TermIfMissing -Taxonomy "pa_model" -Name "Civic" -Slug "civic"
New-TermIfMissing -Taxonomy "pa_model" -Name "Camry" -Slug "camry"
New-TermIfMissing -Taxonomy "pa_model" -Name "Corolla" -Slug "corolla"
New-TermIfMissing -Taxonomy "pa_model" -Name "350Z" -Slug "350z"
New-TermIfMissing -Taxonomy "pa_model" -Name "WRX" -Slug "wrx"
New-TermIfMissing -Taxonomy "pa_model" -Name "RX-8" -Slug "rx-8"
New-TermIfMissing -Taxonomy "pa_model" -Name "Evo X" -Slug "evo-x"
New-TermIfMissing -Taxonomy "pa_model" -Name "IS250" -Slug "is250"

# Engine terms
New-TermIfMissing -Taxonomy "pa_engine" -Name "K24" -Slug "k24"
New-TermIfMissing -Taxonomy "pa_engine" -Name "K20" -Slug "k20"
New-TermIfMissing -Taxonomy "pa_engine" -Name "2GR-FE" -Slug "2gr-fe"
New-TermIfMissing -Taxonomy "pa_engine" -Name "SR20DET" -Slug "sr20det"
New-TermIfMissing -Taxonomy "pa_engine" -Name "RB26DETT" -Slug "rb26dett"
New-TermIfMissing -Taxonomy "pa_engine" -Name "EJ20" -Slug "ej20"
New-TermIfMissing -Taxonomy "pa_engine" -Name "EJ25" -Slug "ej25"
New-TermIfMissing -Taxonomy "pa_engine" -Name "4B11T" -Slug "4b11t"

# Condition terms
New-TermIfMissing -Taxonomy "pa_condition" -Name "Used - Good" -Slug "used-good"
New-TermIfMissing -Taxonomy "pa_condition" -Name "Used - Fair" -Slug "used-fair"
New-TermIfMissing -Taxonomy "pa_condition" -Name "Refurbished" -Slug "refurbished"
New-TermIfMissing -Taxonomy "pa_condition" -Name "For Parts" -Slug "for-parts"

# Side terms
New-TermIfMissing -Taxonomy "pa_side" -Name "Left" -Slug "left"
New-TermIfMissing -Taxonomy "pa_side" -Name "Right" -Slug "right"
New-TermIfMissing -Taxonomy "pa_side" -Name "Front" -Slug "front"
New-TermIfMissing -Taxonomy "pa_side" -Name "Rear" -Slug "rear"
New-TermIfMissing -Taxonomy "pa_side" -Name "Driver Side" -Slug "driver-side"
New-TermIfMissing -Taxonomy "pa_side" -Name "Passenger Side" -Slug "passenger-side"

Write-Host "Seeding sample products..."

$productsPhp = @'
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
'@

Invoke-WpEval -PhpCode $productsPhp

Write-Host "Done."
