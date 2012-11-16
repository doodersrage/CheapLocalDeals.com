<?PHP
echo $prnt_header->page_javascript();
?>
<script type="text/javascript">
document.write(unescape("%3Cscript src='<?PHP echo CONNECTION_TYPE.'js_load.deal?type=curl&amp;js_doc='.urlencode('https://ssl.google-analytics.com/ga.js'); ?>' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("<?PHP echo GOOG_ANALYTICS_ACC; ?>");
pageTracker._trackPageview();
} catch(err) {}</script>
<?PHP
// disconnect from MySQL server
$dbh->disconnect();
?>