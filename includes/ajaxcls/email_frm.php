<?PHP
// load application header
require('../../includes/application_top.php');

$eml_frm = '<form name="email_set" target="" onSubmit="return false;">
	<table cellspacing="5">
		<tr>
			<td align="center" id="pop_err"></td>
		</tr>
		<tr>
			<td align="center"><input type="text" name="email_sub" id="email_sub" value=""></td>
		</tr>
		<tr>
			<td align="center"><input type="button" name="Continue" value="Continue" onclick="sub_eml()"></td>
		</tr>
	</table>
	</form>';
	
echo create_warning_box($eml_frm,'Please enter your email');

?>