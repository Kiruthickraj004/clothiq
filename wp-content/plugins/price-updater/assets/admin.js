jQuery(document).ready(function ($) {

    $('#bpu_parent_cat').on('change', function () {

        const parentId = $(this).val();
        const $child   = $('#bpu_child_cat');

        $child.html('<option value="">Loading...</option>');

        if (!parentId) {
            $child.html('<option value="">Select child</option>');
            return;
        }

        $.post(bpuData.ajaxUrl, {
            action: 'bpu_get_child_categories',
            parent_id: parentId,
            nonce: bpuData.nonce
        }, function (response) {
            $child.html('<option value="">Select child</option>' + response);
        });
    });
});
