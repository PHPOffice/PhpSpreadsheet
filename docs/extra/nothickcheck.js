document.addEventListener("DOMContentLoaded", function() {
    const div = document.getElementsByClassName('col-md-9');
    if (div.length == 1) {
        div0 = div[0];
        const element = document.getElementById('features-cross-reference');
        if (element) {
            div0.innerHTML = div0.innerHTML
              .replaceAll("\u{2714}", "\u{2713}")
              .replaceAll("\u{2716}", "\u{2715}");
        }
    }
});
