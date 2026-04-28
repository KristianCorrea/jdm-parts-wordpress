<?php
/**
 * JDM Miami — WooCommerce seed data
 *
 * Runs in one PHP process via WP-CLI:
 *
 *   Local (Docker):
 *     ./seed_data.sh
 *
 *   Remote / managed host (SSH + WP-CLI):
 *     wp eval-file wp-content/themes/jdm-miami-theme/seed_data.php
 *
 * Safe to re-run: existing terms and products are skipped, not duplicated.
 */

defined( 'ABSPATH' ) || die( 'Run via WP-CLI only.' );

if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce is not active. Activate it first.' );
	return;
}

// ---------------------------------------------------------------------------
// HELPERS
// ---------------------------------------------------------------------------

function jdm_seed_log( $msg ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $msg );
	} else {
		echo $msg . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Create a taxonomy term if it does not already exist.
 * Returns the term ID.
 */
function jdm_ensure_term( $taxonomy, $name, $slug, $description = '', $parent = 0 ) {
	$existing = get_term_by( 'slug', $slug, $taxonomy );
	if ( $existing ) {
		return $existing->term_id;
	}

	$args = array(
		'slug'        => $slug,
		'description' => $description,
	);
	if ( $parent ) {
		$args['parent'] = $parent;
	}

	$result = wp_insert_term( $name, $taxonomy, $args );
	if ( is_wp_error( $result ) ) {
		jdm_seed_log( '  [ERROR] ' . $name . ': ' . $result->get_error_message() );
		return 0;
	}

	jdm_seed_log( '  + ' . $name );
	return $result['term_id'];
}

/**
 * Create or update a WooCommerce global attribute.
 * Returns the attribute ID.
 */
function jdm_ensure_attribute( $name, $slug, $has_archives = true ) {
	$existing_id = wc_attribute_taxonomy_id_by_name( $slug );
	if ( $existing_id ) {
		return $existing_id;
	}

	$result = wc_create_attribute(
		array(
			'name'         => $name,
			'slug'         => $slug,
			'type'         => 'select',
			'order_by'     => 'menu_order',
			'has_archives' => $has_archives,
		)
	);

	if ( is_wp_error( $result ) ) {
		jdm_seed_log( '  [ERROR] attribute ' . $name . ': ' . $result->get_error_message() );
		return 0;
	}

	jdm_seed_log( '  + attribute: ' . $name );
	return $result;
}

/**
 * Create a simple WooCommerce product if it does not already exist.
 */
function jdm_ensure_product( $data ) {
	// Check by SKU — most reliable duplicate check.
	$existing_id = wc_get_product_id_by_sku( $data['sku'] );
	if ( $existing_id ) {
		jdm_seed_log( '  ~ exists: ' . $data['title'] );
		return $existing_id;
	}

	$product = new WC_Product_Simple();
	$product->set_name( $data['title'] );
	$product->set_sku( $data['sku'] );
	$product->set_regular_price( $data['price'] );
	$product->set_description( $data['description'] );
	$product->set_short_description( $data['short_description'] ?? '' );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_stock_status( 'instock' );
	$product->set_manage_stock( true );
	$product->set_stock_quantity( isset( $data['stock'] ) ? (int) $data['stock'] : 1 );

	// Build attributes array.
	$attributes = array();
	foreach ( $data['attributes'] as $attr_slug => $attr_values ) {
		$taxonomy = 'pa_' . $attr_slug;

		// Ensure the global taxonomy is registered before we use it.
		if ( ! taxonomy_exists( $taxonomy ) ) {
			jdm_seed_log( '    [WARN] taxonomy not registered yet: ' . $taxonomy . ' — run seed again after WC flushes rewrites.' );
			continue;
		}

		$term_ids = array();
		foreach ( (array) $attr_values as $val ) {
			$term = get_term_by( 'name', $val, $taxonomy );
			if ( ! $term ) {
				$term = get_term_by( 'slug', sanitize_title( $val ), $taxonomy );
			}
			if ( $term ) {
				$term_ids[] = $term->term_id;
			}
		}

		if ( empty( $term_ids ) ) {
			continue;
		}

		$attr = new WC_Product_Attribute();
		$attr->set_id( wc_attribute_taxonomy_id_by_name( ltrim( $taxonomy, 'pa_' ) ) );
		$attr->set_name( $taxonomy );
		$attr->set_options( $term_ids );
		$attr->set_position( 0 );
		$attr->set_visible( true );
		$attr->set_variation( false );

		$attributes[] = $attr;
	}
	$product->set_attributes( $attributes );

	$product_id = $product->save();

	if ( ! $product_id ) {
		jdm_seed_log( '  [ERROR] failed to save: ' . $data['title'] );
		return 0;
	}

	// Assign product categories.
	$cat_ids = array();
	foreach ( (array) $data['categories'] as $cat_slug ) {
		$term = get_term_by( 'slug', $cat_slug, 'product_cat' );
		if ( $term ) {
			$cat_ids[] = $term->term_id;
		}
	}
	if ( $cat_ids ) {
		wp_set_post_terms( $product_id, $cat_ids, 'product_cat' );
	}

	// Set global attribute taxonomy terms on the product post directly.
	foreach ( $data['attributes'] as $attr_slug => $attr_values ) {
		$taxonomy = 'pa_' . $attr_slug;
		if ( taxonomy_exists( $taxonomy ) ) {
			wp_set_post_terms( $product_id, (array) $attr_values, $taxonomy );
		}
	}

	jdm_seed_log( '  + ' . $data['title'] );
	return $product_id;
}

// ---------------------------------------------------------------------------
// 1. PRODUCT CATEGORIES
// ---------------------------------------------------------------------------
jdm_seed_log( "\n=== Product Categories ===" );

$categories = array(
	array( 'Engine & Engine Parts',      'engine-parts',              'OEM JDM engines, blocks, heads, and all internal engine components.' ),
	array( 'Transmissions & Drivetrain', 'transmissions-drivetrain',  'Transmissions, differentials, transfer cases, axles, and driveshafts.' ),
	array( 'Suspension & Steering',      'suspension-steering',       'Coilovers, control arms, subframes, racks, and steering components.' ),
	array( 'Brakes',                     'brakes',                    'Calipers, rotors, master cylinders, lines, and brake hardware.' ),
	array( 'Cooling System',             'cooling-system',            'Radiators, intercoolers, fans, thermostats, and hoses.' ),
	array( 'Electrical & Sensors',       'electrical-sensors',        'Alternators, starters, wiring harnesses, sensors, and fuse boxes.' ),
	array( 'ECU / Modules',              'ecu-modules',               'Engine control units, TCUs, ABS modules, and electronic control modules.' ),
	array( 'Interior Parts',             'interior-parts',            'JDM seats, dashboards, door panels, consoles, and trim pieces.' ),
	array( 'Exterior / Body Parts',      'exterior-body',             'Body panels, bumpers, mirrors, hoods, doors, and glass.' ),
	array( 'Wheels & Tires',             'wheels-tires',              'OEM JDM wheels, tires, center caps, and lug hardware.' ),
	array( 'Lighting',                   'lighting',                  'JDM headlights, tail lights, fog lights, and turn signals.' ),
	array( 'Fuel System',                'fuel-system',               'Fuel pumps, injectors, rails, tanks, and fuel delivery components.' ),
	array( 'Exhaust',                    'exhaust',                   'OEM manifolds, downpipes, cats, mid-pipes, and mufflers.' ),
	array( 'Accessories / Misc',         'accessories-misc',          'Miscellaneous OEM hardware, brackets, clips, and accessories.' ),
);

foreach ( $categories as $cat ) {
	jdm_ensure_term( 'product_cat', $cat[0], $cat[1], $cat[2] );
}

// ---------------------------------------------------------------------------
// 2. GLOBAL ATTRIBUTES
// ---------------------------------------------------------------------------
jdm_seed_log( "\n=== Global Attributes ===" );

$attributes = array(
	// slug (without pa_)   => [ label, has_archives ]
	'make'             => array( 'Make',              true ),
	'model'            => array( 'Model',             true ),
	'vehicle_year'     => array( 'Year',              true ),
	'engine'           => array( 'Engine',            true ),
	'transmission'     => array( 'Transmission Type', true ),
	'condition'        => array( 'Condition',         true ),
	'mileage'          => array( 'Mileage',           true ),
	'side'             => array( 'Side',              false ),
	'oem_part_number'  => array( 'OEM Part Number',   false ),
);

foreach ( $attributes as $slug => $cfg ) {
	jdm_ensure_attribute( $cfg[0], $slug, $cfg[1] );
}

// Flush so newly registered attribute taxonomies are available immediately.
delete_transient( 'wc_attribute_taxonomies' );
WC_Cache_Helper::invalidate_cache_group( 'woocommerce' );
if ( function_exists( 'wc_recount_all_terms' ) ) {
	wc_recount_all_terms();
}

// Re-register any attribute taxonomies created in this process so they are
// available for term creation and product assignment without a full WC init.
foreach ( wc_get_attribute_taxonomies() as $tax ) {
	$taxonomy = wc_attribute_taxonomy_name( $tax->attribute_name );
	if ( taxonomy_exists( $taxonomy ) ) {
		continue;
	}

	register_taxonomy(
		$taxonomy,
		apply_filters( 'woocommerce_taxonomy_objects_' . $taxonomy, array( 'product' ) ),
		apply_filters(
			'woocommerce_taxonomy_args_' . $taxonomy,
			array(
				'hierarchical'       => false,
				'show_ui'            => false,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => $tax->attribute_name ),
				'sort'               => false,
				'public'             => (bool) $tax->attribute_public,
				'show_in_nav_menus'  => (bool) $tax->attribute_public,
			)
		)
	);
}

flush_rewrite_rules();

// ---------------------------------------------------------------------------
// 3. ATTRIBUTE TERMS
// ---------------------------------------------------------------------------
jdm_seed_log( "\n=== Attribute Terms ===" );

// --- Make ---
jdm_seed_log( "\nMake:" );
$makes = array( 'Honda', 'Toyota', 'Nissan', 'Subaru', 'Mazda', 'Mitsubishi', 'Lexus', 'Acura', 'Infiniti', 'Scion' );
foreach ( $makes as $make ) {
	jdm_ensure_term( 'pa_make', $make, sanitize_title( $make ) );
}

// --- Model (prefixed with make for clarity in filters) ---
jdm_seed_log( "\nModel:" );
$models = array(
	// Honda
	'honda-civic'         => 'Honda Civic',
	'honda-accord'        => 'Honda Accord',
	'honda-crv'           => 'Honda CR-V',
	'honda-integra'       => 'Honda Integra',
	'honda-s2000'         => 'Honda S2000',
	'honda-prelude'       => 'Honda Prelude',
	// Toyota
	'toyota-camry'        => 'Toyota Camry',
	'toyota-corolla'      => 'Toyota Corolla',
	'toyota-supra'        => 'Toyota Supra',
	'toyota-celica'       => 'Toyota Celica',
	'toyota-rav4'         => 'Toyota RAV4',
	'toyota-tacoma'       => 'Toyota Tacoma',
	// Nissan
	'nissan-350z'         => 'Nissan 350Z',
	'nissan-370z'         => 'Nissan 370Z',
	'nissan-altima'       => 'Nissan Altima',
	'nissan-sentra'       => 'Nissan Sentra',
	'nissan-pathfinder'   => 'Nissan Pathfinder',
	'nissan-skyline'      => 'Nissan Skyline',
	// Subaru
	'subaru-wrx'          => 'Subaru WRX',
	'subaru-wrx-sti'      => 'Subaru WRX STI',
	'subaru-impreza'      => 'Subaru Impreza',
	'subaru-legacy'       => 'Subaru Legacy',
	'subaru-forester'     => 'Subaru Forester',
	// Mazda
	'mazda-rx8'           => 'Mazda RX-8',
	'mazda-mx5'           => 'Mazda MX-5',
	'mazda-3'             => 'Mazda 3',
	'mazda-6'             => 'Mazda 6',
	// Mitsubishi
	'mitsubishi-evo-x'    => 'Mitsubishi Evo X',
	'mitsubishi-eclipse'  => 'Mitsubishi Eclipse',
	'mitsubishi-galant'   => 'Mitsubishi Galant',
	// Lexus
	'lexus-is250'         => 'Lexus IS250',
	'lexus-is300'         => 'Lexus IS300',
	'lexus-gs300'         => 'Lexus GS300',
	'lexus-rx300'         => 'Lexus RX300',
	// Acura
	'acura-tl'            => 'Acura TL',
	'acura-tsx'           => 'Acura TSX',
	'acura-rsx'           => 'Acura RSX',
	// Infiniti
	'infiniti-g35'        => 'Infiniti G35',
	'infiniti-g37'        => 'Infiniti G37',
);
foreach ( $models as $slug => $name ) {
	jdm_ensure_term( 'pa_model', $name, $slug );
}

// --- Year (individual years, JDM common range 1990–2022) ---
jdm_seed_log( "\nYear:" );
foreach ( range( 1990, 2022 ) as $year ) {
	jdm_ensure_term( 'pa_vehicle_year', (string) $year, (string) $year );
}

// --- Engine ---
jdm_seed_log( "\nEngine:" );
$engines = array(
	'b16a'    => 'B16A',
	'b18c'    => 'B18C',
	'k20a'    => 'K20A',
	'k24a'    => 'K24A',
	'h22a'    => 'H22A',
	'f20c'    => 'F20C',
	'2jz-ge'  => '2JZ-GE',
	'2jz-gte' => '2JZ-GTE',
	'1jz-gte' => '1JZ-GTE',
	'3s-gte'  => '3S-GTE',
	'2gr-fe'  => '2GR-FE',
	'sr20det' => 'SR20DET',
	'rb25det' => 'RB25DET',
	'rb26dett'=> 'RB26DETT',
	'vq35de'  => 'VQ35DE',
	'vq37vhr' => 'VQ37VHR',
	'ej20'    => 'EJ20',
	'ej25'    => 'EJ25',
	'fa20'    => 'FA20',
	'13b-msp' => '13B-MSP',
	'4b11t'   => '4B11T',
	'4g63'    => '4G63',
	'2ar-fe'  => '2AR-FE',
	'3ur-fe'  => '3UR-FE',
);
foreach ( $engines as $slug => $name ) {
	jdm_ensure_term( 'pa_engine', $name, $slug );
}

// --- Transmission Type ---
jdm_seed_log( "\nTransmission:" );
$transmissions = array(
	'manual'    => 'Manual',
	'automatic' => 'Automatic',
	'cvt'       => 'CVT',
	'dct'       => 'DCT',
	'sequential'=> 'Sequential',
);
foreach ( $transmissions as $slug => $name ) {
	jdm_ensure_term( 'pa_transmission', $name, $slug );
}

// --- Condition ---
jdm_seed_log( "\nCondition:" );
$conditions = array(
	'used-good'   => 'Used - Good',
	'used-fair'   => 'Used - Fair',
	'refurbished' => 'Refurbished',
	'for-parts'   => 'For Parts Only',
);
foreach ( $conditions as $slug => $name ) {
	jdm_ensure_term( 'pa_condition', $name, $slug );
}

// --- Mileage ---
jdm_seed_log( "\nMileage:" );
$mileages = array(
	'under-50k'   => 'Under 50,000 mi',
	'50k-100k'    => '50,000 – 100,000 mi',
	'100k-150k'   => '100,000 – 150,000 mi',
	'over-150k'   => 'Over 150,000 mi',
	'unknown'     => 'Mileage Unknown',
);
foreach ( $mileages as $slug => $name ) {
	jdm_ensure_term( 'pa_mileage', $name, $slug );
}

// --- Side ---
jdm_seed_log( "\nSide:" );
$sides = array(
	'driver-side'    => 'Driver Side',
	'passenger-side' => 'Passenger Side',
	'front'          => 'Front',
	'rear'           => 'Rear',
	'left'           => 'Left',
	'right'          => 'Right',
);
foreach ( $sides as $slug => $name ) {
	jdm_ensure_term( 'pa_side', $name, $slug );
}

// ---------------------------------------------------------------------------
// 4. SAMPLE PRODUCTS
// ---------------------------------------------------------------------------
jdm_seed_log( "\n=== Sample Products ===" );

$products = array(

	// ---- ENGINES ----
	array(
		'title'             => 'JDM Honda Civic B16A DOHC VTEC Engine',
		'sku'               => 'JDM-HON-B16A-001',
		'price'             => '1299.00',
		'categories'        => array( 'engine-parts' ),
		'short_description' => 'Genuine JDM B16A DOHC VTEC engine pulled from a Japan-market Honda Civic SiR. Low mileage import.',
		'description'       => 'Pulled from a Japan-domestic Honda Civic SiR. The B16A is the original VTEC engine and a staple of Honda builds. This unit shows approximately 60k miles with no visible damage to the block or head. Oil and coolant passages clear. Sold as-is without accessory brackets.',
		'stock'             => 1,
		'attributes'        => array(
			'make'         => array( 'Honda' ),
			'model'        => array( 'Honda Civic' ),
			'vehicle_year' => array( '1999', '2000', '2001' ),
			'engine'       => array( 'B16A' ),
			'transmission' => array( 'Manual' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( 'Under 50,000 mi' ),
		),
	),

	array(
		'title'             => 'JDM Toyota Supra 2JZ-GTE Twin Turbo Engine',
		'sku'               => 'JDM-TOY-2JZGTE-001',
		'price'             => '4800.00',
		'categories'        => array( 'engine-parts' ),
		'short_description' => 'The legendary 2JZ-GTE twin-turbo inline-6 from a JDM Toyota Supra. Approximately 65k miles.',
		'description'       => 'The 2JZ-GTE needs no introduction. This unit was pulled from an imported Japan-market Supra. Sequential twin-turbo setup intact. Block and head in excellent condition. Comes with original wiring harness and turbo manifold. Approximate mileage 65,000 km (40k miles).',
		'stock'             => 1,
		'attributes'        => array(
			'make'         => array( 'Toyota' ),
			'model'        => array( 'Toyota Supra' ),
			'vehicle_year' => array( '1993', '1994', '1995', '1996', '1997', '1998' ),
			'engine'       => array( '2JZ-GTE' ),
			'transmission' => array( 'Manual', 'Automatic' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( 'Under 50,000 mi' ),
		),
	),

	array(
		'title'             => 'JDM Subaru WRX EJ20 Turbo Engine',
		'sku'               => 'JDM-SUB-EJ20-001',
		'price'             => '1850.00',
		'categories'        => array( 'engine-parts' ),
		'short_description' => 'JDM EJ20 turbocharged flat-four from an imported Subaru WRX. Tested runner.',
		'description'       => 'Genuine JDM EJ20 from a Japan-domestic WRX. This is the Japanese-spec turbo variant producing higher output than USDM equivalents. Pulled with approximately 55k km. AVCS intact. No known leaks. Sold engine-only without alternator or AC.',
		'stock'             => 2,
		'attributes'        => array(
			'make'         => array( 'Subaru' ),
			'model'        => array( 'Subaru WRX', 'Subaru Impreza' ),
			'vehicle_year' => array( '2002', '2003', '2004', '2005' ),
			'engine'       => array( 'EJ20' ),
			'transmission' => array( 'Manual' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( 'Under 50,000 mi' ),
		),
	),

	array(
		'title'             => 'JDM Nissan Skyline RB26DETT Engine',
		'sku'               => 'JDM-NIS-RB26-001',
		'price'             => '5500.00',
		'categories'        => array( 'engine-parts' ),
		'short_description' => 'RB26DETT twin-turbo straight-six from a JDM R34 Skyline GT-R. Rare and genuine.',
		'description'       => 'The RB26DETT is widely regarded as one of the greatest performance engines ever built. Pulled from an imported R34 GT-R with documented 72k km. Both turbos functional. Block casting and head gasket show no signs of lifting. A serious engine for a serious build.',
		'stock'             => 1,
		'attributes'        => array(
			'make'         => array( 'Nissan' ),
			'model'        => array( 'Nissan Skyline' ),
			'vehicle_year' => array( '1999', '2000', '2001', '2002' ),
			'engine'       => array( 'RB26DETT' ),
			'transmission' => array( 'Manual' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( 'Under 50,000 mi' ),
		),
	),

	array(
		'title'             => 'JDM Mazda RX-8 13B-MSP Renesis Engine',
		'sku'               => 'JDM-MAZ-13B-001',
		'price'             => '950.00',
		'categories'        => array( 'engine-parts' ),
		'short_description' => 'JDM 13B-MSP Renesis rotary engine from an imported Mazda RX-8. Running condition.',
		'description'       => 'JDM-spec 13B-MSP naturally aspirated rotary. Pulled from a Japan-market RX-8 with approximately 48k km. Compression tested and confirmed within spec. Apex seals measured at acceptable tolerance. Best for a build car or direct replacement with proper break-in.',
		'stock'             => 1,
		'attributes'        => array(
			'make'         => array( 'Mazda' ),
			'model'        => array( 'Mazda RX-8' ),
			'vehicle_year' => array( '2004', '2005', '2006', '2007', '2008' ),
			'engine'       => array( '13B-MSP' ),
			'transmission' => array( 'Manual', 'Automatic' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( 'Under 50,000 mi' ),
		),
	),

	// ---- TRANSMISSIONS ----
	array(
		'title'             => 'JDM Honda Civic B-Series 5-Speed Manual Transmission (S4C)',
		'sku'               => 'JDM-HON-CIV-5SPD-001',
		'price'             => '699.00',
		'categories'        => array( 'transmissions-drivetrain' ),
		'short_description' => 'OEM 5-speed manual from a JDM Honda Civic SiR. Bolt-on for B-series swaps.',
		'description'       => 'Genuine JDM S4C cable-operated 5-speed manual transmission. Pulled from a Japan-domestic Civic SiR. Shifts cleanly through all gears, no grinding or slipping. Low mileage import. Compatible with B16A, B18C, and other B-series engines.',
		'stock'             => 2,
		'attributes'        => array(
			'make'         => array( 'Honda' ),
			'model'        => array( 'Honda Civic', 'Honda Integra' ),
			'vehicle_year' => array( '1999', '2000', '2001' ),
			'engine'       => array( 'B16A', 'B18C' ),
			'transmission' => array( 'Manual' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( '50,000 – 100,000 mi' ),
		),
	),

	array(
		'title'             => 'JDM Subaru WRX STI V8 6-Speed Transmission (TY856)',
		'sku'               => 'JDM-SUB-STI-6SPD-001',
		'price'             => '1450.00',
		'categories'        => array( 'transmissions-drivetrain' ),
		'short_description' => 'STI V8 6-speed close-ratio gearbox from a JDM import. Includes DCCD-compatible rear diff.',
		'description'       => 'The TY856 6-speed from an imported Subaru WRX STI Version 8. Close-ratio gears built for performance. Includes rear differential assembly with DCCD wiring connector. Approx. 60k km. No case cracks. All synchros checked and functional.',
		'stock'             => 1,
		'attributes'        => array(
			'make'         => array( 'Subaru' ),
			'model'        => array( 'Subaru WRX STI' ),
			'vehicle_year' => array( '2003', '2004', '2005' ),
			'engine'       => array( 'EJ20', 'EJ25' ),
			'transmission' => array( 'Manual' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( 'Under 50,000 mi' ),
		),
	),

	// ---- ECU / MODULES ----
	array(
		'title'             => 'JDM Subaru WRX ECU OEM (2002–2005 EJ20)',
		'sku'               => 'JDM-SUB-WRX-ECU-001',
		'price'             => '249.99',
		'categories'        => array( 'ecu-modules' ),
		'short_description' => 'OEM ECU from an imported Subaru WRX. JDM-spec tune, direct fit EJ20.',
		'description'       => 'OEM ECU removed from a Japan-domestic WRX (EJ20 motor, 2002 chassis). Good used condition, no corrosion on connectors. JDM-spec calibration — will need base map or tune for non-JDM applications. Verified plugged in and reading correctly before removal.',
		'stock'             => 2,
		'attributes'        => array(
			'make'         => array( 'Subaru' ),
			'model'        => array( 'Subaru WRX', 'Subaru Impreza' ),
			'vehicle_year' => array( '2002', '2003', '2004', '2005' ),
			'engine'       => array( 'EJ20' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( '50,000 – 100,000 mi' ),
		),
	),

	array(
		'title'             => 'JDM Mitsubishi Evo X ACD/AYC Module (4B11T)',
		'sku'               => 'JDM-MIT-EVOX-ACD-001',
		'price'             => '389.00',
		'categories'        => array( 'ecu-modules' ),
		'short_description' => 'OEM ACD/AYC control module from a JDM Mitsubishi Lancer Evo X.',
		'description'       => 'Active Center Differential and Active Yaw Control module from a Japan-domestic Evo X. This is the JDM-spec unit with S-AWC capability. Pulled carefully with all connectors intact. Suitable for replacement or swap builds. Tested communicating via OBD prior to removal.',
		'stock'             => 1,
		'attributes'        => array(
			'make'         => array( 'Mitsubishi' ),
			'model'        => array( 'Mitsubishi Evo X' ),
			'vehicle_year' => array( '2008', '2009', '2010', '2011', '2012' ),
			'engine'       => array( '4B11T' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( '50,000 – 100,000 mi' ),
		),
	),

	// ---- ELECTRICAL & SENSORS ----
	array(
		'title'             => 'JDM Honda Accord Alternator OEM (K24A)',
		'sku'               => 'JDM-HON-ACC-ALT-001',
		'price'             => '149.99',
		'categories'        => array( 'electrical-sensors' ),
		'short_description' => 'OEM alternator from a JDM Honda Accord K24A. Tested and charging correctly.',
		'description'       => 'Pulled from a Japan-domestic Honda Accord (K24A motor). Tested output at 14.2V under load — charging normally. Approx. 65k miles with normal cosmetic wear. Bolt-on for all K24A applications including Accord, Element, and TSX.',
		'stock'             => 3,
		'attributes'        => array(
			'make'         => array( 'Honda' ),
			'model'        => array( 'Honda Accord' ),
			'vehicle_year' => array( '2008', '2009', '2010', '2011', '2012' ),
			'engine'       => array( 'K24A' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( '50,000 – 100,000 mi' ),
		),
	),

	// ---- SUSPENSION ----
	array(
		'title'             => 'JDM Nissan 350Z Front Knuckle Assembly (Driver Side)',
		'sku'               => 'JDM-NIS-350Z-KNK-001',
		'price'             => '189.00',
		'categories'        => array( 'suspension-steering' ),
		'short_description' => 'OEM front driver-side knuckle from a JDM Nissan 350Z. Complete with hub and bearing.',
		'description'       => 'Complete front driver-side knuckle assembly from an imported Nissan 350Z. Hub bearing shows no play. ABS ring intact. Knuckle casting has no cracks. Ready to install. Good option for track car builds or collision replacement.',
		'stock'             => 1,
		'attributes'        => array(
			'make'         => array( 'Nissan' ),
			'model'        => array( 'Nissan 350Z' ),
			'vehicle_year' => array( '2003', '2004', '2005', '2006', '2007', '2008' ),
			'engine'       => array( 'VQ35DE' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( '50,000 – 100,000 mi' ),
			'side'         => array( 'Driver Side' ),
		),
	),

	// ---- BRAKES ----
	array(
		'title'             => 'JDM Subaru WRX STI Brembo Front Brake Caliper Set',
		'sku'               => 'JDM-SUB-STI-BRK-001',
		'price'             => '425.00',
		'categories'        => array( 'brakes' ),
		'short_description' => 'OEM Brembo 4-pot front caliper set from a JDM WRX STI. Gold finish, good bore.',
		'description'       => 'Factory Brembo 4-piston front calipers from an imported Subaru WRX STI. Both calipers included. Pistons move freely with no seizing. Bleeder screws intact. Gold powder coat has normal wear. A significant upgrade over standard WRX hardware when fitted with proper brackets.',
		'stock'             => 1,
		'attributes'        => array(
			'make'         => array( 'Subaru' ),
			'model'        => array( 'Subaru WRX STI' ),
			'vehicle_year' => array( '2004', '2005', '2006', '2007' ),
			'engine'       => array( 'EJ25' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( 'Under 50,000 mi' ),
		),
	),

	// ---- COOLING ----
	array(
		'title'             => 'JDM Toyota Supra Intercooler (2JZ-GTE OEM)',
		'sku'               => 'JDM-TOY-SUP-IC-001',
		'price'             => '320.00',
		'categories'        => array( 'cooling-system' ),
		'short_description' => 'OEM front-mount intercooler from a JDM Toyota Supra twin-turbo. Clean core, no damage.',
		'description'       => 'Factory front-mount intercooler from a Japan-domestic Supra with the 2JZ-GTE. Core is clean, no fin damage, and no signs of leaks at the end tanks. Inlet and outlet flanges are straight. A great OEM replacement or baseline for a modest 2JZ build.',
		'stock'             => 1,
		'attributes'        => array(
			'make'         => array( 'Toyota' ),
			'model'        => array( 'Toyota Supra' ),
			'vehicle_year' => array( '1993', '1994', '1995', '1996', '1997', '1998' ),
			'engine'       => array( '2JZ-GTE' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( 'Under 50,000 mi' ),
		),
	),

	// ---- EXTERIOR ----
	array(
		'title'             => 'JDM Nissan 350Z Driver Side Mirror OEM',
		'sku'               => 'JDM-NIS-350Z-MIR-001',
		'price'             => '119.99',
		'categories'        => array( 'exterior-body' ),
		'short_description' => 'OEM driver-side mirror from a JDM Nissan 350Z. Power fold, no cracks.',
		'description'       => 'OEM driver side power-folding mirror from an imported Nissan 350Z. Glass intact with no chips. Housing has normal surface wear. Fold and adjust motors work correctly. Fits all 2003–2008 350Z chassis.',
		'stock'             => 2,
		'attributes'        => array(
			'make'         => array( 'Nissan' ),
			'model'        => array( 'Nissan 350Z' ),
			'vehicle_year' => array( '2003', '2004', '2005', '2006', '2007', '2008' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( '50,000 – 100,000 mi' ),
			'side'         => array( 'Driver Side' ),
		),
	),

	// ---- LIGHTING ----
	array(
		'title'             => 'JDM Honda Civic EK9 Type R Front Headlight Set',
		'sku'               => 'JDM-HON-EK9-HDLT-001',
		'price'             => '285.00',
		'categories'        => array( 'lighting' ),
		'short_description' => 'OEM JDM Civic Type R (EK9) headlight pair. Clear lenses, no cracks.',
		'description'       => 'Genuine JDM headlight set from a Honda Civic Type R (EK9). Both units included. Lenses are clear with no yellowing or cracks. Housings intact. These are the JDM-spec units with the Type R designation on the housing. Rare and highly sought for EK builds.',
		'stock'             => 1,
		'attributes'        => array(
			'make'         => array( 'Honda' ),
			'model'        => array( 'Honda Civic' ),
			'vehicle_year' => array( '1997', '1998', '1999', '2000', '2001' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( '50,000 – 100,000 mi' ),
		),
	),

	// ---- FUEL SYSTEM ----
	array(
		'title'             => 'JDM Mitsubishi Evo X Fuel Injector Set (4B11T OEM)',
		'sku'               => 'JDM-MIT-EVOX-INJ-001',
		'price'             => '299.00',
		'categories'        => array( 'fuel-system' ),
		'short_description' => 'Set of 4 OEM fuel injectors from a JDM Mitsubishi Evo X (4B11T).',
		'description'       => 'Complete set of 4 factory fuel injectors from a Japan-domestic Evo X. Injectors were flow-tested before removal and all within 2% variance of each other. O-rings and clips included. Direct replacements for 4B11T applications, also compatible with DSM/Evo builds with proper tuning.',
		'stock'             => 2,
		'attributes'        => array(
			'make'         => array( 'Mitsubishi' ),
			'model'        => array( 'Mitsubishi Evo X' ),
			'vehicle_year' => array( '2008', '2009', '2010', '2011', '2012' ),
			'engine'       => array( '4B11T' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( '50,000 – 100,000 mi' ),
		),
	),

	// ---- EXHAUST ----
	array(
		'title'             => 'JDM Toyota Supra 2JZ-GTE OEM Exhaust Manifold',
		'sku'               => 'JDM-TOY-SUP-MNF-001',
		'price'             => '215.00',
		'categories'        => array( 'exhaust' ),
		'short_description' => 'OEM twin-turbo exhaust manifold from a JDM Supra 2JZ-GTE. No cracks.',
		'description'       => 'Factory cast-iron exhaust manifold from a JDM Toyota Supra 2JZ-GTE twin-turbo. No cracks found on visual inspection or tap test. All 6 ports clean. Turbo mounting face straight. A reliable OEM baseline before going aftermarket.',
		'stock'             => 1,
		'attributes'        => array(
			'make'         => array( 'Toyota' ),
			'model'        => array( 'Toyota Supra' ),
			'vehicle_year' => array( '1993', '1994', '1995', '1996', '1997', '1998' ),
			'engine'       => array( '2JZ-GTE' ),
			'condition'    => array( 'Used - Good' ),
			'mileage'      => array( '50,000 – 100,000 mi' ),
		),
	),

);

foreach ( $products as $product_data ) {
	jdm_ensure_product( $product_data );
}

// Final cache flush.
delete_transient( 'wc_attribute_taxonomies' );
wc_delete_product_transients();
if ( function_exists( 'wc_recount_all_terms' ) ) {
	wc_recount_all_terms();
}
flush_rewrite_rules();

jdm_seed_log( "\n✓ Seed complete." );
