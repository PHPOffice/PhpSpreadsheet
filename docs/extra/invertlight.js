(function(){
    document.documentElement.style.filter = document.documentElement.style.filter ? '' : 'invert(100%) hue-rotate(180deg)';
    const images = document.querySelectorAll('img');
    images.forEach(img => { if (img.alt != 'Logo') img.style.filter = 'invert(100%) hue-rotate(180deg)';});
    if (document.body.getAttribute('data-md-color-scheme') == 'slate') {
        document.body.setAttribute('data-md-color-scheme', 'default');
    } else {
        document.body.setAttribute('data-md-color-scheme', 'slate');
    }
})();
