#!/usr/bin/env bash

ps aux | grep '/bin/console app:' | grep -v grep | awk '{print $2}' | xargs -r kill -9
