<?php
$PMGitPull = new PMGitPull();
class PMGitPull
{
	public $pm_git_pull;
	public $pm_git_remote;
	public $pm_git_directory;

	public function __construct() {
		$this->set_option();

		if ($this->pm_git_pull === '1') {
			//管理バーにメニュー追加
			add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_item' ), 1000 );
		}
		//特定のクエリ条件でgit pull
		add_action( 'admin_init', array( $this, 'check_and_git_pull' ) );
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

		if ( is_admin() ) {
			$wp_admin_bar->add_node( array(
					'id' => __CLASS__,
					'title' => 'git pull'
				)
			);

			$branchs = @shell_exec('cd ' . $this->pm_git_directory . '; git branch 2>&1');
			$branchs = explode(' ', $branchs);

			foreach ($branchs as $key => $branch) {
				if (strpos($branch, '*') !== false) {
					$wp_admin_bar->add_menu( array(
						'parent' => __CLASS__,
						'id' => __CLASS__ . '_gitpull',
						'meta' => array(),
						'title' => 'git pull ' . $this->pm_git_remote . ' ' . $branchs[$key+1],
						'href' => wp_nonce_url( add_query_arg( array('pm_action' => 'gitpull', 'branch' => $branchs[$key+1]), $_SERVER[ 'REQUEST_URI' ] ) )
					) );
					break;
				}
			}
		}
	}
	public $message = '';
	/**
	 * git pull by admin bar.
	 */
	public function check_and_git_pull() {
		if ( !current_user_can( 'administrator' ) ) {
			return;
		}
		if ( !isset( $_GET[ 'pm_action' ] ) || $_GET[ 'pm_action' ] !== 'gitpull' || !$_GET['branch'] ) {
			return;
		}
		$this->git_pull();
	}
	public function git_pull() {
		if ( !wp_verify_nonce( $_REQUEST[ '_wpnonce' ] ) ) {
			wp_die( '不正アクセス (wp_verify_nonce)' );
		}

		$branch = $_GET['branch'];

		$this->message = @shell_exec('cd ' . $this->pm_git_directory . '; git pull '. $this->pm_git_remote . ' ' . $branch . ' 2>&1');
		if (!$this->message) {
			$this->message = 'git pullに失敗しました。再度試してください';
		}
	}
	/**
	 * 一時保存されているメッセージがあれば表示
	 */
	function admin_notices() {
		echo wp_kses_post( str_replace("\n", "<br>\n", $this->message) );
	}

	public function set_option() {
		$this->pm_git_pull = get_option('pm_git_pull', '1');
		$this->pm_git_remote = get_option('pm_git_remote', 'origin');
		$this->pm_git_directory = get_option('pm_git_directory');
	}
}