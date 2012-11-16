<?PHP
global $page_output;
?>
<div class="wrap">
    <div class="headAdmin">
    	<div class="headLinks">
        	<a class="headerAdminLink" href="<?PHP echo MOB_SSL_URL; ?>?action=manageAcc&section=address">Address</a> | <a class="headerAdminLink" href="<?PHP echo MOB_SSL_URL; ?>?action=manageAcc&section=password">Password</a> | <a class="headerAdminLink" href="<?PHP echo MOB_SSL_URL; ?>?action=manageAcc&section=orders">Orders</a> | <a class="headerAdminLink" href="<?PHP echo MOB_SSL_URL; ?>?action=manageAcc&section=certs">Certificates</a> | <a class="headerAdminLink" href="<?PHP echo MOB_SSL_URL; ?>?action=manageAcc&section=credit">Credit</a>
        </div>
    </div>
    <div class="main">
        <div class="logo">
            <a href="<?PHP echo MOB_URL; ?>"><img class="alnCent" src="includes/template/images/cldLogo.png" height="35" width="275" alt="Cheap Local Deals start saving logo image"></a>
        </div>
        <div class="mnTxt">
        	<?PHP echo $page_output; ?>
        </div>
    </div>
    <?PHP echo crtFoot(); ?>
</div>