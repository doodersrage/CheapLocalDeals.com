<?PHP echo PAGE_HEADER; ?>
<body>
<div class="container">
<div id="menu">
<?PHP echo LEFT_NAV; ?>
</div>
<table class="page_content" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td valign="top" class="admin_right_clm"><?PHP echo (isset($session_warning) ? $session_warning : ''); ?><div class="error_box"></div><?PHP echo (defined('ADMIN_PAGE_CONTENT') ? ADMIN_PAGE_CONTENT : ''); ?></td>
  </tr>
</table>
</div>
<?PHP
echo $prnt_header->page_javascript();
?>
</body>
</html>
