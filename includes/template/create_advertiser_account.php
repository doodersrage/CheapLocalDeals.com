<body>
<div class="container">
  <div class="content_box">
    <table cellpadding="15" class="tbl_page_content" cellspacing="0">
      <tr>
        <td><table width="740" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td align="center" style="padding-bottom: 10px;"><span class="advert_form_header">Questions or no time to fill out our form? Call us, we can help! 866.283.6809</span></td>
            </tr>
            <tr>
              <td width="80%"><form enctype="multipart/form-data" name="form1" method="post" action="" class="create_advertiser_account_frm">
                  <table border="0" cellspacing="0" cellpadding="0" class="advertiser_form" align="center">
                    <tr>
                      <th align="center" colspan="2">Company Information (*indicates required field) </th>
                    </tr>
                    <?PHP echo $form_output; ?>
                    <tr>
                      <td align="center" colspan="2"><input class="submit_btn" type="submit" name="Submit" value="Create Account"></td>
                    </tr>
                  </table>
                </form></td>
            </tr>
          </table></td>
      </tr>
    </table>
  </div>
  <?PHP require(TEMPLATE_DIR.'page_footer.php');
  		require(TEMPLATE_DIR.'page_header.php'); ?>
</div>
<?PHP require(INCLUDES_DIR.'application_bottom.php'); ?>
</body>
</html>