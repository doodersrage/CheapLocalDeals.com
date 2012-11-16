<div class="page_footer">
    <table border="0" width="900" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%" height="3" align="center" valign="top" bgcolor="#AD9E74"><img src="images/gldband.jpg" width="900" height="20" alt="gold band footer" /></td>
      </tr>
      <tr>
        <td width="100%" valign="top" bgcolor="#FFFFFF" height="3"><img border="0" src="<?PHP echo STD_TEMPLATE_DIR; ?>images/dort-wt.jpg" width="4" height="3" alt="dort wt image" /></td>
      </tr>
	  <tr>
        <td width="100%" colspan="2" align="center" bgcolor="#537D4A"><div class="states_foot"><?PHP require(FUNCTIONS_DIR.'build_states.php'); ?></div></td>
      </tr>
      <tr>
        <td width="100%" valign="top" bgcolor="#537D4A"><table border="0" width="100%" cellspacing="0" cellpadding="10">
            <tr>
              <td width="100%"><table border="0" align="center" cellpadding="5" cellspacing="5">
                <tr>
                  <td><font size="1" color="#ffffff">CheapLocalDeals.com &copy; 2008-<?PHP echo date('Y'); ?>.&nbsp; All Rights Reserved. - <a href="<?PHP echo SITE_URL; ?>about-cheap-local-deals/"><font size="1" color="#ffffff">About Us</font></a> - <a href="<?PHP echo SITE_URL; ?>privacy-policy/"><font size="1" color="#ffffff">Privacy Policy</font></a> - <a href="<?PHP echo SITE_URL; ?>contact_us.deal"><font size="1" color="#ffffff">Contact Us</font></a> - <a href="http://www.cheaplocaldeals.com/faq/"><font size="1" color="#ffffff">Faq</font></a> - <a href="<?PHP echo SITE_URL; ?>sitemap.html"><font size="1" color="#ffffff">Sitemap</font></a></font></td>
                  <td><a href="<?PHP echo SITE_URL; ?>rss.deal?zip=<?PHP echo (isset($_SESSION['cur_zip']) ? $_SESSION['cur_zip'] : ''); ?><?PHP echo (isset($_SESSION['city']) ? '&city='.$_SESSION['city'] : ''); ?>" target="_blank"><img alt="Cheap Local Deals RSS Feed" border="0" width="28" height="28" src="images/rss_feed.jpg" /></a></td>
                  <td><span id="siteseal"><script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=XA9vgHkC9CWZYbJTs5k33QLK1zyW9JMHPRqoVSYr4fS6LyhpekESKW"></script></span></td>
                </tr>
              </table></td>
            </tr>
          </table></td>
      </tr>
	  <tr>
        <td width="100%" colspan="2" align="center" bgcolor="#537D4A" class="cm_foot"><a href="http://www.customermagnetism.com/" target="_blank">search engine optimization</a> services provided by www.customermagnetism.com</td>
      </tr>
	  <tr>
        <td width="100%" colspan="2" align="center" bgcolor="#537D4A" class="cm_foot">Page Generated: <?PHP echo date("F d Y H:i:s."); ?></td>
      </tr>
    </table>
</div>
