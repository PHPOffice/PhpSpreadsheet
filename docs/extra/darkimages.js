/* the following 2 attempts didn't always work
{
    const images = document.querySelectorAll('img');
    images.forEach(img => {img.style.filter = img.style.filter ? '' : 'invert(100%) hue-rotate(180deg)';});
}

{
    const images = document.querySelectorAll('img');
    images.forEach(img => {img.style.filter = img.style.filter ? '' : 'hue-rotate(180deg)';});
}
*/

/* this appears to work all the time */
{
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        const style = getComputedStyle(img);
        const temp = style.filter;
        if (temp == '' || temp == 'none') {
            img.style.filter = 'invert(1) hue-rotate(180deg)';
        } else {
            img.style.filter = 'none';
        }
    });
}
