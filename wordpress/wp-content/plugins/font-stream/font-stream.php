<?php
/*
Plugin Name: font-stream
Plugin URI:
Description:
Version: 0.1.0
Author: Taichi Nakamura
Author URI:
License:
License URI:
Change Log:
20170717 v0.1.0 初版
*/

$FontStream = new Font_Stream();
class Font_Stream
{

	public function __construct() {

		add_action( 'admin_enqueue_scripts', function($hook) {
			global $pagenow;
			if ( $pagenow == "options-general.php" && isset($_GET['page']) && $_GET['page'] == "font_stream" )
			{
				wp_enqueue_script( "font-stream-js", plugins_url('font-stream/js/main.js'), ['jquery'] );
				wp_enqueue_style( "font-stream-css", plugins_url('font-stream/css/main.css'));
			}
		} );

		// メニューを追加します。
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'fontstream_edit_plugin_list_links' ));

		add_action('admin_menu', function(){
			add_options_page('fontstream', 'Font Stream', 'manage_options', 'font_stream', array($this, 'show_option'));
		});

//		$this->set_option();
	}

	/**
	 * プラグインメニューに追加
	 * @param $links
	 * @return array
	 */
	public function fontstream_edit_plugin_list_links( $links ) {
		// We shouldn't encourage editing our plugin directly.
		unset( $links['edit'] );

		// Add our custom links to the returned array value.
		return array_merge( [
			'<a href="' . admin_url( 'options-general.php?page=font_stream' ) . '">設定</a>',
		], $links );
	}


	public function show_option() {
//		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//			$this->update_option();
//		}

		$pull_checked = $this->font_stream_pull === '1' ? 'checked' : '';
		$fetch_checked = $this->font_stream_fetch === '1' ? 'checked' : '';
		$checkout_checked = $this->font_stream_checkout === '1' ? 'checked' : '';
		$master_on_checked = $this->font_stream_master_on === '1' ? 'checked' : '';

		echo "<h2>Font Stream</h2>";
		echo '<form action="'.admin_url( 'options-general.php?page=font_stream' ).'" method="post" accept-charset="utf-8" class="font-stream">
			<h4>Token</h4>
			<div>
				<input type="text" name="font_stream_directory" value="'.$this->font_stream_directory.'">
			</div>
			
			<h4>Font</h4>
			<ul class="fontList">
				<li>アニト L 等幅 <a href="#">削除</a></li>
			</ul>
			<button name="submit" class="add button button-primary">＋フォントを追加</button>
			<input type="submit" name="submit" class="submit button button-primary" value="変更を保存">
		</form>';
	}
}