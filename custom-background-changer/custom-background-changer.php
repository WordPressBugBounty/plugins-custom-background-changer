<?php
/*
Plugin Name: Custom Background Changer
Plugin URI: https://wordpress.org/plugins/custom-background-changer/
Description: A WordPress plugin to change background color, gradient, image, overlay, or video on any individual post or page.
Version: 4.0
Author: Anshul G
Author URI: https://profiles.wordpress.org/anshuln90/
Donate URI: http://www.paypal.me/anshulgangrade
Text Domain: custom_background_changer
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CBC_VERSION', '4.0' );
define( 'CBC_FILE', basename( __FILE__ ) );
define( 'CBC_NAME', str_replace( '.php', '', CBC_FILE ) );
define( 'CBC_PATH', plugin_dir_path( __FILE__ ) );
define( 'CBC_URL', plugin_dir_url( __FILE__ ) );

// =========================================================
// ADMIN: Donate link on Plugins listing page
// =========================================================
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cbc_action_links' );
function cbc_action_links( $links ) {
    $donate_link = '<a href="' . esc_url( 'http://www.paypal.me/anshulgangrade' ) . '" target="_blank" style="color:#e65c00; font-weight:600;">&#9829; ' . esc_html__( 'Donate', 'custom_background_changer' ) . '</a>';
    array_unshift( $links, $donate_link );
    return $links;
}

add_filter( 'plugin_row_meta', 'cbc_plugin_row_meta', 10, 2 );
function cbc_plugin_row_meta( $links, $file ) {
    if ( plugin_basename( __FILE__ ) === $file ) {
        $links[] = '<a href="' . esc_url( 'http://www.paypal.me/anshulgangrade' ) . '" target="_blank">&#9829; ' . esc_html__( 'Support this plugin', 'custom_background_changer' ) . '</a>';
        $links[] = '<a href="' . esc_url( 'https://wordpress.org/plugins/custom-background-changer/' ) . '" target="_blank">' . esc_html__( 'Plugin Page', 'custom_background_changer' ) . '</a>';
    }
    return $links;
}

// =========================================================
// ADMIN: Enqueue assets only on post/page edit screens
// =========================================================
add_action( 'admin_enqueue_scripts', 'cbc_style_script_enqueue' );
function cbc_style_script_enqueue( $hook_suffix ) {
    if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }
    $td = 'custom_background_changer';

    wp_enqueue_style( 'cbc-metabox-css', CBC_URL . 'assets/css/cbc-metabox.css', array(), CBC_VERSION );
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'cbc-metabox-js', CBC_URL . 'assets/js/cbc-metabox.js', array( 'jquery', 'wp-color-picker' ), CBC_VERSION, true );
    wp_enqueue_media();
    wp_localize_script( 'cbc-metabox-js', 'meta_image', array(
        'title'  => esc_html__( 'Choose or Upload an Image', $td ),
        'button' => esc_html__( 'Use this image', $td ),
    ) );
}

// =========================================================
// ADMIN: Register Meta Box
// =========================================================
add_action( 'add_meta_boxes', 'cbc_custom_background_changer_metabox' );
function cbc_custom_background_changer_metabox() {
    add_meta_box(
        'cbc_metafield',
        esc_html__( 'Custom Background Changer', 'custom_background_changer' ),
        'cbc_meta_callback',
        array( 'post', 'page' ),
        'normal',
        'default'
    );
}

// =========================================================
// ADMIN: Meta Box HTML Output
// =========================================================
function cbc_meta_callback( $post ) {
    $td = 'custom_background_changer';
    wp_nonce_field( 'cbc_nonce_action', 'cbc_nonce' );

    $bg_option     = get_post_meta( $post->ID, 'cbc-bgoption',        true );
    $bg_color      = get_post_meta( $post->ID, 'cbc-bgcolor',         true );
    $bg_color2     = get_post_meta( $post->ID, 'cbc-bgcolor2',        true );
    $grad_angle    = get_post_meta( $post->ID, 'cbc-gradient-angle',  true ) ?: '135';
    $bg_image      = get_post_meta( $post->ID, 'cbc-bgimage',         true );
    $bg_attach     = get_post_meta( $post->ID, 'cbc-bgattach',        true ) ?: 'scroll';
    $bg_repeat     = get_post_meta( $post->ID, 'cbc-bgrepeat',        true ) ?: 'no-repeat';
    $bg_pos        = get_post_meta( $post->ID, 'cbc-bgposition',      true ) ?: 'left';
    $bg_size       = get_post_meta( $post->ID, 'cbc-bgsize',          true ) ?: 'auto';
    $overlay_color = get_post_meta( $post->ID, 'cbc-overlay-color',   true );
    $overlay_op    = get_post_meta( $post->ID, 'cbc-overlay-opacity', true ) ?: '0.5';
    $bg_video      = get_post_meta( $post->ID, 'cbc-bgvideo',         true );

    // Build gradient preview style
    if ( $bg_color && $bg_color2 ) {
        $preview_bg = 'linear-gradient(' . esc_attr( $grad_angle ) . 'deg, ' . esc_attr( $bg_color ) . ', ' . esc_attr( $bg_color2 ) . ')';
    } elseif ( $bg_color ) {
        $preview_bg = esc_attr( $bg_color );
    } else {
        $preview_bg = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
    }
    ?>

    <!-- ============= Enable Toggle ============= -->
    <div class="cbc-enable-row">
        <label>
            <input type="checkbox" name="cbc-bgoption" id="cbc-bgoption" value="on" <?php checked( $bg_option, 'on' ); ?>>
            <?php esc_html_e( 'Enable Custom Background for this post / page', $td ); ?>
        </label>
    </div>

    <!-- ============= Tab Navigation ============= -->
    <ul class="cbc-tabs-nav">
        <li class="active">
            <a href="#" data-tab="cbc-tab-color">
                <span class="cbc-tab-icon"><span class="dashicons dashicons-art"></span></span>
                <?php esc_html_e( 'Color / Gradient', $td ); ?>
            </a>
        </li>
        <li>
            <a href="#" data-tab="cbc-tab-image">
                <span class="cbc-tab-icon"><span class="dashicons dashicons-format-image"></span></span>
                <?php esc_html_e( 'Image', $td ); ?>
            </a>
        </li>
        <li>
            <a href="#" data-tab="cbc-tab-overlay">
                <span class="cbc-tab-icon"><span class="dashicons dashicons-admin-appearance"></span></span>
                <?php esc_html_e( 'Overlay', $td ); ?>
            </a>
        </li>
        <li>
            <a href="#" data-tab="cbc-tab-video">
                <span class="cbc-tab-icon"><span class="dashicons dashicons-video-alt3"></span></span>
                <?php esc_html_e( 'Video', $td ); ?>
            </a>
        </li>
    </ul>

    <!-- ============= TAB 1: Color / Gradient ============= -->
    <div id="cbc-tab-color" class="cbc-tab-panel active">
        <p class="cbc-section-title"><?php esc_html_e( 'Solid Color or Gradient Background', $td ); ?></p>

        <div class="cbc-field-row">
            <span class="cbc-field-label"><?php esc_html_e( 'Primary Color', $td ); ?></span>
            <div class="cbc-field-input">
                <input type="text" id="cbc-bgcolor" name="cbc-bgcolor" value="<?php echo esc_attr( $bg_color ); ?>" />
            </div>
        </div>

        <div class="cbc-field-row">
            <span class="cbc-field-label"><?php esc_html_e( 'Gradient Color 2', $td ); ?></span>
            <div class="cbc-field-input">
                <input type="text" id="cbc-gradient-color2" name="cbc-bgcolor2" value="<?php echo esc_attr( $bg_color2 ); ?>" />
                <span class="description"><?php esc_html_e( 'Leave empty for solid color. Fill to enable gradient.', $td ); ?></span>
            </div>
        </div>

        <div class="cbc-field-row">
            <span class="cbc-field-label"><?php esc_html_e( 'Gradient Angle (°)', $td ); ?></span>
            <div class="cbc-field-input">
                <input type="number" id="cbc-gradient-angle" name="cbc-gradient-angle"
                    value="<?php echo esc_attr( $grad_angle ); ?>" min="0" max="360" />
            </div>
        </div>

        <!-- Live Preview -->
        <div class="cbc-gradient-preview-wrap">
            <span class="cbc-gradient-preview-label"><?php esc_html_e( 'Live Preview', $td ); ?></span>
            <div class="cbc-gradient-preview" style="background: <?php echo esc_attr( $preview_bg ); ?>;"></div>
        </div>
    </div>

    <!-- ============= TAB 2: Image ============= -->
    <div id="cbc-tab-image" class="cbc-tab-panel">
        <p class="cbc-section-title"><?php esc_html_e( 'Background Image Settings', $td ); ?></p>

        <div class="cbc-field-row">
            <span class="cbc-field-label"><?php esc_html_e( 'Image URL', $td ); ?></span>
            <div class="cbc-field-input">
                <div class="cbc-image-upload-row">
                    <input type="text" id="cbc-bgimage" name="cbc-bgimage" value="<?php echo esc_url( $bg_image ); ?>" placeholder="https://..." />
                    <input type="button" id="cbc-bgimage-button" class="button" value="<?php esc_attr_e( 'Choose Image', $td ); ?>" />
                </div>
            </div>
        </div>

        <div class="cbc-field-row">
            <span class="cbc-field-label"><?php esc_html_e( 'Attachment', $td ); ?></span>
            <div class="cbc-field-input">
                <div class="cbc-radio-group">
                    <label><input type="radio" name="cbc-bgattach" value="scroll" <?php checked( $bg_attach, 'scroll' ); ?>> <?php esc_html_e( 'Scroll', $td ); ?></label>
                    <label><input type="radio" name="cbc-bgattach" value="fixed"  <?php checked( $bg_attach, 'fixed' );  ?>> <?php esc_html_e( 'Fixed (Parallax)', $td ); ?></label>
                </div>
            </div>
        </div>

        <div class="cbc-field-row">
            <span class="cbc-field-label"><?php esc_html_e( 'Repeat', $td ); ?></span>
            <div class="cbc-field-input">
                <select name="cbc-bgrepeat" id="cbc-bgrepeat">
                    <option value="no-repeat" <?php selected( $bg_repeat, 'no-repeat' ); ?>><?php esc_html_e( 'No Repeat',              $td ); ?></option>
                    <option value="repeat"    <?php selected( $bg_repeat, 'repeat' );    ?>><?php esc_html_e( 'Repeat Both',             $td ); ?></option>
                    <option value="repeat-x"  <?php selected( $bg_repeat, 'repeat-x' ); ?>><?php esc_html_e( 'Repeat Horizontally',     $td ); ?></option>
                    <option value="repeat-y"  <?php selected( $bg_repeat, 'repeat-y' ); ?>><?php esc_html_e( 'Repeat Vertically',       $td ); ?></option>
                </select>
            </div>
        </div>

        <div class="cbc-field-row">
            <span class="cbc-field-label"><?php esc_html_e( 'Position', $td ); ?></span>
            <div class="cbc-field-input">
                <select name="cbc-bgposition" id="cbc-bgposition">
                    <option value="left"   <?php selected( $bg_pos, 'left' );   ?>><?php esc_html_e( 'Left',   $td ); ?></option>
                    <option value="center" <?php selected( $bg_pos, 'center' ); ?>><?php esc_html_e( 'Center', $td ); ?></option>
                    <option value="right"  <?php selected( $bg_pos, 'right' );  ?>><?php esc_html_e( 'Right',  $td ); ?></option>
                </select>
            </div>
        </div>

        <div class="cbc-field-row">
            <span class="cbc-field-label"><?php esc_html_e( 'Size', $td ); ?></span>
            <div class="cbc-field-input">
                <select name="cbc-bgsize" id="cbc-bgsize">
                    <option value="auto"    <?php selected( $bg_size, 'auto' );    ?>><?php esc_html_e( 'Auto',                $td ); ?></option>
                    <option value="cover"   <?php selected( $bg_size, 'cover' );   ?>><?php esc_html_e( 'Cover (Full Screen)', $td ); ?></option>
                    <option value="contain" <?php selected( $bg_size, 'contain' ); ?>><?php esc_html_e( 'Contain',             $td ); ?></option>
                </select>
            </div>
        </div>
    </div>

    <!-- ============= TAB 3: Overlay ============= -->
    <div id="cbc-tab-overlay" class="cbc-tab-panel">
        <p class="cbc-section-title"><?php esc_html_e( 'Color Overlay', $td ); ?></p>
        <div class="cbc-info-box" style="margin-bottom:14px;">
            <p><span class="dashicons dashicons-info"></span><?php esc_html_e( 'Add a translucent color layer on top of your background image or video to improve text readability.', $td ); ?></p>
        </div>

        <div class="cbc-field-row">
            <span class="cbc-field-label"><?php esc_html_e( 'Overlay Color', $td ); ?></span>
            <div class="cbc-field-input">
                <input type="text" id="cbc-overlay-color" name="cbc-overlay-color" value="<?php echo esc_attr( $overlay_color ); ?>" />
                <span class="description"><?php esc_html_e( 'Leave empty to disable overlay.', $td ); ?></span>
            </div>
        </div>

        <div class="cbc-field-row">
            <span class="cbc-field-label"><?php esc_html_e( 'Opacity', $td ); ?></span>
            <div class="cbc-field-input">
                <div class="cbc-opacity-wrap">
                    <input type="range" id="cbc-overlay-opacity" name="cbc-overlay-opacity"
                        value="<?php echo esc_attr( $overlay_op ); ?>" min="0.1" max="1" step="0.1" />
                    <span class="cbc-opacity-badge"><?php echo esc_html( $overlay_op ); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============= TAB 4: Video ============= -->
    <div id="cbc-tab-video" class="cbc-tab-panel">
        <p class="cbc-section-title"><?php esc_html_e( 'MP4 Video Background', $td ); ?></p>
        <div class="cbc-info-box" style="margin-bottom:14px;">
            <p><span class="dashicons dashicons-video-alt3"></span><?php esc_html_e( 'Upload or link an MP4 video. It will autoplay, loop silently, and cover the full screen. Use Overlay tab for a color tint on top.', $td ); ?></p>
        </div>

        <div class="cbc-field-row">
            <span class="cbc-field-label"><?php esc_html_e( 'Video URL (.mp4)', $td ); ?></span>
            <div class="cbc-field-input">
                <div class="cbc-image-upload-row">
                    <input type="url" id="cbc-bgvideo" name="cbc-bgvideo" value="<?php echo esc_url( $bg_video ); ?>" placeholder="https://example.com/video.mp4" />
                    <input type="button" id="cbc-bgvideo-button" class="button" value="<?php esc_attr_e( 'Choose Video', $td ); ?>" />
                </div>
                <span class="description"><?php esc_html_e( 'Video background overrides image background on the frontend.', $td ); ?></span>
            </div>
        </div>
    </div>
    <?php
}

// =========================================================
// ADMIN: Save Meta Data
// =========================================================
add_action( 'save_post', 'cbc_metafield_save' );
function cbc_metafield_save( $post_id ) {
    $is_autosave    = wp_is_post_autosave( $post_id );
    $is_revision    = wp_is_post_revision( $post_id );
    $is_valid_nonce = isset( $_POST['cbc_nonce'] ) && wp_verify_nonce( $_POST['cbc_nonce'], 'cbc_nonce_action' );

    if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $allowed_attaches  = array( 'scroll', 'fixed' );
    $allowed_repeats   = array( 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' );
    $allowed_positions = array( 'left', 'center', 'right' );
    $allowed_sizes     = array( 'auto', 'cover', 'contain' );

    update_post_meta( $post_id, 'cbc-bgoption',        isset( $_POST['cbc-bgoption'] ) ? 'on' : 'off' );
    update_post_meta( $post_id, 'cbc-bgcolor',         isset( $_POST['cbc-bgcolor'] )  ? sanitize_hex_color( $_POST['cbc-bgcolor'] )  : '' );
    update_post_meta( $post_id, 'cbc-bgcolor2',        isset( $_POST['cbc-bgcolor2'] ) ? sanitize_hex_color( $_POST['cbc-bgcolor2'] ) : '' );
    update_post_meta( $post_id, 'cbc-gradient-angle',  isset( $_POST['cbc-gradient-angle'] ) ? absint( $_POST['cbc-gradient-angle'] ) : 135 );
    update_post_meta( $post_id, 'cbc-bgimage',         isset( $_POST['cbc-bgimage'] )  ? esc_url_raw( $_POST['cbc-bgimage'] ) : '' );
    update_post_meta( $post_id, 'cbc-bgattach',        ( isset( $_POST['cbc-bgattach'] ) && in_array( $_POST['cbc-bgattach'], $allowed_attaches, true ) )   ? sanitize_text_field( $_POST['cbc-bgattach'] )   : 'scroll' );
    update_post_meta( $post_id, 'cbc-bgrepeat',        ( isset( $_POST['cbc-bgrepeat'] ) && in_array( $_POST['cbc-bgrepeat'], $allowed_repeats, true ) )     ? sanitize_text_field( $_POST['cbc-bgrepeat'] )   : 'no-repeat' );
    update_post_meta( $post_id, 'cbc-bgposition',      ( isset( $_POST['cbc-bgposition'] ) && in_array( $_POST['cbc-bgposition'], $allowed_positions, true ) ) ? sanitize_text_field( $_POST['cbc-bgposition'] ) : 'left' );
    update_post_meta( $post_id, 'cbc-bgsize',          ( isset( $_POST['cbc-bgsize'] ) && in_array( $_POST['cbc-bgsize'], $allowed_sizes, true ) )           ? sanitize_text_field( $_POST['cbc-bgsize'] )     : 'auto' );
    update_post_meta( $post_id, 'cbc-overlay-color',   isset( $_POST['cbc-overlay-color'] )   ? sanitize_hex_color( $_POST['cbc-overlay-color'] ) : '' );

    $opacity = isset( $_POST['cbc-overlay-opacity'] ) ? (float) $_POST['cbc-overlay-opacity'] : 0.5;
    update_post_meta( $post_id, 'cbc-overlay-opacity', min( 1.0, max( 0.1, $opacity ) ) );
    update_post_meta( $post_id, 'cbc-bgvideo',         isset( $_POST['cbc-bgvideo'] ) ? esc_url_raw( $_POST['cbc-bgvideo'] ) : '' );
}

// =========================================================
// FRONTEND: Body Class
// =========================================================
add_filter( 'body_class', 'cbc_add_body_class' );
function cbc_add_body_class( $classes ) {
    $classes[] = 'cbc-page';
    return $classes;
}

// =========================================================
// FRONTEND: Inject CSS (Color / Gradient / Image)
// =========================================================
add_action( 'wp_head', 'cbc_call_style_header' );
function cbc_call_style_header() {
    if ( ! is_singular() ) { return; }
    global $post;
    if ( ! $post ) { return; }

    $bgoption = get_post_meta( $post->ID, 'cbc-bgoption', true );
    if ( $bgoption !== 'on' ) { return; }

    $bgcolor    = get_post_meta( $post->ID, 'cbc-bgcolor',    true );
    $bgcolor2   = get_post_meta( $post->ID, 'cbc-bgcolor2',   true );
    $angle      = absint( get_post_meta( $post->ID, 'cbc-gradient-angle', true ) ?: 135 );
    $bgimage    = get_post_meta( $post->ID, 'cbc-bgimage',    true );
    $bgattach   = get_post_meta( $post->ID, 'cbc-bgattach',   true );
    $bgrepeat   = get_post_meta( $post->ID, 'cbc-bgrepeat',   true );
    $bgposition = get_post_meta( $post->ID, 'cbc-bgposition', true );
    $bgsize     = get_post_meta( $post->ID, 'cbc-bgsize',     true );
    $bgvideo    = get_post_meta( $post->ID, 'cbc-bgvideo',    true );

    $css_parts = array();
    if ( ! $bgvideo ) {
        if ( $bgcolor && $bgcolor2 ) {
            $css_parts[] = 'background: linear-gradient(' . $angle . 'deg, ' . sanitize_hex_color( $bgcolor ) . ', ' . sanitize_hex_color( $bgcolor2 ) . ');';
        } elseif ( $bgcolor ) {
            $css_parts[] = 'background-color: ' . sanitize_hex_color( $bgcolor ) . ';';
        }
        if ( $bgimage ) {
            $css_parts[] = 'background-image: url("' . esc_url( $bgimage ) . '");';
            if ( $bgattach )   { $css_parts[] = 'background-attachment: ' . sanitize_text_field( $bgattach )   . ';'; }
            if ( $bgrepeat )   { $css_parts[] = 'background-repeat: '    . sanitize_text_field( $bgrepeat )    . ';'; }
            if ( $bgposition ) { $css_parts[] = 'background-position: top ' . sanitize_text_field( $bgposition ) . ';'; }
            if ( $bgsize )     { $css_parts[] = 'background-size: '      . sanitize_text_field( $bgsize )      . ';'; }
        }
    }

    if ( ! empty( $css_parts ) ) {
        echo '<style type="text/css">body.cbc-page{' . implode( '', $css_parts ) . '}</style>' . "\n";
    }
}

// =========================================================
// FRONTEND: Video & Overlay Layer (wp_footer)
// =========================================================
add_action( 'wp_footer', 'cbc_inject_background_layer' );
function cbc_inject_background_layer() {
    if ( ! is_singular() ) { return; }
    global $post;
    if ( ! $post ) { return; }

    $bgoption = get_post_meta( $post->ID, 'cbc-bgoption', true );
    if ( $bgoption !== 'on' ) { return; }

    $bgvideo       = get_post_meta( $post->ID, 'cbc-bgvideo',         true );
    $overlay_color = get_post_meta( $post->ID, 'cbc-overlay-color',   true );
    $overlay_op    = (float) ( get_post_meta( $post->ID, 'cbc-overlay-opacity', true ) ?: 0.5 );
    $bgcolor       = get_post_meta( $post->ID, 'cbc-bgcolor',         true );
    $bgcolor2      = get_post_meta( $post->ID, 'cbc-bgcolor2',        true );
    $angle         = absint( get_post_meta( $post->ID, 'cbc-gradient-angle', true ) ?: 135 );
    $bgimage       = get_post_meta( $post->ID, 'cbc-bgimage',         true );
    $bgattach      = get_post_meta( $post->ID, 'cbc-bgattach',        true );
    $bgrepeat      = get_post_meta( $post->ID, 'cbc-bgrepeat',        true );
    $bgposition    = get_post_meta( $post->ID, 'cbc-bgposition',      true );
    $bgsize        = get_post_meta( $post->ID, 'cbc-bgsize',          true );

    $has_video   = ! empty( $bgvideo );
    $has_overlay = ! empty( $overlay_color );

    if ( ! $has_video && ! $has_overlay ) { return; }

    $layer_style  = 'position:fixed;top:0;left:0;width:100%;height:100%;z-index:-9998;overflow:hidden;pointer-events:none;';
    $video_style  = 'position:absolute;top:50%;left:50%;min-width:100%;min-height:100%;width:auto;height:auto;transform:translate(-50%,-50%);';

    echo '<div id="cbc-bg-layer" style="' . esc_attr( $layer_style ) . '">';
    if ( $has_video ) {
        $fallback = '';
        if ( $bgcolor && $bgcolor2 ) {
            $fallback = 'background:linear-gradient(' . $angle . 'deg,' . sanitize_hex_color( $bgcolor ) . ',' . sanitize_hex_color( $bgcolor2 ) . ');';
        } elseif ( $bgcolor ) {
            $fallback = 'background-color:' . sanitize_hex_color( $bgcolor ) . ';';
        } elseif ( $bgimage ) {
            $fallback  = 'background-image:url("' . esc_url( $bgimage ) . '");';
            if ( $bgattach )   { $fallback .= 'background-attachment:' . sanitize_text_field( $bgattach )   . ';'; }
            if ( $bgrepeat )   { $fallback .= 'background-repeat:'    . sanitize_text_field( $bgrepeat )    . ';'; }
            if ( $bgposition ) { $fallback .= 'background-position:top ' . sanitize_text_field( $bgposition ) . ';'; }
            if ( $bgsize )     { $fallback .= 'background-size:'      . sanitize_text_field( $bgsize )      . ';'; }
        }
        echo '<div style="position:absolute;top:0;left:0;width:100%;height:100%;' . esc_attr( $fallback ) . '">';
        echo '<video autoplay loop muted playsinline style="' . esc_attr( $video_style ) . '">';
        echo '<source src="' . esc_url( $bgvideo ) . '" type="video/mp4">';
        echo '</video>';
        echo '</div>';
    }
    echo '</div>';

    if ( $has_overlay ) {
        $os = 'position:fixed;top:0;left:0;width:100%;height:100%;z-index:-9997;pointer-events:none;background-color:' . sanitize_hex_color( $overlay_color ) . ';opacity:' . esc_attr( $overlay_op ) . ';';
        echo '<div id="cbc-overlay-layer" style="' . esc_attr( $os ) . '"></div>';
    }
}