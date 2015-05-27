<?php
include "control.php";
include "../header.php";
?>
<!-- tinyMCE -->
<script language="javascript" type="text/javascript" src="../jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
theme : "advanced",
mode : "textareas",
//save_callback : "customSave",
content_css : "../jscripts/tiny_mce/advanced.css",
extended_valid_elements : "a[href|target|name],font[face|size|color|style],span[class|align|style]",
theme_advanced_toolbar_location : "top",
plugins : "table",
theme_advanced_buttons3_add_before : "tablecontrols,separator",
//invalid_elements : "a",
relative_urls : false,
theme_advanced_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1", // Theme specific setting CSS classes
debug : false,
verify_html : false
});
</script>
<!-- /tinyMCE --> 	
<script type="text/javascript">
function changeHiddenInput(objDropDown)
{
	var solodata=objDropDown.value.split("||");
	var soloid=solodata[0];
	if (soloid)
	{
		var solourl=solodata[1];
		var solosubject=solodata[2];
		var soloadbody=solodata[3];
		var objdeleteid = document.getElementById("deleteid");
		var objsaveid = document.getElementById("saveid");
		var objsaveurl = document.getElementById("url");
		var objsavesubject = document.getElementById("subject");
		objdeleteid.value = soloid;
		objsaveid.value = soloid;
		objsaveurl.value = solourl; 
		objsavesubject.value = solosubject;
		document.getElementById('save').checked = true;	
		tinyMCE.getInstanceById('mce_editor_0').getBody().innerHTML=' ';
		tinyMCE.execCommand('mceInsertContent',false,soloadbody);
	}
	else
	{
		var objdeleteid = document.getElementById("deleteid");
		var objsaveid = document.getElementById("saveid");
		var objsaveurl = document.getElementById("url");
		var objsavesubject = document.getElementById("subject");
		objdeleteid.value = "";
		objsaveid.value = "";
		objsaveurl.value = ""; 
		objsavesubject.value = "";
		document.getElementById('save').checked = false;
		tinyMCE.getInstanceById('mce_editor_0').getBody().innerHTML=' ';
	}
}
</script> 
<?php
$action = $_POST["action"];
$error = "";
$show = "";
##############################################
if ($action == "send")
{
$adbody = $_POST["adbody"];
$subject = $_POST["subject"];
$url = $_POST["url"];
$saveid = $_POST["saveid"];
	if(!$subject)
	{
	$error .= "<div>No subject was entered.</div>";
	}
	if(!$adbody)
	{
	$error .= "<div>No message body was entered.</div>";
	}
if(!$error == "")
{
?>
<table cellpadding="4" cellspacing="4" border="0" align="center" width="80%">
<tr><td align="center" colspan="2"><div class="heading">Error</div></td></tr>
<tr><td align="center" colspan="2"><br><?php echo $error ?></td></tr>
<tr><td colspan="2" align="center"><br><a href="contactmembers.php">RETURN</a></td></tr>
</table>
<br><br>
<?php
include "../footer.php";
exit;
}

if(!$url)
{
$url = $domain;
}

$adbody = stripslashes($adbody);
$adbody = str_replace('\\', '', $adbody); 
$subject = stripslashes($subject);
$subject = str_replace('\\', '', $subject); 
$adbody = mysql_real_escape_string($adbody);
$subject = mysql_real_escape_string($subject);

$q = "insert into adminemails (subject,adbody,url) values ('$subject','".$adbody."','$url')";
$r = mysql_query($q) or die(mysql_error());

		if($save)
		{
			if ($saveid != "")
			{
				$saveq = "select * from adminemail_saved where id='$saveid'";
				$saver = mysql_query($saveq);
				$saverows = mysql_num_rows($saver);
				if ($saverows < 1)
				{
				mysql_query("insert into adminemail_saved (subject,adbody,url) values('$subject','$adbody','$url')");
				}
				if ($saverows > 0)
				{
				mysql_query("update adminemail_saved set subject='$subject',adbody='$adbody',url='$url' where id='$saveid'");
				}
			}
			if ($saveid == "")
			{
			mysql_query("insert into adminemail_saved (subject,adbody,url) values('$subject','$adbody','$url')");
			}
		} # if($save)
?>
<table cellpadding="4" cellspacing="4" border="0" align="center" width="80%">
<tr><td align="center" colspan="2"><div class="heading">Your Admin Email Was Sent!</div></td></tr>
<tr><td colspan="2" align="center"><br><a href="contactmembers.php">RETURN</a></td></tr>
</table>
<br><br>
<?php
include "../footer.php";
exit;
} # if ($action == "send")
##############################################
if ($action == "delete")
{
$delq = "delete from adminemail_saved where id='".$_POST['deleteid']."'";
$delr = mysql_query($delq);
?>
<table cellpadding="4" cellspacing="4" border="0" align="center" width="80%">
<tr><td align="center" colspan="2"><div class="heading">The Saved Email Was Deleted</div></td></tr>
<tr><td colspan="2" align="center"><br><a href="contactmembers.php">RETURN</a></td></tr>
</table>
<br><br>
<?php
include "../footer.php";
exit;
}
########################################################################## SABRINA MARKON 2012 PearlsOfWealth.com
?>
<table cellpadding="4" cellspacing="4" border="0" align="center" width="100%">
<tr><td align="center" colspan="2"><div class="heading">Send&nbsp;An&nbsp;Admin&nbsp;Email</div></td></tr>
<?php
$savedq = "select * from adminemail_saved";
$savedr = mysql_query($savedq);
$savedrows = mysql_num_rows($savedr);
if ($savedrows > 0)
{
?>
<tr><td align="center" colspan="2"><br>
<table width="600" cellpadding="4" cellspacing="2" border="0" align="center" class="sabrinatable">
<tr class="sabrinatrdark"><td align="center" colspan="2">YOUR SAVED EMAILS</td></tr>
<tr class="sabrinatrlight"><td align="center" colspan="2">Select an email from the ones you've saved below, or enter a new one.</td></tr>
<form action="contactmembers.php" method="post">
<tr class="sabrinatrdark"><td colspan="2" align="center"><select name="solosavedchoice" id="solosavedchoice" onchange="changeHiddenInput(this)">
<option value=""> - Select Saved Ad - </option>
<?php
while ($savedrowz = mysql_fetch_array($savedr))
	{
	$savedsubject = $savedrowz["subject"];
	$savedsubject = stripslashes($savedsubject);
	$savedsubject = str_replace('\\', '', $savedsubject); 
	$savedadbody = $savedrowz["adbody"];
	$savedadbody = stripslashes($savedadbody);
	$savedadbody = str_replace('\\', '', $savedadbody);
	$savedadbody = htmlentities($savedadbody, ENT_COMPAT, "ISO-8859-1");
	$savedurl = $savedrowz["url"];
	$savedid = $savedrowz["id"];
?>
<option value="<?php echo $savedid ?>||<?php echo $savedurl ?>||<?php echo $savedsubject ?>||<?php echo $savedadbody ?>"><?php echo $savedsubject ?></option>
<?php
	}
?>
</select>&nbsp;&nbsp;<input type="hidden" name="deleteid" id="deleteid" value=""><input type="hidden" name="action" value="delete"><input type="submit" value="Delete Saved"></td></tr></form>
</table>
</td></tr>
<?php		
} # if ($savedrows > 0)

?>
<tr><td align="center" colspan="2"><br>
<table cellpadding="4" cellspacing="2" border="0" align="center" class="sabrinatable">
<tr class="sabrinatrdark"><td align="center" colspan="2">SEND ADMIN EMAIL</td></tr>
<tr class="sabrinatrlight"><td align="center" colspan="2">Please use the personalization substitution below anywhere in your subject or message, typed EXACTLY as shown (cAsE sEnSiTiVe):</td></tr>
<tr class="sabrinatrdark"><td align="center" colspan="2">
<table cellpadding="2" cellspacing="2" border="0" align="center" class="sabrinatable" width="100%">
<tr class="sabrinatrlight"><td align="center">Type</td><td align="center">Substitutes</td></tr>
<tr class="sabrinatrdark"><td align="center">~USERID~</td><td align="center">Member's UserID</td></tr>
<tr class="sabrinatrdark"><td align="center">~FULLNAME~</td><td align="center">Member's First and Last Name</td></tr>
<tr class="sabrinatrdark"><td align="center">~FIRSTNAME~</td><td align="center">Member's First Name</td></tr>
<tr class="sabrinatrdark"><td align="center">~LASTNAME~</td><td align="center">Member's Last Name</td></tr>
<tr class="sabrinatrdark"><td align="center">~EMAIL~</td><td align="center">Member's Email Address</td></tr>
<tr class="sabrinatrdark"><td align="center">~AFFILIATE_URL~</td><td align="center">Member's Affiliate URL</td></tr>
</table>
</td></tr>
<form method="post" name="theform" id="theform" action="contactmembers.php">
<tr class="sabrinatrlight"><td align="center" valign="top">Subject:</td><td><input type="text" name="subject" id="subject" maxlength="255" size="72" class="typein"></td></tr>
<tr class="sabrinatrlight"><td align="center" valign="top">URL:</td><td><input type="text" name="url" id="url" maxlength="255" size="72" class="typein"></td></tr>
<tr class="sabrinatrlight"><td align="center" valign="top">Ad&nbsp;Body:</td><td><textarea name="adbody" id="adbody" rows="20" cols="105"></textarea></td></tr>
<tr class="sabrinatrlight"><td align="center" valign="top">Save&nbsp;Email</td><td><input type="checkbox" name="save" id="save" value="1"></td></tr>
<tr class="sabrinatrdark">
<td align="center" colspan="2">
<input type="hidden" name="saveid" id="saveid" value="">
<input type="hidden" name="action" value="send">
<input type="submit" value="SEND" class="sendit">
</form>
</td>
</tr>
</table>
</td></tr></table>
<br><br>
<?php
include "../footer.php";
?>