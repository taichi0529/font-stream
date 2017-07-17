<?php
$PMGitFetch = new PMGitFetch();
class PMGitFetch
{
	public $pm_git_fetch;
	public $pm_git_remote;
	public $pm_git_directory;

	public function __construct() {
		$this->set_option();

		if ($this->pm_git_fetch === '1') {
			//管理バーにメニュー追加
			add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_item' ), 1000 );
		}
		//特定のクエリ条件でgit fetch
		add_action( 'admin_init', array( $this, 'check_and_git_fetch' ) );
		//画面にpull結果を表示
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}
	/**
	 * 管理バーにメニュー追加
	 */
	public function add_admin_bar_item( $wp_admin_bar ) {
		if ( !current_user_can( 'administrator' ) ) {
			return;
		}
		//$AWSTools = new AWSTools();
		//$thisIP = $AWSTools->get_internal_ip();
		if ( is_admin() ) {
			$wp_admin_bar->add_node( array(
					'id' => __CLASS__,
					'title' => 'git fetch'
				)
			);
			$wp_admin_bar->add_menu( array(
				'parent' => __CLASS__,
				'id' => __CLASS__ . '_gitfetch',
				'meta' => array(),
				'title' => 'git fetch',
				'href' => wp_nonce_url( add_query_arg( 'pm_action', 'gitfetch', $_SERVER[ 'REQUEST_URI' ] ) )
			) );
		}
	}
	public $message = '';
	/**
	 * git pull by admin bar.
	 */
	public function check_and_git_fetch() {
		if ( !current_user_can( 'administrator' ) ) {
			return;
		}
		if ( !isset( $_GET[ 'pm_action' ] ) || $_GET['pm_action'] !== 'gitfetch' ) {
			return;
		}
		$this->git_fetch();
	}
	public function git_fetch() {
		if ( !wp_verify_nonce( $_REQUEST[ '_wpnonce' ] ) ) {
			wp_die( '不正アクセス (wp_verify_nonce)' );
		}
		$fp = popen('cd ' . $this->pm_git_directory . '; git fetch '.$this->pm_git_remote.' 2>&1', "r");
		while(!feof($fp))
		{
			$this->message  .= fread($fp, 1024);
		}
		fclose($fp);
	}
	/**
	 * 一時保存されているメッセージがあれば表示
	 */
	function admin_notices() {
		echo wp_kses_post( str_replace("\n", "<br>\n", $this->message) );
	}

	public function set_option() {
		$this->pm_git_fetch = get_option('pm_git_fetch', '1');
		$this->pm_git_remote = get_option('pm_git_remote', 'origin');
		$this->pm_git_directory = get_option('pm_git_directory');
	}
}