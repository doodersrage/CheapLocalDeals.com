<div class="container">
  <div class="content_box">
    <div class="page_header_content">$page_header$</div>
    <div class="page_header_foot_content">$page_description$</div>
    <?PHP
	if($api_load->status != 1) {
	?>
    <div class="banner_box"> <a href="http://www.localmarketingexpo.com/" target="_blank"><img src="<?PHP echo CONNECTION_TYPE; ?>images/LMX-banner.jpg" alt="Local Marketing Expo - LMX" height="90" width="728"/></a> </div>
    <?PHP
	}
	?>
    <div class="banner_box"> <img src="<?PHP echo CONNECTION_TYPE; ?>images/75off.jpg" alt="save up to 75 percent off gift certificates" height="49" width="900"/></div>
    <table border="0" width="900" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="2">$green_cat_list_head$<img class="listhead02" src="<?PHP echo CONNECTION_TYPE; ?>STD_TEMPLATE_DIRimages/002-list_head.jpg" name="catlisthead" width="785" height="63" alt="list head" /></td>
      </tr>
      <tr>
        <td width="100%" colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td width="785" class="catlistbg02" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="10">
                  <tr>
                    <td width="100%"><table width="95%" border="0" align="right" cellpadding="4" cellspacing="0">
                        <tr>
                          <td align="center" valign="top" class="se_by_kw"><a href="$fav_lnk$"><img src="images/deals.gif" width="251" height="40" alt="Deals of the Month" /></a>
                            <div class="pop_src_box">
                              <div class="pop_box_hdtxt">You won't find better deals ANYWHERE!</div>
                              <img src="images/pop-tag.gif" alt="Popular Local Deal Searches Bar" width="639" height="11" />
                              <div id="loc_bxs">
                                <div class="pop_img_bx"><a href="$rest_pop_lnk$"><img src="images/food-cat.jpg" width="148" height="148" alt="Food and Restaurant Deals" /></a><a href="$rest_pop_lnk$">Dining Deals</a></div>
                                <div class="pop_img_bx"><a href="$auto_pop_lnk$"><img src="images/auto-cat.jpg" width="148" height="148" alt="Automotive Care Deals" /></a><a href="$auto_pop_lnk$">Auto Service Deals</a></div>
                                <div class="pop_img_bx"><a href="$entertain_pop_lnk$"><img src="images/ent-cat.jpg" width="148" height="148" alt="Entertainment Deals" /></a><a href="$entertain_pop_lnk$">Entertainment Deals</a></div>
                                <div class="pop_img_bx"><a href="$person_pop_lnk$"><img src="images/spa-cat.jpg" width="148" height="148" alt="Personal Healthcare Deals" /></a><a href="$entertain_pop_lnk$">Personal Care Deals</a></div>
                                <div class="pop_clear"></div>
                              </div>
                            </div>
                            <img id="bx_brd" src="images/thn-gry-line.gif" alt="Popular Local Deal Diving Bar" width="639" height="1" /></td>
                        </tr>
                        <tr>
                          <td align="left" valign="top">$page_listing_text$
                            <hr align="center" class="clear" />
                            <center>
                              <a class="top_cat" href="$view_all_link$">View All</a>
                            </center></td>
                        </tr>
                      </table></td>
                  </tr>
                </table></td>
              <td width="115" valign="top" class="catlistrightbg02"><img src="<?PHP echo CONNECTION_TYPE; ?>STD_TEMPLATE_DIRimages/002-right-brd.jpg" width="171" height="345" alt="right border" /></td>
            </tr>
          </table></td>
      </tr>
      <tr>
        <td colspan="2" align="left"><img src="<?PHP echo CONNECTION_TYPE; ?>STD_TEMPLATE_DIRimages/002-list_foot.jpg" name="catlisthead" width="785" height="45" alt="list footer" /></td>
      </tr>
    </table>
    <div class="tbl_page_content">
      <div class="boxCent" style="width:858px">
        <div class="regular_list_head">
          <div class="rlh_left_corner"></div>
          <div class="rlh_right_corner"></div>
          <div class="header_txt"><?PHP echo 'Your Random '.$cities_tbl->city.', '.$cities_tbl->state.' Local Deal'; ?></div>
        </div>
        <div class="adv_listing_mid"></div>
        <div class="listItem">
          <?PHP
                  require(CLASSES_DIR.'sections/random_advert.php');
                  $rand_lst_qry = new rand_lst_qry;
                  echo $rand_lst_qry->list_row[0];
                  ?>
        </div>
        <div class="adv_listing_mid"></div>
        <div class="adv_listing_mid"> </div>
        <div class="regular_list_head">
          <div class="rlh_left_bot_corner"></div>
          <div class="rlh_right_bot_corner"></div>
          <div class="header_txt">&nbsp;</div>
        </div>
        <script type="text/javascript" defer="defer">
                  <!--
                    var requirementsArray = [];
                    <?PHP echo $rand_lst_qry->sel_restriction_src; ?>
                  // -->
                  </script>
      </div>
      <div class="advListFoot zip_footer">
        <div id="footer_content"></div>
      </div>
    </div>
    <div class="textCent boxCent"><font color="red"> $page_count$ </font> deal seekers have viewed this page. <a href="http://www.cheaplocaldeals.com/new-advertiser/" class="add_business_lnk">Click here to add your business today!</a></div>
    <script type="text/javascript" src="<?PHP echo OVERRIDE_SITE_URL.'js_load.deal?js_doc='.urlencode('includes/js/results.js'); ?>" defer="defer"></script>
    <?PHP 
echo '<script src="'.CONNECTION_TYPE.'js_load.deal?js_doc[]='.urlencode('includes/libs/star-rating/jquery.rating.js').'" type="text/javascript" language="javascript" defer="defer"></script>';
?>
    $new_advert_bx$ </div>
  $pg_footer_content$ </div>
