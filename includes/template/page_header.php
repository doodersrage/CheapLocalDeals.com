<?PHP
// page header printed to all front end pages
?>

<div class="header_box">
  <table class="page_header" border="0" width="900" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
    <tr>
      <td width="395" align="left" id="site_logo"><a href="<?PHP echo SITE_URL; ?>"><img border="0" src="<?PHP echo CONNECTION_TYPE; ?><?PHP echo STD_TEMPLATE_DIR; ?>images/cld_logo.jpg" width="395" height="70" alt="cheap local deals logo image" /></a></td>
      <td width="452" align="center" id="head_info"><table align="center">
          <tr>
            <td align="center"><?PHP
        echo draw_dynamic_header_area();
		?>
            </td>
          </tr>
          <tr>
            <td align="center"> SIGNUP ONLINE OR CALL <font color="#FF0000">1-866-283-6809</font> </td>
          </tr>
        </table></td>
      <td width="53" align="right" id="bbb_logo"><a href="http://www.bbb.org/norfolk/business-reviews/internet-shopping/cheap-local-deals-in-chesapeake-va-90015797" rel="nofollow" target="_blank"><img border="0" src="<?PHP echo CONNECTION_TYPE; ?><?PHP echo STD_TEMPLATE_DIR; ?>images/bbb_logo.png" width="53" height="80" alt="bbb logo image" /></a></td>
    </tr>
  </table>
  SESSION_EXP_WARNING
  <div class="error_box"></div>
</div>
