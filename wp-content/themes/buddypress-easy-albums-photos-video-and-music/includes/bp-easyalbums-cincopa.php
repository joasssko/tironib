<?php

function easyAlbums_cincopa_plugin_ver()
{
	return 'ea_wp'.BP_EASYALBUMS_DB_VERSION;
}


define("EASYALBUMS_CINCOPA_REGEXP", "/\[cincopa ([[:print:]]+?)\]/");

function easyAlbums_cincopa_plugin_callback($match)
{
	$uni = uniqid('');
	$ret = '
<!-- Powered by Cincopa WordPress plugin '.easyAlbums_cincopa_plugin_ver().': http://www.cincopa.com/media-platform/wordpress-plugin.aspx -->
<div id="_cp_widget_'.$uni.'"><img src="http://www.cincopa.com/media-platform/runtime/loading.gif" style="border:0;" alt="Powered by Cincopa WordPress plugin" /></div>
<script src="http://www.cincopa.com/media-platform/runtime/libasync.js" type="text/javascript"></script>
<script type="text/javascript">
cp_load_widget("'.urlencode($match[0]).'", "_cp_widget_'.$uni.'");
</script>
';

	$ret .= '<noscript>Click <a href="http://www.cincopa.com/media-platform/view.aspx?fid='.urlencode($match[0]).'">here</a> to open the gallery.<br>';
	$ret .= 'Powered by Cincopa <a href="http://www.cincopa.com/media-platform/wordpress-plugin.aspx">wp content plugins</a> solution for your website and Cincopa MediaSend for <a href="http://www.cincopa.com/mediasend/start.aspx">file transfer</a>.';
	$ret .= '</noscript>';

	return $ret;
}


function easyAlbums_cincopa_plugin($content)
{
	return (preg_replace_callback(EASYALBUMS_CINCOPA_REGEXP, 'easyAlbums_cincopa_plugin_callback', $content));
}

function easyAlbums_cincopa_plugin_rss($content)
{
	return (preg_replace(EASYALBUMS_CINCOPA_REGEXP, '', $content));
}

add_filter('the_content', 'easyAlbums_cincopa_plugin');
add_filter('the_content_rss', 'easyAlbums_cincopa_plugin_rss');
add_filter('the_excerpt_rss', 'easyAlbums_cincopa_plugin_rss');
add_filter('comment_text', 'easyAlbums_cincopa_plugin'); 

add_filter ( 'bp_get_activity_content_body', 'easyAlbums_cincopa_plugin' );
add_filter ( 'bp_get_the_topic_post_content', 'easyAlbums_cincopa_plugin' );

?>