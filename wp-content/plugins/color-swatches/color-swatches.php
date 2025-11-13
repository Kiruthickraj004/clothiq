<?php
/*
Plugin Name: Color Swatches — Admin Only (multi-select)
Plugin URI:  https://example.com
Description: Admin-only color swatches manager. Central color list (option) and product metabox allowing multiple colors per product (with inline add).
Version:     1.0.0
Author:      You
Text Domain: color-swatches-admin-only
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Utilities: get/add colors (stored in option 'tch_colors')
 * Each color: array( 'id' => int, 'name' => string, 'hex' => '#rrggbb' )
 */
function tch_get_colors_option() {
    $colors = get_option( 'tch_colors', array() );
    if ( ! is_array( $colors ) ) $colors = array();
    return $colors;
}
function tch_get_color_by_id( $id ) {
    $colors = tch_get_colors_option();
    foreach ( $colors as $c ) {
        if ( intval( $c['id'] ) === intval( $id ) ) return $c;
    }
    return false;
}
function tch_add_color( $name, $hex = '' ) {
    $name = sanitize_text_field( $name );
    $hex  = sanitize_text_field( $hex );
    if ( empty( $name ) ) return new WP_Error( 'empty_name', __( 'Color name required', 'color-swatches-admin-only' ) );
    if ( $hex && $hex[0] !== '#' ) $hex = '#' . ltrim( $hex, '#' );

    $colors = tch_get_colors_option();

    // If name exists, update hex and return that color
    foreach ( $colors as $idx => $c ) {
        if ( strcasecmp( $c['name'], $name ) === 0 ) {
            if ( $hex ) {
                $colors[ $idx ]['hex'] = $hex;
                update_option( 'tch_colors', $colors );
            }
            return $colors[ $idx ];
        }
    }

    // assign next id
    $next = 1;
    if ( ! empty( $colors ) ) {
        $ids = array_map( function( $x ){ return intval( $x['id'] ); }, $colors );
        $next = max( $ids ) + 1;
    }

    $new = array( 'id' => $next, 'name' => $name, 'hex' => ( $hex ? $hex : '#cccccc' ) );
    $colors[] = $new;
    update_option( 'tch_colors', $colors );
    return $new;
}

/* --------------------------
   Admin enqueues & inline JS
   --------------------------*/
add_action( 'admin_enqueue_scripts', function( $hook ) {
    // Only load on product add/edit pages
    if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        $screen = get_current_screen();
        if ( $screen && $screen->post_type === 'product' ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
            // register a tiny admin script handle to attach inline JS
            wp_register_script( 'tch-admin-only-js', '', array( 'jquery', 'wp-color-picker' ), '1.0', true );
            wp_enqueue_script( 'tch-admin-only-js' );
            wp_localize_script( 'tch-admin-only-js', 'tchAdmin', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'tch_admin_nonce' ),
                'i18n_add_err' => __( 'Could not add color', 'color-swatches-admin-only' ),
            ) );

            // Inline JS: handles inline add (AJAX) and click-on-circle toggling
            $inline = <<<'JS'
(function($){
  $(function(){
    // initialize any color pickers
    $('.tch-color-field').wpColorPicker();

    // Inline add color via AJAX; append a new checkbox + swatch and check it
    $(document).on('click', '#tch-add-color-btn', function(e){
      e.preventDefault();
      var $btn = $(this);
      var name = $.trim($('input[name="new_color_name"]').val());
      var hex  = $('input[name="new_color_hex"]').val();
      if (!name) { alert('Please enter a color name'); return; }
      $btn.prop('disabled', true).text('Adding...');
      $.post(tchAdmin.ajax_url, {
        action: 'tch_create_color_meta',
        nonce: tchAdmin.nonce,
        name: name,
        hex: hex
      }, function(resp){
        if ( resp && resp.success && resp.data ) {
          var c = resp.data;
          // build an item HTML and append to wrapper
          var html = '<label class="tch-color-item" style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">'
                   + '<input type="checkbox" name="tch_product_color_ids[]" value="'+c.id+'" checked />'
                   + ' <a href="#" class="tch-admin-color-circle" style="display:inline-block;width:26px;height:26px;border-radius:50%;background:'+c.hex+';border:1px solid #ddd;margin-right:6px;text-indent:-9999px;overflow:hidden;">'+c.name+'</a>'
                   + '<span style="font-size:13px;color:#444;">'+c.name+'</span>'
                   + '</label>';
          $('.tch-colors-list').append(html);
          $('input[name="new_color_name"]').val('');
          if ( $('input[name="new_color_hex"]').wpColorPicker ) {
            $('input[name="new_color_hex"]').wpColorPicker('color', '#ffffff');
          } else {
            $('input[name="new_color_hex"]').val('#ffffff');
          }
        } else {
          alert( (resp && resp.data && resp.data.message) ? resp.data.message : tchAdmin.i18n_add_err );
        }
      }).fail(function(){ alert(tchAdmin.i18n_add_err); })
      .always(function(){ $btn.prop('disabled', false).text('Add'); });
    });

    // clicking the colored circle toggles the checkbox to improve UX
    $(document).on('click', '.tch-admin-color-circle', function(e){
      e.preventDefault();
      var $label = $(this).closest('label');
      var $checkbox = $label.find('input[type="checkbox"]').first();
      if ($checkbox.length) $checkbox.prop('checked', !$checkbox.prop('checked'));
    });
  });
})(jQuery);
JS;
            wp_add_inline_script( 'tch-admin-only-js', $inline );
        }
    }
}, 20 );

/* --------------------------
   Admin AJAX: create color (inline)
   --------------------------*/
add_action( 'wp_ajax_tch_create_color_meta', function() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'tch_admin_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
    }
    if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }
    $name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $hex  = isset( $_POST['hex'] ) ? sanitize_text_field( wp_unslash( $_POST['hex'] ) ) : '';
    if ( empty( $name ) ) wp_send_json_error( array( 'message' => 'Name required' ) );

    $new = tch_add_color( $name, $hex );
    if ( is_wp_error( $new ) ) wp_send_json_error( array( 'message' => $new->get_error_message() ) );
    wp_send_json_success( $new );
});

/* --------------------------
   Product metabox (normal, high) — multiple colors via checkboxes with circles
   Placed in 'normal' context with 'high' priority to appear before short description.
   --------------------------*/
add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'tch_product_colors_meta',
        __( 'Product Colors', 'color-swatches-admin-only' ),
        'tch_render_product_colors_metabox',
        'product',
        'normal',
        'high'
    );
} );

function tch_render_product_colors_metabox( $post ) {
    wp_nonce_field( 'tch_save_product_colors', 'tch_save_product_colors_nonce' );

    $colors = tch_get_colors_option();
    $assigned = get_post_meta( $post->ID, '_product_color_ids', true );
    if ( ! is_array( $assigned ) ) $assigned = array_map( 'intval', (array) $assigned );

    echo '<p style="margin-bottom:6px;">' . esc_html__( 'Select color(s) for this product:', 'color-swatches-admin-only' ) . '</p>';

    echo '<div class="tch-colors-list" style="display:flex;flex-direction:column;gap:6px;margin-bottom:10px;">';
    if ( ! empty( $colors ) ) {
        usort( $colors, function( $a, $b ){ return strcasecmp( $a['name'], $b['name'] ); });
        foreach ( $colors as $c ) {
            $checked = in_array( intval( $c['id'] ), $assigned, true ) ? 'checked' : '';
            printf(
                '<label style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">'
                . '<input type="checkbox" name="tch_product_color_ids[]" value="%d" %s />'
                . ' <a href="#" class="tch-admin-color-circle" style="display:inline-block;width:26px;height:26px;border-radius:50%%;background:%s;border:1px solid #ddd;margin-right:6px;text-indent:-9999px;overflow:hidden;">%s</a>'
                . ' <span style="font-size:13px;color:#444;">%s</span>'
                . '</label>',
                intval( $c['id'] ),
                $checked,
                esc_attr( $c['hex'] ),
                esc_html( $c['name'] ),
                esc_html( $c['name'] )
            );
        }
    } else {
        echo '<div style="color:#777;">' . esc_html__( 'No colors configured yet. Add one below or in inline add.', 'color-swatches-admin-only' ) . '</div>';
    }
    echo '</div>';

    // Inline add UI
    ?>
    <hr style="margin:10px 0;" />
    <div style="margin-top:8px;">
      <p style="margin:0 0 6px;"><strong><?php esc_html_e( 'Add new color (inline)', 'color-swatches-admin-only' ); ?></strong></p>
      <input type="text" name="new_color_name" placeholder="<?php esc_attr_e( 'Color name (e.g. Red)', 'color-swatches-admin-only' ); ?>" style="width:100%;margin-bottom:6px;" />
      <input type="text" name="new_color_hex" class="tch-color-field" value="#ffffff" style="width:100%;margin-bottom:6px;" />
      <button type="button" id="tch-add-color-btn" class="button"><?php esc_html_e( 'Add', 'color-swatches-admin-only' ); ?></button>
      <p class="description" style="margin-top:6px;"><?php esc_html_e( 'Create a color and it will be selected for this product immediately.', 'color-swatches-admin-only' ); ?></p>
    </div>
    <?php
}

/* --------------------------
   Save handler: store array of color ids in _product_color_ids postmeta
   --------------------------*/
add_action( 'save_post_product', function( $post_id ){
    if ( ! isset( $_POST['tch_save_product_colors_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tch_save_product_colors_nonce'] ) ), 'tch_save_product_colors' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $selected = array();
    if ( ! empty( $_POST['tch_product_color_ids'] ) && is_array( $_POST['tch_product_color_ids'] ) ) {
        $selected = array_map( 'intval', wp_unslash( $_POST['tch_product_color_ids'] ) );
        $selected = array_values( array_filter( array_unique( $selected ), function($v){ return $v > 0; } ) );
    }

    if ( ! empty( $selected ) ) {
        update_post_meta( $post_id, '_product_color_ids', $selected );
    } else {
        delete_post_meta( $post_id, '_product_color_ids' );
    }
});

/* --------------------------
   Optional helper: get product colors array (returns color arrays)
   --------------------------*/
function tch_get_product_colors( $product_id = 0 ) {
    if ( ! $product_id ) $product_id = get_the_ID();
    $ids = get_post_meta( $product_id, '_product_color_ids', true );
    if ( ! is_array( $ids ) ) $ids = array_map( 'intval', (array) $ids );
    $out = array();
    if ( empty( $ids ) ) return $out;
    $colors = tch_get_colors_option();
    foreach ( $ids as $id ) {
        foreach ( $colors as $c ) {
            if ( intval( $c['id'] ) === intval( $id ) ) {
                $out[] = $c;
                break;
            }
        }
    }
    return $out;
}
