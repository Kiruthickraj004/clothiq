document.querySelectorAll('.thumb-img').forEach(img => {
    img.addEventListener('click', function(){
        document.getElementById('mainProductImage').src = this.dataset.full;
    });
});
