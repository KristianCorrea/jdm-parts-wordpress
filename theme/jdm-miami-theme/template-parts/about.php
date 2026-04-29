<?php
/**
 * About 
 *
 * @package JDM_Miami
 */
?>

<section class="bg-neutral-950 text-white py-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            <!-- Left Content -->
            <div>
                <span class="text-sm tracking-widest uppercase text-cyan-400">
                    <?php esc_html_e( 'Who We Are', 'jdm_miami' ); ?>
                </span>

                <h2 class="mt-4 text-4xl lg:text-5xl font-bold leading-tight">
                    <?php esc_html_e( 'Built Around Passion.', 'jdm_miami' ); ?>
                    <br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">
                        <?php esc_html_e( 'Driven by Precision.', 'jdm_miami' ); ?>
                    </span>
                </h2>

                <p class="mt-6 text-neutral-400 max-w-xl leading-relaxed">
                    <?php esc_html_e( 'We specialize in sourcing authentic JDM engines and components directly from Japan. Every part is carefully inspected, documented, and selected for enthusiasts who demand reliability and performance.', 'jdm_miami' ); ?>
                </p>

                <p class="mt-4 text-neutral-400 max-w-xl leading-relaxed">
                    <?php esc_html_e( 'From full engine swaps to rare drivetrain components, our focus is simple: deliver parts you can trust, with the transparency builders actually need.', 'jdm_miami' ); ?>
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>"
                       class="px-6 py-3 bg-cyan-500 hover:bg-cyan-400 text-black font-semibold rounded-lg transition">
                        <?php esc_html_e( 'Browse Inventory', 'jdm_miami' ); ?>
                    </a>

                    <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
                       class="px-6 py-3 border border-neutral-700 hover:border-cyan-400 text-white rounded-lg transition">
                        <?php esc_html_e( 'Contact Us', 'jdm_miami' ); ?>
                    </a>
                </div>
            </div>

            <!-- Right Visual / Card -->
            <div class="relative">
                <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-2xl blur opacity-30"></div>

                <div class="relative bg-neutral-900 border border-neutral-800 rounded-2xl p-8">
                    
                    <h3 class="text-lg font-semibold text-cyan-400 mb-6">
                        <?php esc_html_e( 'At a Glance', 'jdm_miami' ); ?>
                    </h3>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <div class="text-3xl font-bold">500+</div>
                            <div class="text-sm text-neutral-500">
                                <?php esc_html_e( 'Parts Available', 'jdm_miami' ); ?>
                            </div>
                        </div>

                        <div>
                            <div class="text-3xl font-bold">10+</div>
                            <div class="text-sm text-neutral-500">
                                <?php esc_html_e( 'Years Experience', 'jdm_miami' ); ?>
                            </div>
                        </div>

                        <div>
                            <div class="text-3xl font-bold">48h</div>
                            <div class="text-sm text-neutral-500">
                                <?php esc_html_e( 'Fast Shipping', 'jdm_miami' ); ?>
                            </div>
                        </div>

                        <div>
                            <div class="text-3xl font-bold">100%</div>
                            <div class="text-sm text-neutral-500">
                                <?php esc_html_e( 'Inspected Parts', 'jdm_miami' ); ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-neutral-800 pt-6 text-sm text-neutral-500">
                        <?php esc_html_e( 'Import · Inspect · Deliver — built for enthusiasts who expect more.', 'jdm_miami' ); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>