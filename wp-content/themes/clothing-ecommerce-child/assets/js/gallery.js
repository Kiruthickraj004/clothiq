document.addEventListener('DOMContentLoaded', function() {
    const gallerySlides = document.querySelectorAll('.gallery-slide');
    const thumbnails = document.querySelectorAll('.thumbnail');
    const prevBtn = document.querySelector('.gallery-nav.prev-slide');
    const nextBtn = document.querySelector('.gallery-nav.next-slide');

    if (!gallerySlides.length) return;

    let currentIndex = 0;

    function showSlide(index) {
        if (index >= gallerySlides.length) {
            currentIndex = 0;
        } else if (index < 0) {
            currentIndex = gallerySlides.length - 1;
        } else {
            currentIndex = index;
        }

        gallerySlides.forEach((slide, idx) => {
            slide.classList.toggle('active', idx === currentIndex);
        });

        thumbnails.forEach((thumb, idx) => {
            thumb.classList.toggle('active', idx === currentIndex);
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            showSlide(currentIndex - 1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            showSlide(currentIndex + 1);
        });
    }

    thumbnails.forEach((thumbnail, idx) => {
        thumbnail.addEventListener('click', () => {
            showSlide(idx);
        });
    });
});
