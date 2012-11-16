<?PHP
global $page_output;
?>
<div class="wrap">
    <div class="headAdmin">
    	<div class="headLinks">
        	<a class="navArrow" style="float:left" href="javascript: window.history.back();">&larr;</a> <a class="navArrow" style="float:right" href="javascript: window.history.forward();">&rarr;</a>
            <div style="clear:both"></div>
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