<?php
/**
 * WordPress の基本設定
 *
 * このファイルは、MySQL、テーブル接頭辞、秘密鍵、ABSPATH の設定を含みます。
 * より詳しい情報は {@link http://wpdocs.sourceforge.jp/wp-config.php_%E3%81%AE%E7%B7%A8%E9%9B%86 
 * wp-config.php の編集} を参照してください。MySQL の設定情報はホスティング先より入手できます。
 *
 * このファイルはインストール時に wp-config.php 作成ウィザードが利用します。
 * ウィザードを介さず、このファイルを "wp-config.php" という名前でコピーして直接編集し値を
 * 入力してもかまいません。
 *
 * @package WordPress
 */

define('WP_HOME','https://font-stream.asaichi.co.jp');
define('WP_SITEURL','https://font-stream.asaichi.co.jp');
#if(isset($_SERVER['HTTP_X_FORWARDED_HOST'])){
#	$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
#}
$_SERVER['HTTPS'] = 'on';

// 注意: 
// Windows の "メモ帳" でこのファイルを編集しないでください !
// 問題なく使えるテキストエディタ
// (http://wpdocs.sourceforge.jp/Codex:%E8%AB%87%E8%A9%B1%E5%AE%A4 参照)
// を使用し、必ず UTF-8 の BOM なし (UTF-8N) で保存してください。

// ** MySQL 設定 - この情報はホスティング先から入手してください。 ** //
/** WordPress のためのデータベース名 */
define('DB_NAME', 'wordpress');

/** MySQL データベースのユーザー名 */
define('DB_USER', 'user');

/** MySQL データベースのパスワード */
define('DB_PASSWORD', 'password');

/** MySQL のホスト名 */
define('DB_HOST', 'mysql');

/** データベースのテーブルを作成する際のデータベースの文字セット */
define('DB_CHARSET', 'utf8mb4');

/** データベースの照合順序 (ほとんどの場合変更する必要はありません) */
define('DB_COLLATE', '');

/**#@+
 * 認証用ユニークキー
 *
 * それぞれを異なるユニーク (一意) な文字列に変更してください。
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org の秘密鍵サービス} で自動生成することもできます。
 * 後でいつでも変更して、既存のすべての cookie を無効にできます。これにより、すべてのユーザーを強制的に再ログインさせることになります。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '^Y]4b2!,01fmt5fb,>>M)Y<2>Cpj49YadW8Uoga+<A* }Tjn16%bdeLw#=^sU$>g');
define('SECURE_AUTH_KEY',  'Q5x>eXD>(>px7B|7zd?ah9j0=A|YXn30bN}FB`CM377?ou$87e1p9P>2|_NGt[%V');
define('LOGGED_IN_KEY',    '-~h~*F7v1|4i|[M|*skIe%tVrzJ&eho]w%i5me2`dOG7IN77np#q]rX.qaT4VlLI');
define('NONCE_KEY',        'r|&V2cWctV0251iJQoGOv3}`D~ywW8kRpL-R&Mx**;tbL(if@*[2f?r$T<0aL+3w');
define('AUTH_SALT',        'NSK]^SxrJwKR91J&njv=Wf-&O&#*@mtCz/M`!7Kg*`q_Vf}cHH7ZhDs$?v*lNq09');
define('SECURE_AUTH_SALT', '%X7-2VRqvBDfxR%rB{s4f2&KB6f[.}Mr*,Bp[Hae]o<Eo@LmTaHlaH5jAuJ~/%Z<');
define('LOGGED_IN_SALT',   '_tlte4XFU8q=Aw8!Zf0B}Cer|!IVilhlZ |z1Q(F W6kU@caO>DDF8[bE[euiSj@');
define('NONCE_SALT',       'pPu22<ON*d 8i8Y;AqtYUgJ{- 85a4@k4&m|i~R3%H;E#?nLjj0ek&>vT;,qG/ac');

/**#@-*/

/**
 * WordPress データベーステーブルの接頭辞
 *
 * それぞれにユニーク (一意) な接頭辞を与えることで一つのデータベースに複数の WordPress を
 * インストールすることができます。半角英数字と下線のみを使用してください。
 */
$table_prefix  = 'wp_nolo_';

/**
 * 開発者へ: WordPress デバッグモード
 *
 * この値を true にすると、開発中に注意 (notice) を表示します。
 * テーマおよびプラグインの開発者には、その開発環境においてこの WP_DEBUG を使用することを強く推奨します。
 */
define('WP_DEBUG', false);

#define('FORCE_SSL_ADMIN', true);
define('WP_CACHE', true);

/* 編集が必要なのはここまでです ! WordPress でブログをお楽しみください。 */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
