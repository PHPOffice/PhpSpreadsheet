(function(){
    document.documentElement.style.filter = document.documentElement.style.filter ? '' : 'invert(100%) hue-rotate(180deg)';
    const images = document.querySelectorAll('img');
    images.forEach(img => {  img.style.filter = 'invert(100%) hue-rotate(180deg)';});
})();
