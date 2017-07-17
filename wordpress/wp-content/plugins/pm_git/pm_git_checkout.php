<?php
$PMGitCheckout = new PMGitCheckout();
class PMGitCheckout
{
	public $pm_git_checkout;
	public $pm_git_remote;
	public $pm_git_directory;

	public function __construct() {
		$this->set_option();

		if ($this->pm_git_checkout === '1') {
			//管理バーにメニュー追加
			add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_item' ), 1000 );
		}
		//特定のクエリ条件でgit checkout
		add_action( 'admin_init', array( $this, 'check_and_git_checkout' ) );
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
					'title' => 'git checkout'
				)
			);

			$pm_git_master_on = get_option('pm_git_master_on', '0');

			$i = 0;
			$local_branchs = [];

			$fp = popen('cd ' . $this->pm_git_directory . '; git branch 2>&1', "r");
			while(!feof($fp))
			{
				$branchs = fread($fp, 1024);
				$branchs = explode(' ', $branchs);

				foreach ($branchs as $branch) {
					$branch = str_replace('*', "", $branch);

					if (!$branch || $branch === '*') {
						continue;
					}

					if (!$pm_git_master_on) {
						if (trim($branch) == 'master') {
							continue;
						}
					}

					$wp_admin_bar->add_menu( array(
						'parent' => __CLASS__,
						'id' => __CLASS__ . '_gitcheckout'.$i,
						'meta' => array(),
						'title' => 'git checkout '.$branch,
						'href' => wp_nonce_url( add_query_arg( array('pm_action' =>'gitcheckout', 'branch' => $branch, 'pm_branch_remote_flag' =>'0'), $_SERVER[ 'REQUEST_URI' ] ) )
					) );
					$i++;
					$local_branchs[] = $branch;
				}
			}

			$fp = popen('cd ' . $this->pm_git_directory . '; git branch -r 2>&1', "r");
			while(!feof($fp))
			{
				$branchs = fread($fp, 1024);
				$branchs = explode(' ', $branchs);

				foreach ($branchs as $branch) {
					if (!$branch || in_array($branch, array($this->pm_git_remote.'/HEAD', '->'))) {
						continue;
					}

					if (!$pm_git_master_on) {
						if (trim(str_replace($this->pm_git_remote.'/', "", $branch)) == 'master') {
							continue;
						}
					}

					if (in_array(str_replace($this->pm_git_remote.'/', "", $branch), $local_branchs)) {
						continue;
					}

					$wp_admin_bar->add_menu( array(
						'parent' => __CLASS__,
						'id' => __CLASS__ . '_gitcheckout'.$i,
						'meta' => array(),
						'title' => 'git checkout '.$branch,
						'href' => wp_nonce_url( add_query_arg( array('pm_action' =>'gitcheckout', 'branch' => $branch, 'pm_branch_remote_flag' =>'1'), $_SERVER[ 'REQUEST_URI' ] ) )
					) );
					$i++;
				}
			}
		}
	}
	public $message = '';
	/**
	 * git pull by admin bar.
	 */
	public function check_and_git_checkout() {
		if ( !current_user_can( 'administrator' ) ) {
			return;
		}
		if ( !isset( $_GET[ 'pm_action' ] ) || $_GET['pm_action'] !== 'gitcheckout' || !$_GET['branch'] ) {
			return;
		}
		$this->git_checkout();
	}
	public function git_checkout() {
		if ( !wp_verify_nonce( $_REQUEST[ '_wpnonce' ] ) ) {
			wp_die( '不正アクセス (wp_verify_nonce)' );
		}

		$branch = $_GET['branch'];

		if ($_GET['pm_branch_remote_flag'] === '1') {
			$fp = popen('cd ' . $this->pm_git_directory . '; git checkout -b '.str_replace($this->pm_git_remote.'/', "", $branch).' '.$branch.' 2>&1', "r");
		} else {
			$fp = popen('cd ' . $this->pm_git_directory . '; git checkout '.$branch.' 2>&1', "r");
		}

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
		$this->pm_git_checkout = get_option('pm_git_checkout', '1');
		$this->pm_git_remote = get_option('pm_git_remote', 'origin');
		$this->pm_git_directory = get_option('pm_git_directory');
	}
}
