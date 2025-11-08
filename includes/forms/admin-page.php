<?php

// Don't allow direct access to this file.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add the admin menu page for Koi Cards.
 */
function koi_cards_admin_menu()
{
    add_menu_page(
        'Koi Cards',
        'Koi Cards',
        'manage_options',
        'koi-cards',
        'koi_cards_admin_page_display',
        'dashicons-images-alt2',
        20
    );
}
add_action('admin_menu', 'koi_cards_admin_menu');

/**
 * Handle form submissions for adding and deleting cards.
 */
function koi_cards_handle_form_submissions()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'koi_cards';

    // Handle adding a new card
    if (isset($_POST['koi_cards_add_card_nonce']) && wp_verify_nonce($_POST['koi_cards_add_card_nonce'], 'koi_cards_add_card')) {
        $name = sanitize_text_field($_POST['card_name']);
        $card_image = esc_url_raw($_POST['card_image']);
        $black_overlay_image = esc_url_raw($_POST['black_overlay_image']);
        $foil_mask_image = esc_url_raw($_POST['foil_mask_image']);

        if ($name && $card_image && $black_overlay_image && $foil_mask_image) {
            $wpdb->insert(
                $table_name,
                [
                    'name' => $name,
                    'card_image' => $card_image,
                    'black_overlay_image' => $black_overlay_image,
                    'foil_mask_image' => $foil_mask_image,
                ]
            );
        }
    }

    // Handle deleting a card
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['card_id']) && isset($_GET['_wpnonce'])) {
        if (wp_verify_nonce($_GET['_wpnonce'], 'koi_cards_delete_card_' . $_GET['card_id'])) {
            $id = absint($_GET['card_id']);
            $wpdb->delete($table_name, ['id' => $id], ['%d']);
        }
    }
}
add_action('admin_init', 'koi_cards_handle_form_submissions');

/**
 * Display the admin page for Koi Cards.
 */
function koi_cards_admin_page_display()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'koi_cards';
    $cards = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <h2>Add New Card</h2>
        <form method="post" action="">
            <?php wp_nonce_field('koi_cards_add_card', 'koi_cards_add_card_nonce'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="card_name">Card Name</label></th>
                        <td><input type="text" id="card_name" name="card_name" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="card_image">Card Base Image URL</label></th>
                        <td><input type="url" id="card_image" name="card_image" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="black_overlay_image">Black Overlay Image URL</label></th>
                        <td><input type="url" id="black_overlay_image" name="black_overlay_image" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="foil_mask_image">Foil Mask Image URL</label></th>
                        <td><input type="url" id="foil_mask_image" name="foil_mask_image" class="regular-text" required></td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button('Add Card'); ?>
        </form>

        <hr>

        <h2>Existing Cards</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" class="manage-column">Name</th>
                    <th scope="col" class="manage-column">Shortcode</th>
                    <th scope="col" class="manage-column">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cards)) : ?>
                    <tr>
                        <td colspan="3">No cards found.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($cards as $card) : ?>
                        <tr>
                            <td><?php echo esc_html($card->name); ?></td>
                            <td><code>[koi_card_tilt id="<?php echo $card->id; ?>"]</code></td>
                            <td>
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=koi-cards&action=delete&card_id=' . $card->id), 'koi_cards_delete_card_' . $card->id); ?>" onclick="return confirm('Are you sure you want to delete this card?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
