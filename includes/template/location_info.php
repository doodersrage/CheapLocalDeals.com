<?PHP 
global $cities_tbl;
echo '<script src="'.CONNECTION_TYPE.'js_load.deal?js_doc[]='.urlencode('includes/js/loc_inf_advert_info.js').'&amp;js_doc[]='.urlencode('includes/libs/star-rating/jquery.MetaData.js').'&amp;js_doc[]='.urlencode('includes/libs/star-rating/jquery.rating.js').'" type="text/javascript" language="javascript" ></script>';
?>

<div class="container">
  <div class="content_box"> $mid_temp_content$
    <div class="advert_listing_table">
      <div class="boxCent" style="width:858px">
        <div class="regular_list_head">
          <div class="rlh_left_corner"></div>
          <div class="rlh_right_corner"></div>
          <div class="header_txt">More <?PHP echo $cities_tbl->city.', '.$cities_tbl->state; ?> Local Deals You Might Like</div>
        </div>
        <div class="adv_listing_mid"> </div>
        <div class="listItem">
          <?PHP
                  require(CLASSES_DIR.'sections/random_advert.php');
                  $rand_lst_qry = new rand_lst_qry(2);
                  echo $rand_lst_qry->list_row[0];
                  ?>
        </div>
        <div class="adv_listing_mid">
          <hr class="listing_divisor" />
        </div>
        <div class="listItem">
          <?PHP
                   echo $rand_lst_qry->list_row[1];
                  ?>
        </div>
        <div class="adv_listing_mid"> </div>
        <div class="regular_list_head">
          <div class="rlh_left_bot_corner"></div>
          <div class="rlh_right_bot_corner"></div>
          <div class="header_txt">&nbsp;</div>
        </div>
        <script type="text/javascript" defer="defer">
		  <!--
			<?PHP echo $rand_lst_qry->sel_restriction_src; ?>
		  // -->
		  </script>
      </div>
    </div>
  </div>
  $pg_footer_content$ </div>
