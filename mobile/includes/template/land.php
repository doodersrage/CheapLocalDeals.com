<?PHP
global $geo_data, $error_message;
?>
<div class="wrap">
    <div class="main">
        <div class="logo">
        	<a href="<?PHP echo MOB_URL; ?>"><img class="alnCent" src="includes/template/images/gldDolLog.png" height="65" width="62" alt="Cheap Local Deals dollar sign logo image"></a><br/>
            <a href="<?PHP echo MOB_URL; ?>"><img class="alnCent" src="includes/template/images/cldLogo.png" height="35" width="275" alt="Cheap Local Deals start saving logo image"></a>
        </div>
        <div class="mnTxt">
        	<p>Welcome to Cheap Local Deals Mobile! Now you can find and get great deals while on the go! Within Cheap Local Deals Mobile you will find all of the same great deals you have come to love on our normal site. Find the savings you could use today, tomorrow, or right now in the palm of your hand! Save on anything from a night out at a restaurant, home repair, or even get that annoying automotive problem resolved.</p>
            <h1>Start saving today!</h1>
            <p>We are currently developing the mobile version of our website. If you have trouble getting around please feel free to browse the <a href="<?PHP echo MOB_URL; ?>?browse=normal">normal version of our site</a>!</p>
        </div>
        <div class="locSrchFrm">
        	<?PHP if(!empty($error_message)) echo '<center><strong><font color="red">'.$error_message.'</font></strong></center>'; ?>
        	<form id="landFrm" action="" method="post">
              <label>Search by Zip or City/State</label><br/>
              <input type="text" name="search_box" maxlength="100" size="30" value="<?PHP if(empty($_POST['search_box'])) echo $geo_data->city.', '.$geo_data->region; else echo $_POST['search_box']; ?>"/><br/>
              <input class="submit_btn" type="submit" name="search" value="Search!"/>
            </form>
        </div>
        <div class="mnTxt">
        	<center><strong>OR</strong></center>
            <a class="linkBtn" href="<?PHP echo MOB_URL; ?>state">Browse Listings By State</a>
        </div>
    </div>
    <?PHP echo crtFoot(); ?>
</div>