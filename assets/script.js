(function($){
  function openPanel($root){ $root.addClass('open'); }
  function closePanel($root){ $root.removeClass('open'); }

  // Auto open logic: first page of session, after 10s, and suppress for 1 day thereafter
  function scheduleAutoOpen(){
    var $root = $('#jsi-slidein');
    if(!$root.length) return;

    var now = Date.now();
    var SUPPRESS_KEY = 'JSI_SUPPRESS_UNTIL';
    var SHOWN_SESSION = 'JSI_SHOWN_SESSION';
    var LANDING_PROCESSED = 'JSI_LANDING_PROCESSED';
    var suppressUntil = parseInt(localStorage.getItem(SUPPRESS_KEY) || '0', 10);
    var shownSession = sessionStorage.getItem(SHOWN_SESSION) === '1';
    var landingProcessed = sessionStorage.getItem(LANDING_PROCESSED) === '1';

    if (suppressUntil > now || shownSession) { return; }

    // Only process the first page in the session
    if (!landingProcessed) {
      sessionStorage.setItem(LANDING_PROCESSED, '1');
      setTimeout(function(){
        openPanel($root);
        sessionStorage.setItem(SHOWN_SESSION, '1');
        localStorage.setItem(SUPPRESS_KEY, String(now + 24*60*60*1000)); // suppress for 1 day
      }, 10000);
    }
  }

  $(document).on('click', '#jsi-slidein .jsi-trigger', function(){
    var $root = $('#jsi-slidein');
    if($root.hasClass('open')) closePanel($root); else openPanel($root);
  });
  $(document).on('click', '#jsi-slidein .jsi-close-btn', function(){ closePanel($('#jsi-slidein')); });
  $(document).on('click', '#jsi-slidein .jsi-backdrop', function(){ closePanel($('#jsi-slidein')); });
  $(document).on('keydown', function(e){ if(e.key === 'Escape'){ closePanel($('#jsi-slidein')); } });

  $('#jsi-form').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var data = $form.serializeArray();
    var payload = { action: 'jsi_submit', nonce: JSI.nonce, page_url: window.location.href };
    data.forEach(function(it){ payload[it.name] = it.value; });

    // Include reCAPTCHA token when widget is present
    var hasRecaptcha = (typeof grecaptcha !== 'undefined') && $('.g-recaptcha').length > 0;
    if (hasRecaptcha) {
      var token = grecaptcha.getResponse();
      if (!token) {
        $form.find('.jsi-status').text('Please complete the reCAPTCHA.').css('color', '#d33');
        return;
      }
      payload['g-recaptcha-response'] = token;
    }

    var $status = $form.find('.jsi-status').text('');
    $form.find('.jsi-submit').prop('disabled', true).text('Sending...');

    $.ajax({
      url: JSI.ajaxUrl,
      method: 'POST',
      data: payload,
    }).done(function(resp){
      if(resp && resp.success){
        $status.text(resp.data && resp.data.message ? resp.data.message : JSI.messages.success).css('color', '#2f7');
        $form[0].reset();
        if (hasRecaptcha && typeof grecaptcha !== 'undefined') { try { grecaptcha.reset(); } catch(e){} }
      } else {
        var msg = (resp && resp.data && resp.data.message) ? resp.data.message : JSI.messages.failure;
        $status.text(msg).css('color', '#d33');
      }
    }).fail(function(jqXHR){
      var msg = JSI.messages.failure;
      if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.data && jqXHR.responseJSON.data.message) {
        msg = jqXHR.responseJSON.data.message;
      } else if (jqXHR && jqXHR.responseText) {
        // Nonce failures often return "-1"; show a clearer note
        if (jqXHR.responseText.trim() === '-1') {
          msg = 'Security check failed. Please refresh the page and try again.';
        }
      }
      $status.text(msg).css('color', '#d33');
    }).always(function(){
      $form.find('.jsi-submit').prop('disabled', false).text('Send Now');
    });
  });

  // Initialize auto open logic
  $(function(){ scheduleAutoOpen(); });
})(jQuery);