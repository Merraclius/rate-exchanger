#!/bin/sh
if [ -n "$DYNO" ]  && [ -n "$ENV" ]; then
    php init --overwrite=All
    php yii cache/flush-all
    php yii cache/flush-schema --interactive=0
fi

mkdir -p ./web/assets;
mkdir -p ./runtime;
chmod 0777 ./web/assets;