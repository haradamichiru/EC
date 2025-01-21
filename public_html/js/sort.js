document.addEventListener('DOMContentLoaded', function () {
  if (document.querySelector('.sort')) {
    const sorts = document.querySelectorAll('.sort');

    document.querySelectorAll('.sort').forEach (element => {
      element.ondragstart = function () {
        event.dataTransfer.setData('text/plain', event.target.id);
      };
      
      element.ondragover = function () {
        event.preventDefault();
        this.style.borderTop = '3px solid';
      };
    
    element.ondragleave = function () {
    this.style.borderTop = "";
    };
    
      element.ondrop = function () {
        event.preventDefault();
        // console.log(this.parentNode.parentNode);
        let id = event.dataTransfer.getData('text');
        let element_drag = document.getElementById(id);
        this.parentNode.insertBefore(element_drag, this);
        this.style.borderTop = '';
      };
    });
  }
});


