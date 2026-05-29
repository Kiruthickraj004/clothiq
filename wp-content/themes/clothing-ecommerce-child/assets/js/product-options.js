document.addEventListener('DOMContentLoaded', function() {
    const sizeInput = document.querySelector('.size-select');
    const sizeValue = document.querySelector('.size-value');
    const sizeOptions = document.querySelectorAll('.size-option');

    sizeOptions.forEach(function(button) {
        button.addEventListener('click', function() {
            const size = this.getAttribute('data-size');
            const label = this.getAttribute('data-size-label');

            sizeOptions.forEach(function(opt) {
                opt.classList.remove('is-active');
                opt.setAttribute('aria-pressed', 'false');
            });

            this.classList.add('is-active');
            this.setAttribute('aria-pressed', 'true');

            if (sizeInput) {
                sizeInput.value = size;
            }
            if (sizeValue && label) {
                sizeValue.textContent = label;
            }
        });
    });
});
