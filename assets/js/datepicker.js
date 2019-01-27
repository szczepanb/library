module.exports = function() {
  $(function()
  {
    console.log(1);
      $('.js-datepicker').datepicker({
          format: 'yyyy-mm-dd'
      });
  });
};