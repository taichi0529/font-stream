<?php
/*
Plugin Name: pm_git
Plugin URI:
Description: サーバー上でgit操作
Version: 1.0.0
Author: PRESSMAN HS, pman-taichi, anahara
Author URI:
License:
License URI:
Change Log:
20150813 v0.1.2 内部IPの取り方を変更。合わせて、内部IP取得関数をCLASSから外して外から使えるようにした。
20150814 v0.1.3 get_internal_ip()を別プラグインに移動
20150814 v0.2 プチリファクタリング
20150814 v0.2.1 全インスタンス、まとめてPULLするように
20150816 v0.2.2 pull対象を単一IP,ALL IPの2モードに
20160405 v2.0.0 systemコマンドでgit pull
20170127 v1.0.0 fetch,pull,checkoutのプラグインをpm gitプラグインに統合
*/


// グローバル変数にインスタンスを生成
$PMGit = new PMGit();
class PMGit
{
	public $pm_git_pull;
	public $pm_git_fetch;
	public $pm_git_checkout;
	public $pm_git_remote;
	public $pm_git_directory;
	public $pm_git_master_on;

	public function __construct() {
		// メニューを追加します。
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'pmgit_edit_plugin_list_links' ));

		add_action('admin_menu', array($this, 'add_options_page'));

		$this->set_option();
	}
	/**
	 * 管理バーにメニュー追加
	 */
	public function pmgit_edit_plugin_list_links( $links ) {
		// We shouldn't encourage editing our plugin directly.
		unset( $links['edit'] );

		// Add our custom links to the returned array value.
		return array_merge( array(
			'<a href="' . admin_url( 'options-general.php?page=pm_git' ) . '">設定</a>',
		), $links );
	}

	public function add_options_page() {
		add_options_page('pmgit', 'pmgit', 'manage_options', 'pm_git', array($this, 'show_option'));
	}

	public function show_option() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->update_option();
		}

		$pull_checked = $this->pm_git_pull === '1' ? 'checked' : '';
		$fetch_checked = $this->pm_git_fetch === '1' ? 'checked' : '';
		$checkout_checked = $this->pm_git_checkout === '1' ? 'checked' : '';
		$master_on_checked = $this->pm_git_master_on === '1' ? 'checked' : '';

		echo "<h2>PM Git</h2>";
		echo '<form action="'.admin_url( 'options-general.php?page=pm_git' ).'" method="post" accept-charset="utf-8">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">使用機能</th>
						<td>
							<input type="checkbox" id="check_pm_git_pull" name="pm_git_pull" value="1" '.$pull_checked.'>&nbsp;<label for="check_pm_git_pull">git pull</label><br>
							<input type="checkbox" id="check_pm_git_fetch" name="pm_git_fetch" value="1" '.$fetch_checked.'>&nbsp;<label for="check_pm_git_fetch">git fetch</label><br>
							<input type="checkbox" id="check_pm_git_checkout" name="pm_git_checkout" value="1" '.$checkout_checked.'>&nbsp;<label for="check_pm_git_checkout">git checkout</label><br>
							<input type="checkbox" id="check_pm_git_checkout" name="pm_git_master_on" value="1" '.$master_on_checked.'>&nbsp;<label for="check_pm_git_checkout">git enable checkout master</label>
						</td>
					</tr>
					<tr>
						<th scope="row">REMOTE</th>
						<td>
							<input type="text" name="pm_git_remote" value="'.$this->pm_git_remote.'"><br>
						</td>
					</tr>
					<tr>
						<th scope="row">directory</th>
						<td>
							<input type="text" name="pm_git_directory" value="'.$this->pm_git_directory.'"><br>
						</td>
					</tr>
				</tbody>
			</table>
			<input type="submit" name="submit" id="submit" class="button button-primary" value="変更を保存">
		</form>';
	}

	public function update_option() {
		if ($_POST['pm_git_pull'] === '1') {
			update_option('pm_git_pull', '1');
		} else {
			update_option('pm_git_pull', '0');
		}

		if ($_POST['pm_git_fetch'] === '1') {
			update_option('pm_git_fetch', '1');
		} else {
			update_option('pm_git_fetch', '0');
		}

		if ($_POST['pm_git_checkout'] === '1') {
			update_option('pm_git_checkout', '1');
		} else {
			update_option('pm_git_checkout', '0');
		}

		if ($_POST['pm_git_master_on'] === '1') {
			update_option('pm_git_master_on', '1');
		} else {
			update_option('pm_git_master_on', '0');
		}

		update_option('pm_git_remote', $_POST['pm_git_remote']);
		update_option('pm_git_directory', $_POST['pm_git_directory']);

		$this->set_option();
	}

	public function set_option() {
		$this->pm_git_pull = get_option('pm_git_pull', '1');
		$this->pm_git_fetch = get_option('pm_git_fetch', '1');
		$this->pm_git_checkout = get_option('pm_git_checkout', '1');
		$this->pm_git_remote = get_option('pm_git_remote', 'origin');
		$this->pm_git_master_on = get_option('pm_git_master_on', '0');
		$this->pm_git_directory = get_option('pm_git_directory');
	}
}

require_once(WP_PLUGIN_DIR.'/pm_git/pm_git_fetch.php');
require_once(WP_PLUGIN_DIR.'/pm_git/pm_git_checkout.php');
require_once(WP_PLUGIN_DIR.'/pm_git/pm_git_pull.php');