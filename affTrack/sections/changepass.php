<?PHP
if($passwordupdated == 1){
	echo '<div><p>Your password has been successfully updated!</p></div>';
} else {
?><form action="" method="post" class="jNice">
  <h3>Fill out the form below to change your password.</h3>
  <p>*All fields are required!</p><br>
  <fieldset>
    <p>
      <label>*Current Password:</label>
      <input type="password" name="oldpassword" class="text-medium" />
    </p>
    <p>
      <label>*New Password:</label>
      <input type="password" name="newpassword" class="text-medium" />
    </p>
    <p>
      <label>*Confirm New Password:</label>
      <input type="password" name="confirmnewpassword" class="text-medium" />
    </p>
    <input type="hidden" name="changePassSub" value="1" ><input type="submit" name="submit" value="Login" />
  </fieldset>
</form>
<?PHP
}
?>