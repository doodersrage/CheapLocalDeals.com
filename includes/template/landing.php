<div class="container">
  <div class="content_box"> $error_message$ $header_content$
    <div class="tbl_page_content">
      <div class="search_area">
        <div class="search_form">
          <form name="zip_search" id="zip_search" action="" method="post">
            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><input id="search_box" name="search_box" type="text" value="<?PHP if(empty($_POST['search_box'])) echo $geo_data->city.', '.$geo_data->region; ?>" />
                  <div class="suggestionsBox" id="suggestions" style="display: none;">
                    <div class="suggestionList" id="autoSuggestionsList"></div>
                  </div></td>
                <td valign="top"><input name="image" type="image" id="search_button" src="<?PHP echo CONNECTION_TYPE; ?>STD_TEMPLATE_DIRimages/search_button.png" /></td>
                <td valign="top"><div id="front_help_btn" class="bubbleInfo"> <img class="trigger" src="<?PHP echo CONNECTION_TYPE; ?>images/600px-Circle-question-red_svg.png" alt="submit form question mark" />
                    <div class="popup"> Enter your city and state or zip code and click search or click <a href="SITE_URLstate-browse/">browse local deals by state</a> to begin your search for local deals! </div>
                  </div></td>
              </tr>
            </table>
          </form>
        </div>
        <img src="<?PHP echo CONNECTION_TYPE; ?>STD_TEMPLATE_DIRimages/header_search01.jpg" alt="header search image" name="bbb_logo" width="860" height="300" border="0" usemap="#bbb_logoMap" class="search_img" /> </div>
      <div class="boxCent" style="width:858px">
        <div class="regular_list_head">
          <div class="rlh_left_corner"></div>
          <div class="rlh_right_corner"></div>
          <div class="header_txt"><?PHP echo 'Your Random '.$geo_data->city.', '.$geo_data->region.' Local Deal'; ?></div>
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
        <div class="regular_list_head">
          <div class="rlh_left_bot_corner"></div>
          <div class="rlh_right_bot_corner"></div>
          <div class="header_txt">&nbsp;</div>
        </div>
        <br />
        <script type="text/javascript" defer="defer">
		//<![CDATA[
		  var requirementsArray = [];
		  <?PHP echo $rand_lst_qry->sel_restriction_src; ?>
		//]]>
		</script>
      </div>
      $footer_content$
      <div class="textCent boxCent"> Current number of active deal seekers: <font color="#FF0000">$active_searches$</font> </div>
    </div>
  </div>
  $pg_footer_content$ </div>
<script type="text/javascript" src="<?PHP echo OVERRIDE_SITE_URL.'js_load.deal?js_doc='.urlencode('includes/js/nav_toggle.js'); ?>" defer="defer"></script>
<?PHP 
echo '<script src="'.CONNECTION_TYPE.'js_load.deal?js_doc[]='.urlencode('includes/libs/star-rating/jquery.rating.js').'" type="text/javascript" language="javascript" defer="defer"></script>';
?>
<map name="bbb_logoMap" id="bbb_logoMap">
  <area shape="rect" coords="144,227,354,273" alt="Start saving by opening an account today!" href="javascript: void(0)" onclick="ldSignUp()" />
</map>
