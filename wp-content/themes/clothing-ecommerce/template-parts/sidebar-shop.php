<?php
// sidebar-shop.php - fallback sidebar used by archive-product.php
defined( 'ABSPATH' ) || exit;
wp_enqueue_script( 'jquery-ui-slider' );
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />

<div class="shop_sidebar_area">
  <div class="widget catagory mb-50">
    <h6 class="widget-title mb-30">Categories</h6>
    <div class="catagories-menu">
      <?php
      wp_list_categories( array(
        'taxonomy'   => 'product_cat',
        'title_li'   => '',
        'depth'      => 2,
      ) );
      ?>
    </div>
  </div>
  <?php
  global $wpdb;
  $prices = $wpdb->get_row(
      "SELECT 
         MIN(CAST(pm.meta_value AS DECIMAL(12,2))) AS min_price,
         MAX(CAST(pm.meta_value AS DECIMAL(12,2))) AS max_price
       FROM {$wpdb->postmeta} pm
       INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
       WHERE pm.meta_key = '_price'
         AND p.post_type = 'product'
         AND p.post_status = 'publish'
       LIMIT 1"
  );
  $min_store = ( $prices && $prices->min_price !== null ) ? floatval( $prices->min_price ) : 0;
  $max_store = ( $prices && $prices->max_price !== null ) ? floatval( $prices->max_price ) : 1000;

  if ( $min_store < 0 ) $min_store = 0;
  if ( $max_store <= 0 ) $max_store = max( 100, $min_store + 500 );
  $cur_min = isset( $_GET['min_price'] ) ? floatval( wp_unslash( $_GET['min_price'] ) ) : $min_store;
  $cur_max = isset( $_GET['max_price'] ) ? floatval( wp_unslash( $_GET['max_price'] ) ) : $max_store;
  $shop_id = wc_get_page_id( 'shop' );
  $shop_link = $shop_id && $shop_id > 0 ? get_permalink( $shop_id ) : get_post_type_archive_link( 'product' );
  if ( ! $shop_link ) {
      $shop_link = home_url( add_query_arg( array() ) );
  }
  $preserve = $_GET;
  unset( $preserve['min_price'], $preserve['max_price'], $preserve['paged'] );
  $currency_symbol = html_entity_decode( get_woocommerce_currency_symbol() );
  ?>
  <div class="widget price mb-50">
    <h6 class="widget-title mb-30">Filter by</h6>
    <p class="widget-title2 mb-30">Price</p>
    <div class="widget-desc">
      <form method="get" action="<?php echo esc_url( $shop_link ); ?>" class="tch-price-filter-form">
        <div class="slider-range">
          <div data-min="<?php echo esc_attr( $min_store ); ?>"
               data-max="<?php echo esc_attr( $max_store ); ?>"
               data-unit="<?php echo esc_attr( $currency_symbol ); ?>"
               class="slider-range-price ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"
               data-value-min="<?php echo esc_attr( $cur_min ); ?>"
               data-value-max="<?php echo esc_attr( $cur_max ); ?>"
               data-label-result="Range:">
              <div class="ui-slider-range ui-widget-header ui-corner-all"></div>
              <span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0"></span>
              <span class="ui-slider-handle ui-state-default ui-corner-all" tabindex="0"></span>
          </div>
          <div class="range-price" id="tch-range-label">
            <?php
            echo esc_html( 'Range: ' . $currency_symbol . number_format_i18n( $cur_min, 2 ) . ' - ' . $currency_symbol . number_format_i18n( $cur_max, 2 ) );
            ?>
          </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;margin-top:8px;">
          <input type="hidden" name="min_price" id="tch-min-price" value="<?php echo esc_attr( $cur_min ); ?>" />
          <input type="hidden" name="max_price" id="tch-max-price" value="<?php echo esc_attr( $cur_max ); ?>" />
        </div>
        <?php
        foreach ( $preserve as $k => $v ) {
            if ( is_array( $v ) ) {
                foreach ( $v as $sub ) {
                    printf( '<input type="hidden" name="%s[]" value="%s" />', esc_attr( $k ), esc_attr( wp_unslash( $sub ) ) );
                }
            } else {
                printf( '<input type="hidden" name="%s" value="%s" />', esc_attr( $k ), esc_attr( wp_unslash( $v ) ) );
            }
        }
        ?>
      </form>
    </div>
  </div>
  <div class="widget color mb-50">
    <p class="widget-title2 mb-30">Color</p>
    <div class="widget-desc">
      <?php
      the_widget( 'WC_Widget_Layered_Nav', array( 'title' => '' ), array( 'widget_id' => 'layered-nav-color' ) );
      ?>
    </div>
  </div>
  <div class="widget brands mb-50">
    <p class="widget-title2 mb-30">Brands</p>
    <div class="widget-desc">
      <?php
      $brands = get_terms( array( 'taxonomy' => 'pa_brand', 'hide_empty' => true ) );
      if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
          echo '<ul>';
          foreach ( $brands as $b ) {
              echo '<li><a href="' . esc_url( get_term_link( $b ) ) . '">' . esc_html( $b->name ) . '</a></li>';
          }
          echo '</ul>';
      } else {
          echo '<ul><li><a href="#">Asos</a></li><li><a href="#">Mango</a></li><li><a href="#">River Island</a></li></ul>';
      }
      ?>
    </div>
  </div>

</div>
<?php
$inline_js = <<<JS
jQuery(function($){
  var \$sliderWrap = $('.slider-range .slider-range-price');
  if (!\$sliderWrap.length) return;
  var \$slider = \$sliderWrap.first();
  var min = parseFloat( \$slider.data('min') ) || 0;
  var max = parseFloat( \$slider.data('max') ) || 1000;
  var curMin = parseFloat( \$slider.data('value-min') );
  var curMax = parseFloat( \$slider.data('value-max') );
  if ( isNaN(curMin) ) curMin = min;
  if ( isNaN(curMax) ) curMax = max;
  if (curMin < min) curMin = min;
  if (curMax > max) curMax = max;
  \$slider.slider({
    range: true,
    min: min,
    max: max,
    values: [ Math.round(curMin), Math.round(curMax) ],
    create: function() {
      updateLabel( \$slider.slider('values',0), \$slider.slider('values',1) );
      $('#tch-min-visible').val( Math.round( \$slider.slider('values',0) ) );
      $('#tch-max-visible').val( Math.round( \$slider.slider('values',1) ) );
      $('#tch-min-price').val( Math.round( \$slider.slider('values',0) ) );
      $('#tch-max-price').val( Math.round( \$slider.slider('values',1) ) );
    },
    slide: function( event, ui ) {
      updateLabel( ui.values[0], ui.values[1] );
      $('#tch-min-visible').val( Math.round(ui.values[0]) );
      $('#tch-max-visible').val( Math.round(ui.values[1]) );
      $('#tch-min-price').val( Math.round(ui.values[0]) );
      $('#tch-max-price').val( Math.round(ui.values[1]) );
    },
    stop: function( event, ui ) {
      submitPriceFilter();
    }
  });
  var numberChangeTimer;
  $('#tch-min-visible, #tch-max-visible').on('change input', function(){
    clearTimeout(numberChangeTimer);
    var vmin = parseFloat( $('#tch-min-visible').val() ) || min;
    var vmax = parseFloat( $('#tch-max-visible').val() ) || max;
    if (vmin < min) vmin = min;
    if (vmax > max) vmax = max;
    if (vmin > vmax) {
      var tmp = vmin; vmin = vmax; vmax = tmp;
      $('#tch-min-visible').val( Math.round(vmin) );
      $('#tch-max-visible').val( Math.round(vmax) );
    }
    \$slider.slider('values', [ Math.round(vmin), Math.round(vmax) ]);
    $('#tch-min-price').val( Math.round(vmin) );
    $('#tch-max-price').val( Math.round(vmax) );
    updateLabel( vmin, vmax );
    numberChangeTimer = setTimeout(function(){
      submitPriceFilter();
    }, 700);
  });
  function updateLabel(a,b){
    var unit = \$slider.data('unit') || '';
    function fmt(n){
      n = Math.round(n);
      return n.toString().replace(/\\B(?=(\\d{3})+(?!\\d))/g, ",");
    }
    $('#tch-range-label').text('Range: ' + unit + fmt(a) + ' - ' + unit + fmt(b) );
  }
  function submitPriceFilter(){
    var \$form = $('.tch-price-filter-form').first();
    if ( ! \$form.length ) return;
    \$form.find('input[name=\"paged\"]').remove();
    \$form.submit();
  }

});
JS;
wp_add_inline_script( 'jquery-ui-slider', $inline_js );
