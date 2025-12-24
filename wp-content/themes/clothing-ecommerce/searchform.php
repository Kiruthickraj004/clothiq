<?php
/**
 * Custom search form (input only)
 */
?>
<form role="search"
      method="get"
      class="search-form"
      action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input type="search"
           class="search-field"
           placeholder="<?php esc_attr_e( 'Search products…', 'clothing-ecommerce' ); ?>"
           value="<?php echo get_search_query(); ?>"
           name="s" />
</form>
