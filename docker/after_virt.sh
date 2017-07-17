#!/bin/bash
#このファイルはコンテナ作成時にしか実行されないはず。

sed -i -e 's/\(access_log off;\)/\1\n\t\texpires 10d;/g' /etc/nginx/conf.d/${PROFILE}_ssl.conf
sed -i -e 's/\(access_log off;\)/\1\n\t\texpires 10d;/g' /etc/nginx/conf.d/${PROFILE}_http.conf
sed -i -e 's/\(allow 0.0.0.0\/0;\)/#\1/g' /etc/nginx/conf.d/${PROFILE}_http.conf
