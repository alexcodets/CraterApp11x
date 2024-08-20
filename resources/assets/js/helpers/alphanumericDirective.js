export default {
    bind: function(el) {
      el.addEventListener('input', function(e) {
        let input = e.target.value;
        let regex = /^\s*[a-zA-Z0-9]+\s*$/;
        if (!regex.test(input)) {
          e.target.value = input.replace(/[^a-zA-Z0-9\s]/g, '');
        }
      });
    }
  };