<?php
session_start();
include('logo.html');
include('menulist.html');
include('configuration');

if(isset($_POST['submit'])) {
	$name = $_REQUEST['name'];
	$email = $_REQUEST['email'];
	$website = $_REQUEST['website'];
	$accountid = $_REQUEST['accountid'];
	$description = $_REQUEST['description'];
	if(trim($name) == '') {
		$errmsg = 'Please enter your name';
	}
	else if(trim($email) == '') {
		$errmsg = 'Please enter your email address';
	}
	else if(!isEmail($email)) {
		$errmsg = 'Your email address is not valid';
	}
	else if(trim($accountid) == '') {
		$errmsg = 'Please enter your account id';
	}	
	else if(strlen($accountid) < 4 || strlen($accountid) > 16) 	{
		$errmsg = 'account id should have length between 4 and 16';
	}
	else if(trim($accountid) != '') {
		$handle = sqlite3_open($db) or die("Could not open database");
		$query = "SELECT * FROM users where username=\"$accountid\"";
		$result = sqlite3_query($handle, $query) or die("Error in query: ".sqlite3_error($handle));
		
		if (($row = sqlite3_fetch_array($result)) != '') { 
			$errmsg = 'Desired id already exist. Please enter different Desired id.';
		}
	} 
	else if(trim($description) == '') {
		$errmsg = 'Please enter your description';
	}
	else if(strlen($description) < 20) {
		$errmsg = 'Description should atleast be 20 characters';	
	}
}
 
if(trim($errmsg) != '' || !isset($_POST['submit'])) {
?>
		<h3 class="firstheader" align="center">Request CCAN account</h3> 
		<div align="center" class="errmsg"><font color="RED"><?=$errmsg;?></font></div>
		<form method="post" action="requestid.php">
	  	<table align="center" width="70%" border="0" bgcolor="999999" cellpadding="4" cellspacing="1">
 		<tr align="left" bgcolor="lightgray">
 		<td> <p>Full name: </p> <p><input name="name" type="text" value="<?=$name;?>"/></p> </td>
		</tr>
		<tr align="left" bgcolor="silver">
 		<td> <p>Email: </p> 	<p><input name="email" type="text" value="<?=$email;?>"/> <br /></p>	</td>
		</tr>
		<tr align="left" bgcolor="lightgray">
 		<td> <p>Desired ID: </p><p><input name="accountid" type="text" value="<?=$accountid;?>"/><br /></p></td>
		</tr>
		<tr align="left" bgcolor="silver">
 		<td><p> Web Site[Optional]: </p><p><input name="website" type="text" value="<?=$website;?>"/><br /></p></td>
		</tr>
		<tr align="left" bgcolor="lightgray">
 		<td><p> A short description of what you are planning to contribute: </p>
		 	<p><textarea name="description" rows="10" cols="70" value="<?=$description;?>"> </textarea></p>
		</td>
		</tr>
		<tr align="center">
 		<td> <input type="submit" name="submit" value="Request Account"/> </td>
		</tr>
		</table>
		</form><hr>
<?php
}

else {
$handle = sqlite3_open($db) or die("Could not open database");
$query = "insert into users values(\"".$name."\",\"".$email."\",\"".$accountid."\",\"".$website."\",\"".$description."\",\"false\" ,\"false\")";
$result = sqlite3_exec($handle, $query) or die("Error in query: ".sqlite3_error($handle));

$subject = "Approval of ccan account";
$message = "There is new request for ccan account id.\n\n Please use the following link to approve http://ccan.ozlabs.org/dinesh/approval.php?accountid=".$accountid;
mail($ccan_admin, $subject, $message, "From: $email");
?>
	</br><div>Thank you for registering with CCAN. You will get the password to your mail after approval.</div>
<?php
}

function isEmail($email)
{
	return(preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i"
			,$email));
}
?>
