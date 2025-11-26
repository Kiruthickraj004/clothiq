(function($){
  // expects tchCart.ajax_url and tchCart.nonce to be available
  var ajaxUrl = (typeof tchCart !== 'undefined' && tchCart.ajax_url) ? tchCart.ajax_url : '/wp-admin/admin-ajax.php';
  var nonce   = (typeof tchCart !== 'undefined' && tchCart.nonce) ? tchCart.nonce : '';

  // Open modal: trigger from header button (#essenceCartBtn)
  $(document).on('click', '#essenceCartBtn', function(e){
    e.preventDefault();
    // if modal currently exists, show it; otherwise optionally fetch fragment then show
    if ( $('.cart-bg-overlay').length && $('.right-side-cart-area').length ) {
      $('.cart-bg-overlay').fadeIn(200);
      $('.right-side-cart-area').addClass('active').fadeIn(200);
    } else {
      // fallback: fetch fragment and insert, then show
      $.post(ajaxUrl, { action: 'tch_get_cart_fragment', nonce: nonce }, function(resp){
        if ( resp && resp.success && resp.data.html ) {
          $('body').append(resp.data.html);
          $('.cart-bg-overlay').fadeIn(200);
          $('.right-side-cart-area').addClass('active').fadeIn(200);
          // refresh header count (if provided)
          if ( typeof resp.data.count !== 'undefined' ) {
            $('.cart-count').text(resp.data.count);
          }
        }
      });
    }
  });

  // Close modal by overlay or close button
  $(document).on('click', '.cart-bg-overlay, #rightSideCartClose', function(e){
    e.preventDefault();
    $('.right-side-cart-area').removeClass('active').fadeOut(150);
    $('.cart-bg-overlay').fadeOut(150);
  });

  // Remove item (click on the small close icon)
  $(document).on('click', '.tch-remove-cart-item', function(e){
    e.preventDefault();
    var $el = $(this);
    var key = $el.data('cart-key');
    if (!key) return;
    $el.prop('disabled', true);

    $.post(ajaxUrl, { action: 'tch_remove_cart_item', cart_key: key, nonce: nonce }, function(resp){
      if ( resp && resp.success ) {
        // replace the modal HTML (keeps design identical)
        if ( resp.data.html ) {
          // replace existing modal
          var $existing = $('.right-side-cart-area');
          if ( $existing.length ) {
            $existing.replaceWith(resp.data.html);
          } else {
            $('body').append(resp.data.html);
          }
        }
        // update header count
        if ( typeof resp.data.count !== 'undefined' ) {
          $('.cart-count').text(resp.data.count);
        }
      } else {
        alert( (resp && resp.data && resp.data.message) ? resp.data.message : 'Could not remove item' );
      }
    }).fail(function(){
      alert('Error');
    });
  });

  // Provide a global function to refresh fragment (useful after add-to-cart)
  window.tchRefreshCartFragment = function(){
    $.post(ajaxUrl, { action: 'tch_get_cart_fragment', nonce: nonce }, function(resp){
      if ( resp && resp.success ) {
        if ( resp.data.html ) {
          var $existing = $('.right-side-cart-area');
          if ( $existing.length ) {
            $existing.replaceWith(resp.data.html);
          } else {
            $('body').append(resp.data.html);
          }
        }
        if ( typeof resp.data.count !== 'undefined' ) {
          $('.cart-count').text(resp.data.count);
        }
      }
    });
  };

})(jQuery);
