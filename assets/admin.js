(function($){
  $(function(){
    $('.jsi-color').wpColorPicker();

    var $input = $('#jsi_icon_class');
    var $preview = $('#jsi-icon-preview');

    function updateIconPreview() {
      if (!$input.length || !$preview.length) {
        return;
      }

      var baseClass = 'jsi-icon-preview fa-fw ';
      var value = ($input.val() || '').trim();

      if (!value) {
        value = 'fa-solid fa-envelope-open-text';
      }

      $preview.attr('class', baseClass + value);
    }

    $input.on('input change', updateIconPreview);
    updateIconPreview();
  });
})(jQuery);