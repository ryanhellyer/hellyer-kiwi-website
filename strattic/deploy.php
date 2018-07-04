<?php

$command = '/tmp/lock1 /usr/local/bin/spider.sh ' . STRATTIC_CLOUDFRONT_DOMAIN . ' ' . STRATTIC_CLOUDFRONT_ID . ' ' . STRATTIC_STAGE_DOMAIN . ' ' . STRATTIC_S3_BUCKET . ' ' . STRATTIC_EMAIL . ' ' . STRATTIC_PASSWORD;

$output = shell_exec( 'flock -n 4 ' . $command );
echo $command;

die;
