document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('form');
  const creditRadio = document.querySelector('input[value="credit"]');
  const creditContents = document.getElementById('creditInfo');
  const payRadios = document.querySelectorAll('input[name="pay"]');

  var radio = document.getElementsByName('pay');

  if (radio[0].checked == false) {
    creditContents.style.display = "none";
  }
  for (var i = 0; i < radio.length; i++) {
    radio[i].onchange = function() {
      if (radio[0].checked) {
        creditContents.style.display = "block";
      } else {
      creditContents.style.display = "none";
      }
    }
  };

});
