<div id="OverlayNotice" class="modal">
  <div class="modal-content">
    <div class="modal-body">
      <p><?php echo COOKIENOTICE; ?></p>
      <button class="btn btn-danger" id="HideOverlayNotice">&times;</button>
    </div>
  </div>
</div>
<div id="CookieMonster" class="container-fluid fixed-bottom bg-light text-center py-3">
  <div class="text-right">
    <button class="btn btn-success btn-sm" id="Exit">I accept!</button>
  </div>
  <p id="Cookies">By using this site, you agree to</p>
  <button class="btn btn-dark" id="ShowPolicy">our cookie policy</button>
</div>


<script>
jQuery("#HideOverlayNotice").click(function()
{
  jQuery('#OverlayNotice').modal('hide')
});
jQuery(document).ready(function()
{
  var loc = window.location.host;

  if (document.cookie.indexOf(loc + "Cookies=") >= 0)
    jQuery('#CookieMonster').hide();
  else
  {
    jQuery("#ShowPolicy").click(function()
    {
      jQuery('#OverlayNotice').modal('show');
    });

    jQuery("#Exit").click(function()
    {
      jQuery('#CookieMonster').hide();
      document.cookie = loc + "Cookies=On; expires=" + new Date(Date.now() + 10*24*60*60*1000) + "; path=/";
    });
  }
});
</script>