#!/bin/bash

redis-cli del log
redis-cli del percentage
redis-cli del message
redis-cli del files_processed
redis-cli del get number_of_important_urls
redis-cli del get number_important_urls_processed

rm /tmp/deploy.pid

# Kill all instances of current script to avoid double ups
kill -9 $(pgrep -f deploy.py)
kill -9 $(pgrep -f deploy.sh)
