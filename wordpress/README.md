# kusanagi on dockerの設定方法
xxxxxxxx用に最適化してありますが、他でもそんなに手をかけずに使えるようにしてあります。
## デフォルトからの変更内容

* kusanagi-php7
    * GDでjpegが使えなかったので使えるようにした。
    * www-dataのuidを1001に変更
    * mysqlのクライアントをインストール。mariaDB 10.1
* kusanagi-nginx
    * 画像のexpiresを10日間に
    * ホストのファイル共有時の問題
    kusanagiはコンテナを立ち上げるときに`docker-entrypoint.sh` -> `/usr/lib/kusanagi/libvirt.sh`
    を実行してその中でWP最新版をダウンロードしている。WPLANG=jaだと日本語版をDLする。
    解凍して
    
    ```
    mv ./wordpress/* /home/kusanagi/$PROFILE/DocumentRoot
    ```
    
    を実行するがこのときHostのディレクトリへマッピングしているとコピーができないので下記をDockerfile-nginxで付け加えている。
    
    ```
    mv ./wordpress/wp-content/languages /home/kusanagi/$PROFILE/DocumentRoot/wp-content/
    ```

## 開発環境(dev)
開発環境はdocker for macで動くようにしてあります。他の環境の場合は・・・頑張って！
dockerは17.06以上で。

開発環境はhaproxyを入れることにより下記の利点がある

* SSLの設定を新たにすることなく使える（ただしオレオレ）
* また、ポートをつけなくても良くなる。
* ALBやCloudFrontと同じ環境を作りやすい。


### haproxy

1. 下記URLにあるhaproxyのgitリポジトリをcloneする。
    - https://pm1932.backlog.jp/settings/git/DEVINFO/list
1. cloneしたディレクトリに移動して下記コマンドを実行すれば終わり。

```bash
docker-compose up -d
```

リトレンゴ用の設定はしてあるけれどもその他はhaproxy.cfgをみて設定して下さい。そんなに難しくはないはず。

### kusanagi版xxxxxxxx

git clone pm1932@pm1932.git.backlog.jp:/xxxxxxxx/xxxxxxxx.git
mv xxxxxxxx xxxxxxxx_dev
cd xxxxxxxx_dev
./compose.sh dev start



## ステージング環境(test)
amazon linuxで動作確認しています。
amazon linuxで動作確認しています。


```
sudo su -
yum -y install git docker
curl -L https://github.com/docker/compose/releases/download/1.13.0/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
chmod 755 /usr/local/bin/docker-compose
/etc/init.d/docker start
chkconfig docker on
useradd -u 1001 www-data
groupadd -g 1001 www-data
usermod -a -G docker www-data

sudo su - xxxxxxxx

ssh-keygen -t rsa -b 4096
#公開鍵をbacklogのxxxxxxxx@gitユーザーに登録

git clone pm1932@pm1932.git.backlog.jp:/xxxxxxxx/xxxxxxxx.git
mv xxxxxxxx xxxxxxxx_test
cd xxxxxxxx_test
./compose.sh test start

```

### cloudfront
/wp-content/uploads以下をキャッシュするようにしている。
オリジンを一つ設定して、あとはbehaviorsでパスによってキャッシュするしないを設定している。
キャッシュにヒットしているかどうかは下記参照

http://qiita.com/ne_ko_ka/items/b4514402df65b6b1a7ff

ACMでSSL証明書をつけてtest.xxxxxxxx.comのゾーン情報をcloudfrontのアドレスにすればみられるはず。

### 備考

画像は旧サーバーとrsyncするようにしてある。
DBはmysql5.7にしてある。

## 商用環境(prod)
ステージング環境とほぼ同じでtestをprodにしてコマンドを実行すれば良いだけ。
ただし、DBはRDSに繋がるように設定すること。既存のcloudfrontは設定を変更すること。