
jQuery(function(){

	jQuery('.share_table').click(function() {
		jQuery('.share_table').addClass("bookmark_table")
		jQuery('.share_table').removeClass("share_table")
		jQuery('.shared_links').show("medium")	
    });
}); 

var iclink = (document.URL);
	var ictitle = (document.title);
	document.write('<table class="share_table" border="0" cellspacing="0" cellpadding="1" align="center" style="padding:1px;"><tr>');
	document.write('<th>Share This Page</th></tr><tr><td>');
	document.write('<table class="shared_links" style="display:none;" border="0" cellspacing="0" cellpadding="1" align="center" style="padding:1px;"><tr>');
// Email 	
	document.write('<td><a rel="nofollow" href="mailto:?body=Thought you might like this: ' + iclink + '&amp;subject=' + ictitle + '" title="Send E-mail" target="_blank"><img src="http://velkymx.googlepages.com/send.email.gif" width="16" height="16" border="0" /></a></td>');
// InChicks.com	
	document.write('<td><a rel="nofollow" href="http://www.inchicks.com/story_submit.php?u=' + iclink + '&t=' + ictitle + '" title="Add to Inchicks.com" target="_blank"><img src="http://velkymx.googlepages.com/btnIC.gif" border="0" height="16" width="16"></a></td>');
// del.ico.us 	
	document.write('<td><a rel="nofollow" href="http://del.icio.us/post?url=' + iclink + '&amp;title=AskPatty.com - ' + ictitle + '" title="Add to del.ico.us" target="_blank"><img src="http://velkymx.googlepages.com/del.icio.us.gif" width="16" height="16" border="0" /></a></td>');
// My.MSN
	document.write('<td><a rel="nofollow" href="http://my.msn.com/addtomymsn.armx?id=rss&amp;ut=' + iclink + '&amp;title=' + ictitle + '" title="Add to MyMSN" target="_blank"><img src="http://velkymx.googlepages.com/msn.gif" width="16" height="16" border="0" /></a></td>');
// digg.com
	document.write('<td><a rel="nofollow" href="http://digg.com/submit?phase=2&amp;url=' + iclink + '&amp;title=' + ictitle + '" title="Add to Digg.com" target="_blank"><img src="http://velkymx.googlepages.com/digg.gif" width="16" height="16" border="0" /></a></td>');	
// reddit.com
	document.write('<td><a rel="nofollow" href="http://reddit.com/submit?url=' + iclink + '&amp;title=' + ictitle + '" title="Add to ReddIt.com" target="_blank"><img src="http://velkymx.googlepages.com/ReddIt.gif" width="16" height="16" border="0" /></a></td>');	               
// blinklist.com
	document.write('<td><a rel="nofollow" href="http://blinklist.com/index.php?Action=Blink/addblink.php&amp;URL=' + iclink + '" title="Add to BlinkList.com" target="_blank"><img src="http://velkymx.googlepages.com/blinklist.gif" width="16" height="16" border="0" /></a></td>');		
// netvouz
	document.write('<td><a rel="nofollow" href="http://www.netvouz.com/action/submitBookmark?url=' + iclink + '&title=' + ictitle + '" title="Add to netvouz.com" target="_blank"><img src="http://velkymx.googlepages.com/netvouz.gif" width="16" height="16" border="0" /></a></td>');
document.write('</tr><tr>');
// Technorati
	document.write('<td><a rel="nofollow" href="http://www.technorati.com/cosmos/search.html?url=' + iclink + '" title="Add to Technorati" target="_blank"><img src="http://velkymx.googlepages.com/technorati.gif" width="16" height="16" border="0" /></a></td>');
// blogmarks.net
	document.write('<td><a rel="nofollow" href="http://blogmarks.net/my/new.php?mini=1&simple=1&url=' + iclink + '&title=' + ictitle + '" title="Add to Blogmarks.net" target="_blank"><img src="http://velkymx.googlepages.com/blogmarks.gif" width="16" height="16" border="0" /></a></td>');
// google.com
	document.write('<td><a rel="nofollow" href="http://www.google.com/bookmarks/mark?op=add&bkmk=' + iclink + 'l&title=' + ictitle + '" title="Add to Google.com" target="_blank"><img src="http://velkymx.googlepages.com/google.gif" width="16" height="16" border="0" /></a></td>');
// LinkaGoGo
	document.write('<td><a rel="nofollow" href="http://www.linkagogo.com/go/AddNoPopup?url=' + iclink + '&title=' + ictitle + '" title="Add to LinkaGoGo" target="_blank"><img src="http://velkymx.googlepages.com/LinkaGoGo.gif" width="16" height="16" border="0" /></a></td>');
// newsvine
	document.write('<td><a rel="nofollow" href="http://www.newsvine.com/_tools/seed&save?u=' + iclink + '&h=' + ictitle + '" title="Add to NewsVine" target="_blank"><img src="http://velkymx.googlepages.com/newsvine.gif" width="16" height="16" border="0" /></a></td>');
// Facebook
	document.write('<td><a rel="nofollow" href="http://www.facebook.com/sharer.php?u=' + iclink + '&t=' + ictitle + '" title="Add to Facebook" target="_blank"><img src="http://static.ak.facebook.com/images/share/facebook_share_icon.gif" width="16" height="16" border="0" /></a></td>');
// Myspace
	document.write('<td><a rel="nofollow" href="http://www.myspace.com/index.cfm?fuseaction=postto&u=' + iclink + '&t=' + ictitle + '" title="Add to Myspace" target="_blank"><img src="http://cms.myspacecdn.com/cms/post_myspace_icon.gif" width="16" height="16" border="0" /></a></td>');
	document.write('</tr></table>');
	document.write('</td></tr></table>');

