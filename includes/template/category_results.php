<?PHP 
echo '<script src="'.CONNECTION_TYPE.'js_load.deal?js_doc[]='.urlencode('includes/js/category_results.js').'&amp;js_doc[]='.urlencode('includes/libs/star-rating/jquery.MetaData.js').'&amp;js_doc[]='.urlencode('includes/libs/star-rating/jquery.rating.js').'" type="text/javascript" language="javascript"></script>';
?>
<div class="container">
  <div class="content_box"> $error_message$
    <div class="page_header_content">$page_content_header$</div>
    <div class="tbl_page_content">
      <div class="font_box_border">
        <div class="bc_head_box">
          <div class="cat_bc">$bc$</div>
        </div>
      </div>
      <div class="advertListing">
        <table border="0" width="840" cellspacing="0" cellpadding="0" align="center">
          $page_content_listing$
        </table>
      </div>
      <div class="advListFoot">$page_content_footer$</div>
    </div>
    </div>
  $pg_footer_content$ </div>