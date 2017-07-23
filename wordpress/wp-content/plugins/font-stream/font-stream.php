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
add_action( 'wp_ajax_font_stream_save', [ $FontStream, 'save' ] );
add_action( 'wp_ajax_font_stream_load', [ $FontStream, 'load' ] );

add_action( 'wp_head', [ $FontStream, 'css' ], 1 );

class Font_Stream
{
	protected $_option_name = 'comquest_font_stream_data';

	public function __construct() {

		add_action( 'admin_enqueue_scripts', function ( $hook ) {
			global $pagenow;
			if ( $pagenow == "options-general.php" && isset( $_GET['page'] ) && $_GET['page'] == "font_stream" ) {
				wp_enqueue_script( "font-stream-js", plugins_url( 'font-stream/js/main.js' ), [ 'jquery' ] );
				wp_localize_script(
					'font-stream-js',
					'FONT_STREAM',
					[
						'endpoint' => admin_url( 'admin-ajax.php' )
					]
				);
				wp_enqueue_style( "font-stream-css", plugins_url( 'font-stream/css/main.css' ) );
			}
		} );

		// メニューを追加します。
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),
			[ $this, 'fontstream_edit_plugin_list_links' ] );

		add_action( 'admin_menu', function () {
			add_options_page( 'fontstream', 'Font Stream', 'manage_options', 'font_stream', [ $this, 'show_option' ] );
		} );

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
		echo '<form action="' . admin_url( 'options-general.php?page=font_stream' ) . '" method="post" accept-charset="utf-8" class="font-stream">
			<h4>Token</h4>
			<div>
				<input type="text" id="token" value="' . $this->font_stream_directory . '">
			</div>
			
			<h4>Font</h4>
			<table class="fontList">
				<thead>
					<tr>
						<th class="font">フォント</th>
						<th class="weight">ウェイト</th>
						<th class="type">種類</th>
						<th class="delete">削除</th>
					</tr>
				</thead>
				<tbody class="fontList"></tbody>
			</table>
			<button name="submit" class="add button button-primary">＋フォントを追加</button>
			<button name="submit" class="save button button-primary">変更を保存</button>
		</form>';
	}

	public function save() {
		$token = stripslashes( $_POST['token'] );
		$css = stripslashes( $_POST['css'] );
		$options = $_POST['options'];
		$jsonArray = [
			"status" => 0
		];
		$data = [
			"token"   => $token,
			"css"     => $css,
			"options" => $options
		];
		update_option( $this->_option_name, $data );
		wp_send_json( $jsonArray );
	}

	public function load() {
		$data = get_option( $this->_option_name, true );
		wp_send_json( $data );
	}

	public function css() {
		$data = get_option( $this->_option_name, true );
		echo '<style>' . $data['css'] . '</style>';
//		echo '<style>body{font-family: AnzuMojiMono !important;}</style>';
	}
}