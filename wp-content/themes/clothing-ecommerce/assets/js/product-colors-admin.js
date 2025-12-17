jQuery(function ($) {

    $('#add-custom-color').on('click', function () {
        const index = $('#custom-colors-wrapper .custom-color-row').length;

        $('#custom-colors-wrapper').append(`
            <div class="custom-color-row">
                <input type="text"
                       name="_custom_colors[${index}][name]"
                       placeholder="Color name" />

                <input type="color"
                       name="_custom_colors[${index}][hex]"
                       value="#000000" />

                <button type="button"
                        class="button remove-color">×</button>
            </div>
        `);
    });

    $(document).on('click', '.remove-color', function () {
        $(this).closest('.custom-color-row').remove();
    });

});
