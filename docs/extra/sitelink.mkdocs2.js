document.addEventListener("DOMContentLoaded", function() {
  var done = false;
  document.querySelectorAll("#toc-collapse ul").forEach(function(obj) {
    const x = obj.innerHTML;
    if (!done) {
        obj.innerHTML = "<li class='nav-item'><a class='nav-link' href='https://github.com/PHPOffice/phpspreadsheet'>Visit PhpSpreadsheet</a></li>" + x;
        done = true;
    }
  });
});
