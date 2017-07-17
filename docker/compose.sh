#!/bin/bash -e

PROJECT="fontstream"

if [ "$1" != "dev" -a "$1" != "test" -a "$1" != "prod" ]
then
    echo "./compose.sh (dev|test|prod) (start|stop|down)";
    exit;
fi
if [ "$2" != "start" -a "$2" != "stop" -a "$2" != "down"  -a "$2" != "restart" ]
then
    echo "./compose.sh (dev|test|prod) (start|stop|down|restart)";
    exit;
fi

MODE=$1
ACTION=$2
export MODE=$1
export ACTION=$2
export PROJECT=${PROJECT}

SCRIPT_DIR=$(cd $(dirname $(readlink $0 || echo $0));pwd)
cd $SCRIPT_DIR
export SCRIPT_DIR=$SCRIPT_DIR
export WP_DIR=$SCRIPT_DIR/../wordpress

if [ "$ACTION" = "start" ]
then
    docker-compose -f docker-compose.$MODE.yml -p ${PROJECT}${MODE} up -d
elif [ "$ACTION" = "stop" ]
then
    docker-compose -f docker-compose.$MODE.yml -p ${PROJECT}${MODE} stop
elif [ "$ACTION" = "restart" ]
then
    docker-compose -f docker-compose.$MODE.yml -p ${PROJECT}${MODE} restart
elif [ "$ACTION" = "down" ]
then
    docker-compose -f docker-compose.$MODE.yml -p ${PROJECT}${MODE} down
fi
echo $ACTION
