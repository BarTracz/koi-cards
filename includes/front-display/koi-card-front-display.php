<?php

// Don't allow direct access to this file.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display the Koi Parallax Tilt Card.
 *
 * @return false|string
 */
function display_koi_card_tilt($atts): false|string
{
    $atts = shortcode_atts(['id' => 0], $atts, 'koi_card_tilt');
    $card_id = intval($atts['id']);

    if (!$card_id) {
        return '<div>Error: No card ID provided.</div>';
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'koi_cards';
    $card = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $card_id));

    if (!$card) {
        return '<div>Error: Card not found.</div>';
    }

    ob_start();
    ?>
    <div class="koi-card-wrapper">
        <div class="koi-tilt-container">
            <div class="koi-tilt-card" style="background-image: url('<?php echo esc_url($card->card_image); ?>');">
                <!-- PNG overlay with black elements, above foil, unaffected by tilt/foil -->
                <div class="koi-black-overlay">
                    <img src="<?php echo esc_url($card->black_overlay_image); ?>" alt="Black Elements Overlay" draggable="false" />
                </div>
                <!-- Strong chroma/foil effect layer, visible only on non-transparent parts of PNG mask -->
                <div class="koi-strong-foil-mask">
                    <img class="koi-strong-foil-mask-img" src="<?php echo esc_url($card->black_overlay_image); ?>" alt="Foil Mask" draggable="false" />
                    <div class="koi-strong-foil-gradient" style="mask-image: url('<?php echo esc_url($card->foil_mask_image); ?>'); -webkit-mask-image: url('<?php echo esc_url($card->foil_mask_image); ?>');"></div>
                </div>
                <div class="koi-foil-overlay"></div>
            </div>
        </div>
    </div>

    <style>
        /* Black PNG overlay for static elements */
        .koi-black-overlay {
            pointer-events: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 3; /* Above foil overlay */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .koi-black-overlay img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            user-select: none;
        }
        /* Strong foil/chroma effect layer, masked by PNG transparency */
        .koi-strong-foil-mask {
            pointer-events: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .koi-strong-foil-mask-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            user-select: none;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            pointer-events: none;
        }
        .koi-strong-foil-gradient {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 20px;
            opacity: 0.3;
            mix-blend-mode: lighten;
            background: conic-gradient(
                from 120deg at 120% -20%,
                #fff700 0%, #00ffd0 18%, #ff00ea 36%, #00bfff 48%, #ffb347 60%, #ff00cc 80%, #fff700 100%
            );
            background-size: 200% 200%;
            transition: background 0.2s, opacity 0.3s;
            mask-size: 100% 100%;
            mask-repeat: no-repeat;
            mask-position: center;
            -webkit-mask-size: 100% 100%;
            -webkit-mask-repeat: no-repeat;
            -webkit-mask-position: center;
        }
        /* Foil overlay for prismatic effect */
        .koi-foil-overlay {
            pointer-events: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 20px;
            z-index: 1;
            opacity: 0.1;
            mix-blend-mode: color-dodge;
            background: conic-gradient(
                from 120deg at 120% -20%,
                #ff00cc 0%, #00ffea 18%, #ffe600 36%, #ffb347 48%, #ff69b4 60%, #ff00cc 80%, #ffe600 100%
            );
            background-size: 200% 200%;
            transition: background 0.2s, opacity 0.3s;
        }
        .koi-card-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px 0;
            background: #f0f0f0;
            margin-top: 40px;
            border-radius: 10px;
        }
        .koi-tilt-container {
            perspective: 1000px;
        }
        .koi-tilt-card {
            width: 585px;
            height: 904px;
            background-size: 100%;
            background-position: center;
            border-radius: 20px;
            box-shadow: 0 20px 30px rgba(0,0,0,0.25);
            transition: transform 0.1s ease-out, background-position 0.1s ease-out;
            transform-style: preserve-3d;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            overflow: hidden;
        }
        .koi-tilt-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 20px;
            background: radial-gradient(
                circle at var(--mouse-x, 50%) var(--mouse-y, 50%),
                rgba(255, 255, 255, 0.15),
                transparent 40%
            );
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }
        .koi-tilt-container:hover .koi-tilt-card::before {
            opacity: 1;
        }
        .koi-tilt-content {
            transform: translateZ(50px);
            text-shadow: 0 5px 15px rgba(0,0,0,0.5);
        }
        .koi-tilt-content h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .koi-tilt-content p {
            font-size: 1rem;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all card containers to support multiple cards on the same page.
            const cardContainers = document.querySelectorAll('.koi-tilt-container');

            cardContainers.forEach(cardContainer => {
                const card = cardContainer.querySelector('.koi-tilt-card');
                const foil = card ? card.querySelector('.koi-foil-overlay') : null;
                const strongFoilGradient = card ? card.querySelector('.koi-strong-foil-gradient') : null;

                if (card) {
                    cardContainer.addEventListener('mousemove', (e) => {
                        const rect = card.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        const centerX = card.offsetWidth / 2;
                        const centerY = card.offsetHeight / 2;
                        const deltaX = (x - centerX) / centerX;
                        const deltaY = (y - centerY) / centerY;
                        const rotateY = deltaX * 15;
                        const rotateX = -deltaY * 15;
                        card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
                        const bgPosX = -deltaX * 5;
                        const bgPosY = -deltaY * 5;
                        card.style.backgroundPosition = `${50 + bgPosX}% ${50 + bgPosY}%`;
                        card.style.setProperty('--mouse-x', `${(x / card.offsetWidth) * 100}%`);
                        card.style.setProperty('--mouse-y', `${(y / card.offsetHeight) * 100}%`);
                        // Move foil overlay gradient with mouse
                        if (foil) {
                            const angle = Math.atan2(deltaY, deltaX) * 180 / Math.PI + 180;
                            foil.style.background = `conic-gradient(from ${angle}deg at 120% -20%, #ff00cc 0%, #00ffea 18%, #ffe600 36%, #ffb347 48%, #ff69b4 60%, #ff00cc 80%, #ffe600 100%)`;
                        }
                        // Move strong foil gradient with mouse
                        if (strongFoilGradient) {
                            const angle = Math.atan2(deltaY, deltaX) * 180 / Math.PI + 180;
                            strongFoilGradient.style.background = `conic-gradient(from ${angle}deg at 120% -20%, #fff700 0%, #00ffd0 18%, #ff00ea 36%, #00bfff 48%, #ffb347 60%, #ff00cc 80%, #fff700 100%)`;
                        }
                    });

                    cardContainer.addEventListener('mouseleave', () => {
                        card.style.transition = 'transform 0.5s ease, background-position 0.5s ease';
                        card.style.transform = 'rotateX(0deg) rotateY(0deg)';
                        card.style.backgroundPosition = 'center';
                        if (foil) {
                            foil.style.background = 'conic-gradient(from 120deg at 120% -20%, #ff00cc 0%, #00ffea 18%, #ffe600 36%, #ffb347 48%, #ff69b4 60%, #ff00cc 80%, #ffe600 100%)';
                        }
                        if (strongFoilGradient) {
                            strongFoilGradient.style.background = 'conic-gradient(from 120deg at 120% -20%, #fff700 0%, #00ffd0 18%, #ff00ea 36%, #00bfff 48%, #ffb347 60%, #ff00cc 80%, #fff700 100%)';
                        }
                    });

                    cardContainer.addEventListener('mouseenter', () => {
                        card.style.transition = 'transform 0.1s ease-out, background-position 0.1s ease-out';
                    });
                }
            });
        });
    </script>
    <?php
    return ob_get_clean();
}

// Register the shortcode to display the card.
add_shortcode('koi_card_tilt', 'display_koi_card_tilt');
