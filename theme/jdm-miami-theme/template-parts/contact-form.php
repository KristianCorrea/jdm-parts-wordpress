<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $parts = sanitize_textarea_field($_POST['parts']);

    $to = get_option('admin_email');
    $subject = 'New Part Request';
    $message = "Name: $name\nEmail: $email\nParts:\n$parts";

    wp_mail($to, $subject, $message);

    echo '<p style="color: #22c55e; text-align:center; margin-top:20px;">Request sent successfully!</p>';
}
?>


<section class="bg-neutral-950 text-white py-20">
    <div class="max-w-3xl mx-auto px-6">

        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold">
                <?php esc_html_e( 'Request a Part', 'jdm_miami' ); ?>
            </h2>
            <p class="mt-4 text-neutral-400">
                <?php esc_html_e( 'Tell us what you’re looking for and we’ll get back to you.', 'jdm_miami' ); ?>
            </p>
        </div>

        <form method="POST" class="space-y-6 bg-neutral-900 border border-neutral-800 p-8 rounded-2xl">

            <!-- Name -->
            <div>
                <label class="block text-sm mb-2 text-neutral-400">
                    <?php esc_html_e( 'Full Name', 'jdm_miami' ); ?>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    required
                    class="w-full px-4 py-3 bg-neutral-950 border border-neutral-700 rounded-lg focus:outline-none focus:border-cyan-400 transition"
                >
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm mb-2 text-neutral-400">
                    <?php esc_html_e( 'Email Address', 'jdm_miami' ); ?>
                </label>
                <input 
                    type="email" 
                    name="email" 
                    required
                    class="w-full px-4 py-3 bg-neutral-950 border border-neutral-700 rounded-lg focus:outline-none focus:border-cyan-400 transition"
                >
            </div>

            <!-- Interested Parts -->
            <div>
                <label class="block text-sm mb-2 text-neutral-400">
                    <?php esc_html_e( 'Interested Parts', 'jdm_miami' ); ?>
                </label>
                <textarea 
                    name="parts" 
                    rows="4"
                    required
                    placeholder="e.g. RB26 engine, transmission, ECU..."
                    class="w-full px-4 py-3 bg-neutral-950 border border-neutral-700 rounded-lg focus:outline-none focus:border-cyan-400 transition"
                ></textarea>
            </div>

            <!-- Submit -->
            <button 
                type="submit" 
                class="w-full py-3 bg-cyan-500 hover:bg-cyan-400 text-black font-semibold rounded-lg transition"
            >
                <?php esc_html_e( 'Submit Request', 'jdm_miami' ); ?>
            </button>

        </form>

    </div>
</section>