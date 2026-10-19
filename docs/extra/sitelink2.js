document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll("footer").forEach(function(obj) {
        obj.innerHTML = obj.innerHTML.replace("uilt with", "uilt for <a href='https://github.com/PHPOffice/PhpSpreadsheet'>PhpSpreadsheet</a> with");
    });
});
