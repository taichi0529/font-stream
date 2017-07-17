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
		// メニューを追加します。
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'fontstream_edit_plugin_list_links' ));

		add_action('admin_menu', array($this, 'add_options_page'));

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

	/**
	 * サイドメニューに追加
	 */
	public function add_options_page() {
		add_options_page('fontstream', 'Font Stream', 'manage_options', 'font_stream', array($this, 'show_option'));
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
		echo '<form action="'.admin_url( 'options-general.php?page=font_stream' ).'" method="post" accept-charset="utf-8">
			<h4>Token</h4>
			
			<table class="form-ddddtable">
				<tbody>
					<tr>
						<th scope="row">使用機能</th>
						<td>
							<input type="checkbox" id="check_font_stream_pull" name="font_stream_pull" value="1" '.$pull_checked.'>&nbsp;<label for="check_font_stream_pull">git pull</label><br>
							<input type="checkbox" id="check_font_stream_fetch" name="font_stream_fetch" value="1" '.$fetch_checked.'>&nbsp;<label for="check_font_stream_fetch">git fetch</label><br>
							<input type="checkbox" id="check_font_stream_checkout" name="font_stream_checkout" value="1" '.$checkout_checked.'>&nbsp;<label for="check_font_stream_checkout">git checkout</label><br>
							<input type="checkbox" id="check_font_stream_checkout" name="font_stream_master_on" value="1" '.$master_on_checked.'>&nbsp;<label for="check_font_stream_checkout">git enable checkout master</label>
						</td>
					</tr>
					<tr>
						<th scope="row">REMOTE</th>
						<td>
							<input type="text" name="font_stream_remote" value="'.$this->font_stream_remote.'"><br>
						</td>
					</tr>
					<tr>
						<th scope="row">directory</th>
						<td>
							<input type="text" name="font_stream_directory" value="'.$this->font_stream_directory.'"><br>
						</td>
					</tr>
				</tbody>
			</table>
			<input type="submit" name="submit" id="submit" class="button button-primary" value="変更を保存">
		</form>';
	}
}