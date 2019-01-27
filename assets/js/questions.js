module.exports = function(id) {
  $(function()
  {
    $('.modal').each(function()
    {
      $(this).on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        
        var modal = $(this)
        modal.find('.modal-yes-btn').off('click.remove').on('click.remove', function()
        {
          window.location.href = button.data('href');
        });
      })
    });
  });
};