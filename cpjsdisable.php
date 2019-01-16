<?php
/*
	Plugin Name: Hapus emoji & embed & block badbots
	Plugin URI: http://google.com
	Description: reduce http requests embed - emoji
	Author: Heru
	Version: 1.2
	Author URI: http://google.com
*/


remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

function cb_disable_peskies_disable_embeds_rewrites( $rules ) {
	foreach ( $rules as $rule => $rewrite ) {
		if ( false !== strpos( $rewrite, 'embed=true' ) ) {
			unset( $rules[ $rule ] );
		}
	}
	return $rules;
}

function cb_disable_peskies_disable_embeds_tiny_mce_plugin( $plugins ) {
	return array_diff( $plugins, array( 'wpembed' ) );
}

function cb_disable_peskies_disable_embeds_remove_rewrite_rules() {
	add_filter( 'rewrite_rules_array', 'cb_disable_peskies_disable_embeds_rewrites' );
	flush_rewrite_rules();
}

function cb_disable_peskies_disable_embeds_flush_rewrite_rules() {
	remove_filter( 'rewrite_rules_array', 'cb_disable_peskies_disable_embeds_rewrites' );
	flush_rewrite_rules();
}


function cb_disable_peskies_disable_embeds()
{

	// Remove the REST API endpoint.
	remove_action( 'rest_api_init', 'wp_oembed_register_route' );

	// Turn off oEmbed auto discovery.
	add_filter( 'embed_oembed_discover', '__return_false' );

	// Don't filter oEmbed results.
	remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

	// Remove oEmbed discovery links.
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

	// Remove oEmbed-specific JavaScript from the front-end and back-end.
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	
	add_filter( 'tiny_mce_plugins', 'cb_disable_peskies_disable_embeds_tiny_mce_plugin' );

	// Remove all embeds rewrite rules.
	add_filter( 'rewrite_rules_array', 'cb_disable_peskies_disable_embeds_rewrites' );


}
add_action( 'init', 'cb_disable_peskies_disable_embeds', 99 );
register_activation_hook( __FILE__, 'cb_disable_peskies_disable_embeds_remove_rewrite_rules' );
register_deactivation_hook( __FILE__, 'cb_disable_peskies_disable_embeds_flush_rewrite_rules' );

function crunchify_remove_jquery_migrate_load_google_hosted_jquery() {
	if (!is_admin()) {
		wp_deregister_script('jquery');
	wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js', false, null);
	wp_enqueue_script('jquery');
	}
}
add_action('init', 'crunchify_remove_jquery_migrate_load_google_hosted_jquery');

function heru_htaccess_contents( $rules )
{
$my_content = <<<EOD
\n
BrowserMatchNoCase "Baiduspider" bots
BrowserMatchNoCase "HTTrack" bots
BrowserMatchNoCase "YandexBot" bots
BrowserMatchNoCase "DotBot" bots
BrowserMatchNoCase "MJ12bot" bots
BrowserMatchNoCase "bomrabot" bots
BrowserMatchNoCase "Trident" bots
BrowserMatchNoCase "coccocbot-web" bots
BrowserMatchNoCase "AhrefsBot" bots
BrowserMatchNoCase "SemrushBot" bots
BrowserMatchNoCase "TweetmemeBot" bots
BrowserMatchNoCase "Mail.RU_Bot" bots
BrowserMatchNoCase "linkdexbot" bots
BrowserMatchNoCase "Sogou" bots
BrowserMatchNoCase "MegaIndex.ru" bots
BrowserMatchNoCase "BLEXBot" bots
BrowserMatchNoCase "Qwantify" bots
BrowserMatchNoCase "Nimbostratus-Bot" bots
BrowserMatchNoCase "CCBot" bots
BrowserMatchNoCase "ZoominfoBot" bots
BrowserMatchNoCase "SeznamBot" bots
BrowserMatchNoCase "ZoomBot" bots
BrowserMatchNoCase "Yeti" bots
BrowserMatchNoCase "Exabot" bots
BrowserMatchNoCase "Wget" bots
BrowserMatchNoCase "Researchscan" bots
BrowserMatchNoCase "Uptimebot" bots

Order Allow,Deny
Allow from ALL
Deny from env=bots

# BLOCK USER AGENTS
RewriteEngine on
RewriteCond %{HTTP_USER_AGENT} xenu [NC,OR]
RewriteCond %{HTTP_USER_AGENT} nutch [NC,OR]
RewriteCond %{HTTP_USER_AGENT} curl [NC,OR]
RewriteCond %{HTTP_USER_AGENT} larbin [NC,OR]
RewriteCond %{HTTP_USER_AGENT} heritrix [NC,OR]
RewriteCond %{HTTP_USER_AGENT} wget [NC,OR]
RewriteCond %{HTTP_USER_AGENT} Trident/5\.0 [NC]
RewriteRule !^robots\.txt$ - [F]

# BLOCK BLANK USER AGENTS
RewriteCond %{HTTP_USER_AGENT} ^-?$
RewriteRule ^ - [F]

# BEGIN block bad bots
SetEnvIfNoCase User-agent (Arachni|Mechanize|AhrefsBot|DomainCrawler|GrapeshotCrawler|MaxPointCrawler|SEOkicks|SemrushBot|YFF35|yandex|YandexBot|check.exe|baidu|foobar|dotbot|bomrabot|mj12bot) not-allowed=1

Order Allow,Deny
Allow from ALL

# END block bad bots\n
EOD;
    return $my_content . $rules;
}
add_filter('mod_rewrite_rules', 'heru_htaccess_contents');

//filter
add_filter( 'feed_links_show_comments_feed', '__return_false' );
