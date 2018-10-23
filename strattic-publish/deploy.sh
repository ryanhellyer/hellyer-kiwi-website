#!/bin/bash

# Synchronises all static files from within the file system
function S3_Sync() {
	time_start=$(getTime)

	includes=''
	for ext in "${FILE_EXTENSIONS[@]}"; do
		includes=${includes}"--include \"*."$ext"\" "
	done

	/usr/local/bin/aws s3 sync --quiet --size-only "$Directory" s3://"$S3_Bucket" $includes

	Debug "S3 sync End. Total time: $(($(getTime)-time_start)) ms"
}

## Invalidation the CloudFront cache
## TODO: Send statistics? Configurable subject line?
function Invalidate_CloudFront() {
	time_start=$(getTime)

	ID=`sudo /usr/local/bin/aws cloudfront create-invalidation --distribution-id $CloudFront_ID --paths "/*" |grep Id|cut -d ":" -f 2|cut -d "\"" -f 2`
	( /usr/local/bin/aws cloudfront wait invalidation-completed --distribution-id $CloudFront_ID --id $ID ) &

	Debug "CF invalidation complete. Total time: $(($(getTime)-time_start)) ms"
}

## Send file to Amazon S3.
function Send_file_to_S3() {
	time_start=$(getTime)

	# If it's a web page, force content-type, otherwise just S3 guess it
	if [ "index.html" == "$File_Name" ]; then
		S3CacheHeader=""
		(/usr/local/bin/aws s3 cp --quiet $File_Path s3://$S3_Bucket$Dir_Path$File_Name $S3CacheHeader --no-guess-mime-type --content-type "$Content_Type") &
	else
		S3CacheHeader=" --cache-control max-age=604800 "
		(/usr/local/bin/aws s3 cp --quiet $File_Path s3://$S3_Bucket$Dir_Path$File_Name $S3CacheHeader ) &
	fi

	# TODO: use variables in this instead of two separate s3 calls as above
	#(/usr/local/bin/aws s3 cp --quiet $File_Path s3://$S3_Bucket$Dir_Path$File_Name $S3CacheHeader $Content_Type_Declaration) &

	Debug "S3 Single file cp End. Sent "$File_Path" to s3://"$S3_Bucket$Dir_Path$File_Name" . Total time: $(($(getTime)-time_start)) ms"
}

## Send file to Amazon S3.
function Send_redirect_to_S3() {
	time_start=$(getTime)

	### *************** THIS IS COMPLETELY UNTESTED CODE ***************
	(/usr/local/bin/aws s3 cp --quiet blank-redirect.html s3://"$S3_Bucket$Dir_Path"index.html --no-guess-mime-type --content-type "$Content_Type" --website-redirect "$Redirect_URL") &

	Debug "S3 Redirect file cp End. Sent redirect for "$Dir_Path" to S3. Total time: $(($(getTime)-time_start)) ms"
}

## Gets the HTTP Request for a URL.
## Includes page content, followed by request headers in {strattic} deliminated format.
## Uses delimited string to avoid doing multiple requests to the page and to simplify code parsing later.
function Get_HTTP_Request() {
	time_start=$(getTime)

	Delimiter="{{strattic}}"
	curl  -u $User:$Pass --silent --write-out "$Delimiter%{http_code}$Delimiter%{redirect_url}$Delimiter%{content_type}" $URL_to_get

	Debug "Get HTTP request to $URL_to_get. Total time: $(($(getTime)-time_start)) ms"
}

## Copies files from WordPress to the static location.
function Copy_Static_Files() {
	time_start=$(getTime)

	cd $Directory
	for ext in "${FILE_EXTENSIONS[@]}"; do
		find ./ -name "*.$ext" -exec cp --parents \{\} /tmp/$S3_Bucket \;
	done

	Debug "All media files copied to flattening dir. Total time: $(($(getTime)-time_start)) ms"
}

## Fetches the file content and saves it to disk.
function URL_Fetch() {
	time_start=$(getTime)

	URL_to_get=$Home_URL$Path

	# This loop checks against the EXTS array if the URL_to_get is a media/binary (non-html/php) file. It sets fetchfile to 1 if there's a match. bash needs in_array()
	fetchfile=0
	for ext in "${FILE_EXTENSIONS[@]}"; do
		if [[ $ext = ${URL_to_get##*.} ]]; then # ${URL_to_get##*.} matches everything after the last '.' (longest regex grab)
			((fetchfile++))	# double parens for arithmatic operation
			break
		fi
	done

	# If media/static file (if fetchfile is 0, then it's will be a static html file)
	if [ $fetchfile -gt 0 ]; then
		File_Name="${URL_to_get##*/}"

		# Get the paths for the media file
		URL_Dir=${URL_to_get/$File_Name/''}
		Dir_Path=${URL_Dir/$Home_URL/''}
		File_Path=$Directory$Dir_Path$File_Name

		## Here's where imgopt will go. The way its set up the image isn't being copied anywhere, rather the path of the original file in situ is being forwarded to Send_file_to_S3, which is publishing it.
		## We have to copy it anyway because we don't want to overwrite the original. We can't use convert because we need the filename of the original to be the actual filename, and the *original* to be -orig
		## Ideally we would use the WordPress $ID for the redis hash key. But, we're using $File_Path instead

		## Step 1: compressed_date=$(redis-cli hget image:$File_Path compressed_date | tr -d "/\"//");if [[ -z $compressed_date ]]; then #Step 2 #Step 3 #Step 5; else Debug "File compressed on ${compressed_date} #Step 4; fi
		## Step 2: cp $File_Path $File_Path-orig
		## Step 3: mogrify -quality (int) -resize '1260>'  $File_Path
		## Step 4: upload_date=$(redis-cli hget image:$File_Path upload_date | tr -d "/\"//"); if [[ redis_upload_date != file_ctime ]]; then #Step 2 #Step 3 #Step 5; fi
		## Step 5: redis-cli hmset image:$File_Path compressed_date $(date) upload_date $(date of file) orig_fsize $(fsize) comp_fsize $(cfsize)

   		Send_file_to_S3

	# If not a media file ...
	else

		# Get the HTTP Headers from the URL (also includes page content)
		HTTP_Headers=$(Get_HTTP_Request $URL_to_get)

		# Get the page content
		Page_Content=$(echo "$HTTP_Headers" | awk '{split($0,a,"{{strattic}}"); print a[1]}')

		# Rewrite URLs in page - TODO: make it handle home URL being in a sub-folder
		Page_Content=${Page_Content//$Home_URL/$CloudFront_URL}

		Escaped_Home_URL=${Home_URL//\//\\/}
		Escaped_CloudFront_URL=${CloudFront_URL//\//\\/}
		Page_Content=${Page_Content//$Escaped_Home_URL/$Escaped_CloudFront_URL}

		Home_Domain=${Home_URL//http:\/\/""}
		Home_Domain=${Home_Domain//https:\/\/""}
		CloudFront_Domain=${CloudFront_URL//http:\/\/""}
		CloudFront_Domain=${CloudFront_Domain//https:\/\/""}
		Page_Content=${Page_Content//$Home_Domain/$CloudFront_Domain}

		# Get the HTTP Status code (requires stripping of white space due to page content showing up as white space - it may be possible to simplify this by using something other than awk)
		# TODO: Needs to send 500 and 404 errors to CloudFront (can't be done from S3)
		HTTP_Status=$(echo "$HTTP_Headers" | awk '{split($0,a,"{{strattic}}"); print a[2]}')
		HTTP_Status_Code_No_Lead_Space="$(echo -e "${HTTP_Status}" | sed -e 's/^[[:space:]]*//')"
		HTTP_Status_Code_No_Trailing_Space="$(echo -e "${HTTP_Status_Code_No_Lead_Space}" | sed -e 's/[[:space:]]*$//')"
		HTTP_Status_Code=$(echo "$HTTP_Status_Code_No_Trailing_Space"|tr '\n' ' ')

		# Get the Content Type
		Content_Type_Raw=$(echo "$HTTP_Headers" | awk '{split($0,a,"{{strattic}}"); print a[4]}')
		Content_Type_No_Lead_Space="$(echo -e "${Content_Type_Raw}" | sed -e 's/^[[:space:]]*//')"
		Content_Type_No_Trailing_Space="$(echo -e "${Content_Type_No_Lead_Space}" | sed -e 's/[[:space:]]*$//')"
		Content_Type=$(echo "$Content_Type_No_Trailing_Space"|tr '\n' ' ')

		#TODO: Change to case/esac switch
		if [ $HTTP_Status = "200" ]; then

			# Create the directory for the index.html file
			Dir_Path=${URL_to_get/$Home_URL/''}
			Local_Path="/tmp/$S3_Bucket"$Dir_Path
			mkdir -p "$Local_Path" # -p stops it erroring out if dir already exists

			# Store HTML on disk
			File_Name="index.html"
			echo "$Page_Content" > "$Local_Path"index.html
			File_Path=$Local_Path$File_Name

			Send_file_to_S3

		elif [ $HTTP_Status = "302" ] || [ $HTTP_Status = "301" ]; then

			# Get the redirect URL
			Redirect_URL=$(echo "$HTTP_Headers" | awk '{split($0,a,"{{strattic}}"); print a[3]}')

			# Only process redirect if it's not redirecting to itself
			if [ $Redirect_URL != $URL_to_get ]; then

				File_Name="index.html"
				Dir_Path=${URL_to_get/$Home_URL/''}

				# Add trailing slash for S3
				length=${#Dir_Path}
				last_char=${Dir_Path:length-1:1}
				[[ $last_char != "/" ]] && Dir_Path="$Dir_Path/"; :

				Send_redirect_to_S3

				# If redirect URL is for current site, then make sure we fetch it (in case it wasn't found by the API)
				if [[ $Redirect_URL = "$Home_URL"* ]]; then
					Path=${Redirect_URL/$Home_URL/''}

					URL_Fetch
				fi

			fi

		fi

	fi

	# Store list of URLs (used by the admin interface to show which files have been transferred)
	urls=$(redis-cli get log;echo $URL_to_get)
	redis-cli set log "$urls"

	Debug "Fetch of $URL_to_get complete. Total time: $(($(getTime)-time_start)) ms" 
}

## Gets the WordPress API content.
function API_Get() {
	time_start=$(getTime)

	Path=$1
	URL_to_get=$Home_URL$Path

	wget -qO- --no-check-certificate --http-user=$User --http-password=$Pass -qx "$URL_to_get"; 

	Debug "WordPress API request complete. Total time: $(($(getTime)-time_start)) ms"
}

#####################################
# Utility functions.
#####################################
function getTime() {
	echo $(($(date +%s%N)/1000000))
}

function Debug() {
	if [ $Strattic_Debug = true ]; then
		echo [`date`] $1 >> /tmp/log
	fi
}

function get_Number_of_URLS() {

	Number_of_URLs=-1 # start from -1 as gain one due to the "end-first-stage" item
	#	List_of_URLS
	for Path in "${List_of_URLS[@]}"
	do

		if [ 'end-second-stage' == "$Path" ]; then
			second_stage_complete=true
		fi

		if [ true != "$second_stage_complete" ]; then
			((Number_of_URLs++))
		fi

		# or do whatever with individual element of the array
	done

}


function Send_Notification() {
## TODO: set email TO, Subject

	time_start=$(getTime)
	JSON=`jq -n --arg email "${ClientEmail}" --arg domain "$S3_Bucket" '{ domain: $domain, email: $email | split(",") }'`
	curl -k -H "Content-Type: application/json" -X POST -d "${JSON}" https://1mtlmyvc77.execute-api.us-east-1.amazonaws.com/PublishNotification &
	Debug "Publication Notification sent. Total time: $(($(getTime)-time_start)) ms"

}
#############################################################################
## Primary deployment function.
## Gets list of URLs from WordPress plugin and sends them off for processing.
function Deploy(){
	script_name=$0
	CloudFront_URL=$1
	CloudFront_ID=$2
	Home_URL=$3
	S3_Bucket=$4
	ClientEmail=$5
	User=$6
	Pass=$7
	Directory=$8
	Strattic_Debug=$9

	# This is an array of valid media extensions to be published.
	declare -a FILE_EXTENSIONS=("bmp" "css" "doc" "docx" "eot" "gif" "html" "ico" "jpeg" "jpg" "js" "json" "m4a" "md" "mp3" "mp4" "odp" "ods" "odt" "ogg" "otf" "patch" "pdf" "png" "ppt" "pptx" "rtf" "svg" "swf" "textile" "tif" "tiff" "ttf" "txt" "vtt" "wav" "webm" "woff" "woff2" "xls" "xlsx" "xml" "xsl")

	# Kill all instances of current script to avoid double ups - bash's $0 is the $script_name. Better to variableify so we can reuse script/ -o $$ says ignore *this* instance's PID.
	if pidof -o $$ -x $script_name >/dev/null; then
		echo "Process already running"
		exit 1
	fi

	# Flush Redis
	redis-cli set percentage 0
	redis-cli del log
	redis-cli set message "acquiring-urls"

	# Log when deployment was started
	deployment_start=$(getTime)
	Debug "Start of deploy.sh"
	total_files_syncd=0

	# Get list of URLs from site and loop through them one by one
	Dump_of_URLS=$(API_Get "/strattic-api/")
	List_of_URLS=($Dump_of_URLS) # Arrayify
	get_Number_of_URLS

	redis-cli set message "acquired-urls"

	for Path in "${List_of_URLS[@]}"
	do :

		# A path of "INVALIDATECACHE" can be injected 
		if [ 'end-first-stage' == "$Path" ]; then
			redis-cli set message "first-stage-complete"
			Invalidate_CloudFront
		elif [ 'end-second-stage' == "$Path" ]; then
			redis-cli set message "second-stage-complete"
			Invalidate_CloudFront
		elif [[ $Path = *[!\ ]* ]]; then # Checks that name is not empty

			URL_Fetch

			((total_files_syncd++))

		fi

		# Increment percentage counter now that file has been processd
		Percentage=$(echo "scale=4 ; $total_files_syncd*100 / $Number_of_URLs" | bc)

		Debug "Percentage: $Percentage total_files_syncd: $total_files_syncd Number_of_URLs: $Number_of_URLs"

		redis-cli set percentage $Percentage
		redis-cli set files_processed $total_files_syncd

	done

	redis-cli set message "third-stage-complete"

	Debug "Deployment of pages found via API complete. Number of files synced: $total_files_syncd. Total time: $(($(getTime)-deployment_start)) ms"

	# We invalidate at this point to make sure that new pages are available as quickly as possbile
	Invalidate_CloudFront

	# Final stage: Does a complete file sync of file system
	Copy_Static_Files
	S3_Sync
	Invalidate_CloudFront
	Send_Notification

	redis-cli set message "complete"

	Debug "Deployment completed. Total time: $(($(getTime)-deployment_start)) ms"
}

echo ""
echo "--- START OF DEPLOYMENT ---"

Deploy $1 $2 $3 $4 $5 $6 $7 $8 $9
exit

#bash deploy.sh   https://hellyer.kiwi            cloudfront_id   http://dev-hellyer.kiwi/unique-headers   s3_bucket      support@strattic.com    stratticusername  password                 /home/ryan/nginx/dev.hellyer.kiwi/public_html
#bash deploy.sh   https://d3fzlguiagg0jl.cloudfront.net   EPJ1ARFF81866    https://ryan.stratticstage.com           ww2.ryan.com   support@strattic.com    strattic          107f58dfc0ad95afcacf7b   /var/www/html    true
