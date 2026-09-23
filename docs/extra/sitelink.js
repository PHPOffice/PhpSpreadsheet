document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll("li.wy-breadcrumbs-aside").forEach(function(obj) {
    obj.innerHTML = "<a href='https://github.com/PHPOffice/phpspreadsheet'>Visit PhpSpreadsheet</a>" + obj.innerHTML;
  });
});
