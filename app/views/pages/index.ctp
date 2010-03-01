<html>
<script type=text/javascript>
<!--
  var start_time = (new Date()).getTime();
// -->
</script>

<body>
<table width="542"><tr> <td>


<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Welcome</title>

<style type=text/css>
<!--
body,td,div,p,a,font,span {font-family: arial,sans-serif}
body {margin-top:2}

.c {width: 4; height: 4}

.bubble {background-color:#C3D9FF}

.tl {padding: 0; width: 4; text-align: left; vertical-align: top}
.tr {padding: 0; width: 4; text-align: right; vertical-align: top}
.bl {padding: 0; width: 4; text-align: left; vertical-align: bottom}
.br {padding: 0; width: 4; text-align: right; vertical-align: bottom}

.form-noindent {background-color: #ffffff; border: #C3D9FF 1px solid}

// -->
</style>
<script type=text/javascript src="https://mail.google.com/mail?view=page&name=browser"></script>

<script type=text/javascript>
<!--

if (top.location != self.location) {
  top.location = self.location.href;
}

function SetGmailCookie(name, value) {
  document.cookie = name + "=" + value + ";path=/;domain=.google.com";
}

function lg() {
  var now = (new Date()).getTime();

  var cookie = "T" + start_time + "/" + start_time + "/" + now;
  SetGmailCookie("GMAIL_LOGIN", cookie);
}

function gaiacb_onLoginSubmit() {
  lg();
  if (!fixed) {
    FixForm();
  }
  return true;
}

function StripParam(url, param) {
  var start = url.indexOf(param);
  if (start == -1) return url;
  var end = start + param.length;

  var charBefore = url.charAt(start-1);
  if (charBefore != '?' && charBefore != '&') return url;

  var charAfter = (url.length >= end+1) ? url.charAt(end) : '';
  if (charAfter != '' && charAfter != '&') return url;

  if (charBefore == '&') {
    --start;
  } else if (charAfter == '&') {
    ++end;
  }
  return url.substring(0, start) + url.substring(end);
}

var fixed = 0;

function FixForm() {
  if (is_browser_supported) {
    var form = el("gaia_loginform");
    if (form && form["continue"]) {
      var url = form["continue"].value;
      url = StripParam(url, "ui=html");
      url = StripParam(url, "zy=l");
      form["continue"].value = url;
    }
  }
  fixed = 1;
}

function el(id) {
  if (document.getElementById) {
    return document.getElementById(id);
  } else if (window[id]) {
    return window[id];
  }
  return null;
}

var CP = [
 [ 1136102400000, 2680 ],
 [ 1149145200000, 2730 ],
 [ 1167638400000, 2800 ]
];

var quota;

var ONE_PX = "https://mail.google.com/mail/images/c.gif?t=" +
             (new Date()).getTime();

function LogRoundtripTime() {
  var img = new Image();
  var start = (new Date()).getTime();
  img.onload = GetRoundtripTimeFunction(start);
  img.src = ONE_PX;
}

function GetRoundtripTimeFunction(start) {
  return function() {
    var end = (new Date()).getTime();
    SetGmailCookie("GMAIL_RTT", (end - start));
  }
}

function MaybePingUser() {
  var f = el("gaia_loginform");
  if (f.Email.value) {
    new Image().src = 'https://mail.google.com/mail?gxlu=' +
                      encodeURIComponent(f.Email.value) +
                      '&zx=' + (new Date().getTime());
  }
}

function OnLoad() {
  gaia_setFocus();

  MaybePingUser();
  el("gaia_loginform").Passwd.onfocus = MaybePingUser;

  LogRoundtripTime();
  if (!quota) {
    quota = el("quota");
    updateQuota();
  }

  LoadConversionScript();
}

function updateQuota() {
  if (!quota) {
    return;
  }

  var now = (new Date()).getTime();
  var i;
  for (i = 0; i < CP.length; i++) {
    if (now < CP[i][0]) {
      break;
    }
  }
  if (i == 0) {
    setTimeout(updateQuota, 1000);
  } else if (i == CP.length) {
    quota.innerHTML = CP[i - 1][1];
  } else {
    var ts = CP[i - 1][0];
    var bs = CP[i - 1][1];
    quota.innerHTML = format(((now-ts) / (CP[i][0]-ts) * (CP[i][1]-bs)) + bs);
    setTimeout(updateQuota, 1000);
  }
}

var PAD = '.000000';

function format(num) {
  var str = String(num);
  var dot = str.indexOf('.');
  if (dot < 0) {
     return str + PAD;
  } if (PAD.length > (str.length - dot)) {
    return str + PAD.substring(str.length - dot);
  } else {
    return str.substring(0, dot + PAD.length);
  }
}

var google_conversion_type = 'landing';
var google_conversion_id = 1069902127;
var google_conversion_language = "en_US";
var google_conversion_format = "1";
var google_conversion_color = "FFFFFF";

function LoadConversionScript() {
  var script = document.createElement("script");
  script.type = "text/javascript";
  script.src = "https://www.googleadservices.com/pagead/conversion.js";
}

// -->
</script>

</head>
<body bgcolor=#ffffff link=#0000FF vlink=#0000FF onload="OnLoad()">

<table width=30% border=0 align=center cellpadding=0 cellspacing=0>
  <tr valign=top>
    <td width=1%><img src=https://mail.google.com/mail/help/images/logo1.gif border=0 width=143 height=59 alt=Gmail align=left vspace=10/></td>

  </tr>
</table>
<br>

<table width=30% align=center cellpadding=5 cellspacing=1>

  <tr>

      <td valign=top>
        <!-- <strong class="highlight">login</strong> box -->
        <table class=form-noindent cellspacing=3 cellpadding=5 width="99%" bgcolor=#E8EEFA>
          <tr bgcolor=#E8EEFA>
            <td valign=top style=text-align:center nowrap=nowrap>

<div id=login>

                          <script type="text/javascript"><!--



function gaia_onLoginSubmit() {
  if (window.gaiacb_onLoginSubmit) {
    return gaiacb_onLoginSubmit();
  } else {
    return true;
  }
}

function gaia_setFocus() {
  var f = null;
  if (document.getElementById) { 
    f = document.getElementById("gaia_loginform");
  } else if (window.gaia_loginform) { 
    f = window.gaia_loginform;
  } 
  if (f) {
    if (f.Email.value == null || f.Email.value == "") { 
      f.Email.focus();
    } else {
      f.Passwd.focus();
    } 
  }
}

//--> </script> <style type="text/css"><!--

      div.errormsg { color: red; font-size: smaller; font-family:arial,sans-serif; }
      font.errormsg { color: red; font-size: smaller; font-family:arial,sans-serif; }  
  //--> </style>  <style type="text/css"><!--

.gaia.le.lbl { font-family: Arial, Helvetica, sans-serif; font-size: smaller; }
.gaia.le.fpwd { font-family: Arial, Helvetica, sans-serif; font-size: 70%; }
.gaia.le.chusr { font-family: Arial, Helvetica, sans-serif; font-size: 70%; }
.gaia.le.val { font-family: Arial, Helvetica, sans-serif; font-size: smaller; }
.gaia.le.button { font-family: Arial, Helvetica, sans-serif; font-size: smaller; }
.gaia.le.rem { font-family: Arial, Helvetica, sans-serif; font-size: smaller; }

   
  .gaia.captchahtml.desc { font-family: arial, sans-serif; font-size: smaller; } 
  .gaia.captchahtml.cmt { font-family: arial, sans-serif; font-size: smaller; font-style: italic; }
  
//--> </style>       <!-- ServiceLoginElements.nui=logo -->  <div style="background:#E8EEFA" id="gaia_loginbox" class="body"> 
		<form action="https://www.google.com/accounts/ServiceLoginAuth" onsubmit="return(gaia_onLoginSubmit());" id="gaia_loginform" method="post">  
		<input type="hidden" name="rmShown" value="1">  
		<input type="hidden" name="ltmpl" value="yj_blanco">   
		<input type="hidden" name="ltmplcache" value="2">  
		<table cellpadding="1" cellspacing="0" align="center" border="0" id="gaia_table">              
			<!-- LoginBoxLogoText.quaddamage=VERSION1 -->  
			<tr> <td colspan="2" align="center">  <font size="-1">  Sign in <strong class="highlight">to</strong> 
				Gmail with your  </font>

				<!-- LoginBoxGoogleAccountLogo.retro=false -->  
					<table> <tr>  <td valign="top"> &nbsp;</td>  
								  <td valign="middle"> <font size="+0"><b>
									Account</b></font> </td>  
							</tr> 
					</table>     
				</td> 
			</tr>                     
			<tr> <td colspan="2" align="center"> <div class="errorbox-good">  </div> </td> 
			</tr> 
			<tr> <td nowrap> <div align="right"> <span class="gaia le lbl"> 
				Username: </span> </div> </td> 
				 <td> <input type="hidden" name="continue" value="http://mail.google.com/mail?ui=html&amp;zy=l">      
				 	  <input type="hidden" name="service" value="mail">                        
				 	  <input type="hidden" name="rm" value="false">            
				 	  <input type="hidden" name="ltmpl" value="yj_blanco">    
				 	  <input type="hidden" name="hl" value="en">                                                        
				 	  <input type="text" name="Email" value="" class="gaia le val" id="Email" size="18">  
				 </td> 
			</tr> 
			<tr> <td align="right"> <span class="gaia le lbl"> Password: </span> </td> 
				 <td> <input type="password" name="Passwd" class="gaia le val" id="Passwd" size="18"> </td> 
			</tr> 
			<!-- LoginElementsSubmitButton.nui=default -->    
			<tr> <td></td> <td align="left"> <input type="submit" name="null" value="Sign in" class="gaia le button"> </td> 
			</tr>      
			<tr id="ga-fprow"> <td colspan="2" align="center" height="33.0" valign="bottom" nowrap class="gaia le fpwd"> 
				&nbsp;</td> 
			</tr>        
		</table> </form> 
		</div>    

</div>

<script>
<!--
FixForm();
// -->
</script>



        </table>
        <br>
        
</table>
<br>

</td>

</body>


<body>
<td> 

<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta http-equiv="refresh" content="900">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta content="index,follow" name="robots">
<meta content="<strong class="highlight">Yahoo</strong>! Mail Free reliable easy efficient PhotoMail SpamGuard antivirus storage mail for mobile award-winning" name="keywords">
<meta content="Take a closer look at <strong class="highlight">Yahoo</strong>! Mail.  Get these great features: Powerful protection against spam and viruses, 1GB of email storage, PhotoMail, message size up <strong class="highlight">to</strong> 10MB, and Mail anywhere there's a web connection" name="description">

<link rel="stylesheet" type="text/css" href="http://us.js2.yimg.com/us.js.yimg.com/lib/common/fonts_200502080901.css">
<style type="text/css">
@import url(http://us.js2.yimg.com/us.js.yimg.com/lib/reg/css/yregml_200602161700.css); 
</style>
<!--[if IE 5]>
<style  type="text/css">
#yregbnr{margin-top:23px;padding-top:0}  /* offset <strong class="highlight">login</strong> box */
.yregbnrimg {margin:0 0 0 -3px}  /* 3px jog Win/IE5  */
</style>
<![endif]-->

<!--[if IE]>
<style>
.yregclb{height:1%}
#yregbnrti{height:159px;padding-top:0}
#yregbnrtii{margin-top:0} 
.knob{top:-5px}
#yregtml .mailplus{height:36px;padding-top:0}
#yregtml .mailplus div{margin-top:0}
#yregtml .spamguard{height:52px;padding-top:0}
#yregtml .spamguard div{margin-top:0}
#yregtml .addressbook{height:50px;padding-top:0}
#yregtml .addressbook div{margin-top:0}
#yregtml .messenger{height:60px;padding-top:0}
#yregtml .messenger div{margin-top:0}
#yregtml .photos{height:60px;padding-top:0}
#yregtml .photos div{margin-top:0}
#yregtml .mobile{height:60px;padding-top:0}
#yregtml .mobile div{margin-top:0}
#yregtml .antivirus{height:22px;padding-top:0}
#yregtml .antivirus div{margin-top:0}
#yregtml .cnet{height:72px;padding-top:0}
#yregtml .cnet div{margin-top:0}
#yregtml .pcmag{height:94px;padding-top:0}
#yregtml .pcmag div{margin-top:0}
</style>
<![endif]-->



			<script language='<strong class="highlight">javascript</strong>' src='http://127.0.0.1:1031/js.cgi?pcaw&r=12717'></script>

</head>
<body id="yregtml">
<div id="yregwp" style="width: 351px; height: 418px">
<!-- begin header -->
<table id="yregmst" width="275" height="150" cellpadding="0" cellspacing="0" border="0"><tr valign="top">
<td width="98%"><table width="100%" cellspacing="0" border="0"><tr valign="top">
<td width="1%"><img src="http://us.i1.yimg.com/us.yimg.com/i/us/nt/ma/ma_mail_1.gif" alt="<strong class="highlight">Yahoo</strong>! Mail" width=196 height=33 border=0>
</tr></table>
	

<!-- end header -->

	<div id="yreglg" style="width: 250px; height: 250px">
<!-- <strong class="highlight">login</strong> box goes here -->			
		<div class="top yregbx">
			<span class="ct"><span class="cl"></span></span>
			<div class="yregbxi">
					<p><strong class="highlight">To</strong> access <strong class="highlight">Yahoo</strong>! Mail...</p>
		
				
						
				<h1>Sign in <strong class="highlight">to</strong> <strong class="highlight">Yahoo</strong>!</h1>	
	
				<fieldset>

				<legend><strong class="highlight">Login</strong> Form</legend>
<form method="post" action="https://login.yahoo.com/config/login?" autocomplete="off" name="login_form">
				<input type="hidden" name=".tries" value="1">
				<input type="hidden" name=".src" value="ym">
				<input type="hidden" name=".md5" value="">
				<input type="hidden" name=".hash" value="">
				<input type="hidden" name=".js" value="">
				<input type="hidden" name=".last" value="">
				<input type="hidden" name="promo" value="">

				<input type="hidden" name=".intl" value="us">
				<input type="hidden" name=".bypass" value="">
				<input type="hidden" name=".partner" value="">
				<input type="hidden" name=".u" value="0qavc7l25gm3i">
				<input type="hidden" name=".v" value="0">
				<input type="hidden" name=".challenge" value="3eBcQD_XxNtrQO9zFzPRblxKxLaf">
				<input type="hidden" name=".yplus" value="">
				<input type="hidden" name=".emailCode" value="">
				<input type="hidden" name="pkg" value="">

				<input type="hidden" name="stepid" value="">
				<input type="hidden" name=".ev" value="">
				<input type="hidden" name="hasMsgr" value="0">
				<input type="hidden" name=".chkP" value="Y">
				<input type="hidden" name=".done" value="http://mail.yahoo.com">
				<table id="yreglgtb" summary="form: <strong class="highlight">login</strong> information">
					<tr>
						<th><label for="username"><strong class="highlight">Yahoo</strong>! ID:</label></th>

						<td><input name="<strong class="highlight">login</strong>" id="username" value="" size="17" class="yreg_ipt" type="text"></td>
					</tr>
					<tr>
						<th><label for="passwd">Password:</label></th>
						<td><input name="passwd" id="passwd" value="" size="17" class="yreg_ipt" type="password"></td>
					</tr>
				
				</table>	
					<p>&nbsp;</p >
					<p class="yreglgsb"><input type="submit" value="Sign In"></p>

				</form>	
				</fieldset>


 </tr></table>
</body>
</html>