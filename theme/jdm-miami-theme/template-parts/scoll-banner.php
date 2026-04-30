<head>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<head>

<?php
$images = [
    get_template_directory_uri() . '/assets/images/about-1.jpg',
    get_template_directory_uri() . '/assets/images/about-2.jpg',
    get_template_directory_uri() . '/assets/images/about-3.jpg',
];
?>

<section class="relative w-full overflow-hidden" x-data="{
    current: 0,
    images: <?php echo json_encode($images); ?>,
    init() {
        setInterval(() => {
            this.current = (this.current + 1) % this.images.length;
        }, 4000);
    }
}">
    
    <!-- Slides -->
    <div class="relative h-[320px] md:h-[420px] lg:h-[500px]">
        <template x-for="(image, index) in images" :key="index">
            <div 
                x-show="current === index"
                x-transition:enter="transition ease-out duration-700"
                x-transition:enter-start="opacity-0 scale-105"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0"
            >
                <img :src="image" class="w-full h-full object-cover" alt="About banner image">
            </div>
        </template>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50"></div>

        <!-- Content -->
        <div class="absolute inset-0 flex items-center justify-center text-center px-6">
            <div>
                <h1 class="text-3xl md:text-5xl font-bold text-white">
                    <?php esc_html_e( 'About Our Build', 'jdm_miami' ); ?>
                </h1>
                <p class="mt-4 text-neutral-300 max-w-xl">
                    <?php esc_html_e( 'Precision parts. Real imports. Built for serious enthusiasts.', 'jdm_miami' ); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Dots -->
    <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-2">
        <template x-for="(image, index) in images" :key="index">
            <button 
                @click="current = index"
                :class="current === index ? 'bg-cyan-400 w-6' : 'bg-white/40 w-3'"
                class="h-3 rounded-full transition-all duration-300"
            ></button>
        </template>
    </div>

</section>