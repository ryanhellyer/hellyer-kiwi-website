#!/bin/bash

function QueryAPI(){
  page=1
  unset hreq
  hreq="https://$1?per_page=100&page=$page"
  hres=`HCurl $Stage $hreq &`
  echo "$HCurl $hreq" >> /tmp/log
  #echo "$hres" >> /tmp/log

  TPages=`echo -n "$hres"|grep "X-WP-TotalPages:"|cut -d " " -f 2|tr -d '\r'`
  while [ $page -le $TPages ]; do
    n=0
    end=0
    unset req res
    req="https://$1?per_page=100&page=$page"
    res=`Curl $Stage $req &`
    echo "Curl: $Curl $Stage $req" >> /tmp/log
    #echo "Res: $res"
    while [ "$end" != 1 ]; do  
      link=`echo $res|jq ".[$n].link"`; 
      modified=`echo $res|jq ".[$n].modified"`; 
      #remove surrounding quotes
      link=`sed -e 's/^"//' -e 's/"$//' <<<"$link"`
      modified=`sed -e 's/^"//' -e 's/"$//' <<<"$modified"`

      #if [ "$link" == "null" ]; then
      if [ -z "$link" ] || [ "$link" == "null" ]; then 
        end=1; 
        page=$((page+1)); 
      else
        echo "Link: $link" >> /tmp/log
        LCLink=`echo "${link: -1}"`
        # Removed & to force this one first
        if [[ $link != *$Stage* ]]; then
          link=https://$Stage$link
        fi
        if [ "$LCLink" != "/" ] && [ "$link" != "/" ]; then
          Wget $Stage "$link/" 
          Wget $Stage $link"/feed/" &
        else
          Wget $Stage $link 
          Wget $Stage $link"/feed/" &
        fi
        if [[ $1 = *"posts"* ]] || [[ $1 = *"pages"* ]]; then
          id=`echo $res|jq ".[$n].id"`; 
          #BuildSearch $id $link
        fi
      fi;
      n=$((n+1)); 
    done
  done
}

function QueryRobots(){
  cd /tmp
  req="https://$1/robots.txt"
  #res=`Curl $Stage $req`
  Wget $Stage $req &
  mv /tmp/$IP/* /tmp/$Stage/
}

function QueryYoast(){
  req="https://$1"
  res=`HCurl $Stage $req`
  echo $res|grep "404 Not Found" >/dev/null
  if [ $? -ne 0 ]; then
    # Yoast sitemap exists
    #echo "Checking $req"
    res=`Curl $Stage $req`
    Wget $Stage https://$IP/main-sitemap.xsl &
    Wget $Stage $req &
    
    # Parse sitemap_index.xml and grab those files
    maps=`echo $res|xmlstarlet sel -N my="http://www.sitemaps.org/schemas/sitemap/0.9" -n -t -v '//my:sitemap/my:loc'`
    for i in `echo $maps`; do
      #echo "Grabbing " $i
      Wget $Stage $i &
    done 
  fi
}

function Query404(){
  rand=`openssl rand -hex 16`
  req="https://$1/$rand"
  OCurl $Stage $req "404.html"
  error=`cat 404.html|md5sum |cut -d " " -f 1`
  mv 404.html /tmp/$Stage
}

function QueryFavicon(){
  Wget $Stage https://$1/favicon.ico 
  mv /tmp/$1/favicon.ico /tmp/$Stage
}

function HCurl() { 
  curl -Lks -u $User:$Pass -H "Host: $1" -I "$2";
  echo "curl -ks -u $User:$Pass -H \"Host: $1\" -I \"$2\"" >> /tmp/log 
}

function Curl() { 
  curl -Lks -u $User:$Pass -H "Host: $1" "$2";
  echo "curl -ks -u $User:$Pass -H \"Host: $1\" \"$2\"" >> /tmp/log 
}

function OCurl() { 
  curl -Lks -u $User:$Pass -o "$3" -H "Host: $1" "$2";
  echo "curl -ks -u $User:$Pass -o \"$3\" -H \"Host: $1\" \"$2\"" >> /tmp/log;
}

function GetIP() {
  ifconfig |grep "inet addr" |grep Bcast|cut -d ":" -f 2|cut -d " " -f 1
}

function Wget() {
  wget --no-check-certificate --http-user=$User --http-password=$Pass --header "Host: $1" -qx "$2"; 
  echo "wget --no-check-certificate --http-user=$User --http-password=$Pass --header \"Host: $1\" -qx \"$2\"" >> /tmp/log; 
}

function EIP() {
  curl -s ipinfo.io|grep \"ip\"|cut -d "\"" -f 4
}

function FixJSON() {
  # Fix sData to proper JSON
  sed -i '1 i\var data = [' /tmp/$Stage/sData.js
  sed -i '$ s/,$//' /tmp/$Stage/sData.js
  cat /tmp/suffix >> /tmp/$Stage/sData.js
  # Copy over relevant search files
  cp /tmp/fuse.min.js /tmp/$Stage
  cp /tmp/search.html /tmp/$Stage
  cp /tmp/search.js /tmp/$Stage
}

function S3() { 
  #FixJSON
  /bin/cp -R /tmp/$IP/* /tmp/$Stage/
  /bin/cp -R /var/www/html/wp-content /tmp/$Stage
  /bin/cp -R /var/www/html/wp-includes /tmp/$Stage
  cd /tmp/$Stage
  rpl -R "/$Stage" "/$Host" *
  rpl -R "http://$Host" "https://$Host" *
  rpl -R "http://fonts.googleapis.com" "https://fonts.googleapis.com" *
  rpl -R "http://fonts.gstatic.com" "https://fonts.gstatic.com" *
  rpl -R "http://cdn-images.mailchimp.com" "https://cdn-images.mailchimp.com" *
  #InsertCustomRPL

  #Sync Data
  sudo /usr/local/bin/aws s3 sync --delete /tmp/$Stage s3://$Bucket --exclude "*" --include robots.txt --include "*.css" --include "*.js" --include "*.png" --include "*.svg" --include "*.jpeg" --include "*.gif" --include "*.xml" --include "*.html" --include "*.ttf" --include "*.woff" --include "*.woff2" --include "*.jpg" --include "*.eot" --include "*.xsl" --include "*.mp4"  --include "*.ico" --include "*.pdf" --include "*.json"

  cd /tmp/$Stage
  for i in `find . -name "index.html" -type f -exec grep '<?xml' {} +|cut -d ":" -f 1|sed -e "s,^./,,"`; do
    echo "Syncing feed: $i" >> /tmp/log 
    /usr/local/bin/aws s3 cp $i s3://$Bucket/$i --no-guess-mime-type --content-type "application/rss+xml"
  done

  #Sync images
  for i in `find . -regex ".*\.\(jpg\|gif\|png\|jpeg\)" |cut -d ":" -f 1|sed -e "s,^./,,"|sort -u`; do
    echo "Syncing image: $i" >> /tmp/slog
    /usr/local/bin/aws s3 cp $i s3://$Bucket/$i --cache-control max-age=604800;
  done
}

function CF() {
  JSON=`jq -n --arg email "${ClientEmail}" --arg domain "$Bucket" '{ domain: $domain, email: $email | split(",") }'`
  ID=`sudo /usr/local/bin/aws cloudfront create-invalidation --distribution-id $CFID --paths "/*" |grep Id|cut -d ":" -f 2|cut -d "\"" -f 2`
  sudo /usr/local/bin/aws cloudfront wait invalidation-completed --distribution-id $CFID --id $ID && curl -k -H "Content-Type: application/json" -X POST -d "${JSON}" https://1mtlmyvc77.execute-api.us-east-1.amazonaws.com/PublishNotification
  date >> /tmp/log
}

function main(){
  Host=$1
  CFID=$2
  Stage=$3
  Bucket=$4
  ClientEmail=$5
  User="strattic"
  Pass="$6"
  killall -9 stage1.sh
  IP=`/sbin/ifconfig |grep "inet addr" |grep Bcast|cut -d ":" -f 2|cut -d " " -f 1`
  cd /tmp/
  \rm -rf /tmp/$Stage
  \rm -rf /tmp/$IP
  mv /tmp/log /tmp/log~
  date >> /tmp/log
  Posts="/wp-json/strattic/v1/everything?important=false"
  AllPosts="/wp-json/strattic/v1/everything?important=true"
  Robots="/robots.txt"
  Yoast="/sitemap_index.xml"

  #QueryDateArchives
  QueryAPI $IP$Posts
  QueryAPI $IP$AllPosts
  QueryRobots $IP
  QueryYoast $IP$Yoast 
  QueryFavicon $IP
  Query404 $Stage
  S3
  CF 
}
main $1 $2 $3 $4 $5 $6
