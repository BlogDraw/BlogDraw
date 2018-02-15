<div id="CookieMonster" style="background:#FFFFFF;width:100%;bottom:0;position:fixed;padding:0.5rem;">
	<p id="Cookies" style="text-align:right;">By using this site, you agree to <button class="btn btn-default" id="ShowPolicy">our cookie policy</button>&nbsp;<button class="btn btn-danger" id="Exit">&times;</button>&nbsp;&nbsp;</p>  
</div>
<script>
	var loc = window.location.host;
	var button = document.getElementById('Exit');
	var button1 = document.getElementById('ShowPolicy');
	var div = document.getElementById('CookieMonster');
	
	if (document.cookie.indexOf(loc + "Cookies=") >= 0)
	{
		div.style.display = 'none';
	}
	else
	{
		document.cookie = loc + "Cookies=On; expires=" + new Date(Date.now() + 10*24*60*60*1000) + "; path=/";
	}
	
	button1.onclick = function() 
	{
		div.style.display = 'none';
		div.parentNode.innerHTML += '<p><?php echo COOKIENOTICE; ?></p>';
	};
	
	button.onclick = function() 
	{
		if (div.style.display !== 'none') 
		{
			document.getElementById('Cookies').InnerHtml = 'Thanks!';
			div.style.display = 'none';
		}
		else 
		{
			div.style.display = 'block';
		}
	};
</script>
