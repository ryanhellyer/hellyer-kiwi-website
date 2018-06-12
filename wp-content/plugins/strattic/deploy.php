<?php
// jaz 20180410
$target = "d3omgf3l2n6v1m.cloudfront.net";
$cfid = "E371SCCJG17AB7";
$stage = "waterfall.stratticstage.com";
$bucket = "www.waterfall-security.com";
$email = "support@strattic.com";
$pass = "622f92442cba5205941832";
$output = shell_exec("flock -n /tmp/lock /usr/local/bin/spider.sh $target $cfid $stage $bucket $email $pass ");
