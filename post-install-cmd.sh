#!/bin/sh
if [ -n "$DYNO" ]  && [ -n "$ENV" ]; then
    php init --overwrite=All
    php yii cache/flush-all
    php yii cache/flush-schema --interactive=0
fi