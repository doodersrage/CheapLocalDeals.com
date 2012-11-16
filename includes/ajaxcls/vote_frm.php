<?PHP

  $reviews_form .= '<tr><td>
  <table width="100%" id="rating_tbl">
	  <tr>
	<th valign="top">Rating</th>
	<td><input name="star" type="radio" class="star" value="1"/>
	  <input name="star" type="radio" class="star" value="2"/>
	  <input name="star" type="radio" class="star" value="3"/>
	  <input name="star" type="radio" class="star" value="4"/>
	  <input name="star" type="radio" class="star" value="5"/></td>
  </tr>
  <tr>
	<th valign="top">Review</th>
	<td><textarea id="word_count" name="review_txt" class="review_txt" cols="30" rows="3"></textarea><input id="advert_id" name="advert_id" type="hidden" value="'.(int)$_POST['loc_id'].'" /><input id="advert_alt_id" name="advert_alt_id" type="hidden" value="'.(int)$_POST['alt_loc_id'].'" /></td>
  </tr>
  <tr>
	<th>Max Characters: </th><td>300</td>
  </tr>
  <tr>
	<th>Remaining: </th><td id="counter">300</td>
  </tr>
  <tr>
	<td colspan="2" align="center"><input id="rev_sub_btn" class="submit_btn" type="button" name="Submit" id="Submit" value="Submit" /></td>
  </tr>
  </table>
  </td></tr>';

echo $reviews_form;
?>