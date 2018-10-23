#!/usr/bin/env python3.5
# -*- coding: utf-8 -*-

import argparse
import boto3
import httplib2
import json
import os
import re
import redis
import shutil
import subprocess
import sys
import time
import traceback
from urllib.parse import urlparse
from datetime import datetime
from multiprocessing import Pool

# Declare Globals
#
# @param array 	FILE_EXTENSIONS
# @param list	args
# @param object	r			redis log connector
# @param object AWS_Session 	AWS Session connector
# @param list	matches			List of filenames matching FILE_EXTENSIONS

VERSION="1.1.0"
FILE_EXTENSIONS=["bmp","css","doc","docx","eot","gif","html","ico","jpeg","jpg","js","json","m4a","md","mp3","mp4","odp","ods","odt","ogg","otf","patch","pdf","png","ppt","pptx","rtf","svg","swf","textile","tif","tiff","ttf","txt","vtt","wav","webm","woff","woff2","xls","xlsx","xml","xsl"]
parser = argparse.ArgumentParser(description='Process deployment request.')
parser.add_argument('--cfurl','-c' , nargs='?', help='AWS CloudFront URL')
parser.add_argument('--cfid','-f' , nargs='?', help='AWS CloudFront ID')
parser.add_argument('--clientemail','-m' , nargs='?', help='Client Email')
parser.add_argument('--debug', help='Turn on debugging',action='store_true')
parser.add_argument('--dir','-d' , nargs='?', help='Directory')
parser.add_argument('--homeurl','-a' , nargs='?', help='Home URL')
parser.add_argument('--password','-p' , nargs='?', help='Password')
parser.add_argument('--s3bucket','-s' , nargs='?', help='S3 Bucket')
parser.add_argument('--username','-u' , nargs='?', help='Username')
parser.add_argument('--version','-v', help='Show version and exit',action='store_true')

args = parser.parse_args()
if(args.version is True):
	print(VERSION)
	sys.exit()
script_name=os.path.basename(__file__)
r = redis.StrictRedis(host='localhost', port=6379, db=0)
AWS_Session = boto3.session.Session()
matches = []
os.environ["AWS_SHARED_CREDENTIALS_FILE"] = "/var/www/.aws/credentials"
log = "/tmp/log"
pidfile = "/tmp/deploy.pid"

# Debug logger.
# Outputs debug information to both the console and to a logfile.
#
# @param string  statement   The statement to out to the debug log
# TODO: change to loggging module
def Debug(statement):
	global log, Strattic_Debug
	timestp = str(datetime.fromtimestamp(time.time()).strftime('%Y-%m-%d-%H-%M-%S'))
	if Strattic_Debug is True :
		print("["+timestp+"] "+statement)
	ansi_escape = re.compile(r'\x1b[^m]*m')
	statement = ansi_escape.sub('', statement)
	with open(log,'a') as logfile:
		logfile.write("["+timestp+"] "+statement+"\n")

# Make a directory.
# Makes a new directory.
# Function namei is mapped from the mkdir -p Bash command.
#
# @param string  path   The new directory path
def mkdir_p(path):
	try:
		os.makedirs(path)
	except OSError as exc:  # Python >2.5
		if os.path.isdir(path):
			pass
# Deletes a directory tree.
# Function similar to rm -rf <path>
#
# @param string  path   The new directory path
# @param bool ignerr	Boolean value whether to ignore errors
def rmtree(path,ignerr):
	shutil.rmtree(path,ignore_errors=ignerr)
	
# Colors used for styling console output.
# @reference	url	https://en.wikipedia.org/wiki/ANSI_escape_code#Colors
class color:
	DKRED     = '\033[38;5;1m'
	BGRED     = '\033[48;5;1m'
	DKGREEN   = '\033[38;5;2m'
	BGDKGREEN = '\033[48;5;2m'
	DKYELLOW  = '\033[38;5;3m'
	BGDKYELLOW= '\033[48;5;3m'
	DKBLUE    = '\033[38;5;4m'
	BGDKBLUE  = '\033[48;5;4m'
	DKMAGENTA = '\033[38;5;5m'
	DKCYAN    = '\033[38;5;6m'
	ORANGE    = '\033[38;5;214m'
	BGORANGE  = '\033[48;5;214m'
	RED       = '\033[38;5;9m'
	GREEN     = '\033[38;5;10m'
	YELLOW    = '\033[38;5;11m'
	BLUE      = '\033[38;5;12m'
	MAGENTA   = '\033[38;5;13m'
	CYAN      = '\033[38;5;14m'
	VIOLET    = '\033[38;5;91m'
	BOLD      = '\033[1m'
	UNDERLINE = '\033[4m'
	END       = '\033[0m'



# Send notification.
# Sends notification email saying that the publication is complete.
#
# @todo   move away from global variables
def Send_Notification(Client_Email,S3_Bucket):
	time_start=time.time()
	notification_vars = '{\"domain\":\"'+S3_Bucket+'\",\"email\":[\"'+Client_Email+'\"]}'
	res=subprocess.Popen(["curl -k -H \"Content-Type: application/json\" -X POST -d '"+notification_vars+"' https://1mtlmyvc77.execute-api.us-east-1.amazonaws.com/PublishNotification"], stdout=subprocess.PIPE,stderr=subprocess.STDOUT, shell=True).communicate()[0]
	Debug("Publication Notification sent. Total time: "+str(float(time.time())-float(time_start))+" seconds")

# Amazon S3 Sync.
# Synchronises the file system between staging and S3.
# This is used as a backstop in case of errors or missed files earlier on.
#
# @todo   move away from global variables
def S3_Sync():
	global Directory,S3_Bucket
	time_start=time.time()
	includes=''
	for ext in FILE_EXTENSIONS:
		includes = includes + "--include \"*."+ext+"\" "
	#M# Interesting suckiness. boto3 doesn't support sync. So this stays as is for now
	subprocess.Popen(["aws s3 sync --quiet --delete --size-only "+Directory+" s3://"+S3_Bucket+" "+includes], stdout=subprocess.PIPE,stderr=subprocess.STDOUT, shell=True)
	Debug("S3 Sync Subprocess started.")


# Invalidation of the CloudFront cache.
#
# @todo   Send statistics? Configurable subject line?
# @todo   move away from global variables
def Invalidate_CloudFront(wait=False):
	global AWS_Session,CloudFront_ID
	AWS_CF_client = AWS_Session.client('cloudfront')
	time_start=time.time()
	try:
		result = AWS_CF_client.create_invalidation(DistributionId=CloudFront_ID, InvalidationBatch={'Paths':{'Quantity':1,'Items':['/*']},'CallerReference':str(time.time())})
		ID = result["Invalidation"]["Id"]
	except Exception as e:
		print("CloudFront Create Invalidation Error")
		r.set('error','createCFInvalidationError')
		print(e)
		exit()
	if wait is True:
		try:
			Debug("CloudFront Invalidation waiting started.")
			CF_Wait = AWS_CF_client.get_waiter('invalidation_completed')
			CF_Wait.wait(DistributionId=CloudFront_ID,Id=ID)
			Debug("CF invalidation complete. Total time: "+str(float(time.time())-float(time_start))+" seconds")
		except Exception as e:
			print("Invalidation wait error")
			r.set('error','waitInvalidationError')
			print(e)
			exit()

# Copy the static files.
# Copies all static files from the staging sites folder to the temporary folder, ready for syncing with S3.
# @global matches	list	Collects files that end with FILE_EXTENSIONS extension
# @todo   move away from global variables
def Copy_Static_Files():
	global Directory,S3_Bucket
	time_start=time.time()
	try:
		for root, dirnames, filenames in os.walk(Directory):
			for filename in filenames:
				# Shorthand loop that checks filesname against matching FILE_EXTENSIONS
				matching = [ext for ext in FILE_EXTENSIONS if filename.endswith(ext)]
				if matching is not None:
					matches.append(os.path.join(root, filename))
	except Exception as e:
		Debug("Filesystem walk error")
		r.set('error','fsWalkError')
		print(e)
		exit()
	try:
		for f in matches:
			if os.path.isfile(f):
				frep = f.replace(Directory+"/",'')
				mkdir_p("/tmp/"+S3_Bucket+"/"+os.path.dirname(frep))
				shutil.copy2(f, "/tmp/"+S3_Bucket+"/"+frep)
	except Exception as e:
		Debug("File Copy error")
		r.set('error','fileCopyError')
		print(e)
		exit()


# Creates Secure HTTPAuth requestor And HTTPRedirect handler
# @param 	string	URL_to_get
# @return	object	opener
def Get_Opener(URL_to_get,User,Pass):
	try:
		http_conn = httplib2.Http()
		http_conn.follow_redirects = False
		http_conn.add_credentials(User,Pass)
		http_response, http_content = http_conn.request(URL_to_get)
		return [http_response, http_content]
	except Exception as e:
		Debug("Get_Opener failed")
		r.set('error','getOpenerError')
		print(e)
		exit()		

# Gets the Strattic plugin API request
#
# @param string  path   The API request path
#
# @todo   move away from global variables
# @todo   We can hard code the path in here
def API_Get(Path):
	global Home_URL,User,Pass,URL_to_get
	time_start=time.time()
	URL_to_get=Home_URL+Path
	http_response, http_content = Get_Opener(URL_to_get,User,Pass) 
	Debug("WordPress API request complete. Total time: "+str(float(time.time())-float(time_start))+" seconds")
	return http_content.decode('utf-8')


# Send a file to Amazon S3.
#
# @param string  File_Path     Path to the file
# @param string  File_Name     Name of the file
# @param string  Dir_Path      Directory path to use
# @param string  Content_Type  File HTTP content type
#
# @todo   move away from global variables
def Send_file_to_S3(File_Path,File_Name,Dir_Path,Content_Type):
	global S3_Bucket
	#Debug(color.RED+"Trying to send file to s3://"+S3_Bucket+Dir_Path+File_Name+color.END);
	time_start=time.time()
	S3CacheHeader = ''
	MimeTypeFlags = ''
	# If it's a web page, force content-type, otherwise just S3 guess it
	if "index.html" == File_Name:
		MimeTypeFlags = "--no-guess-mime-type --content-type \""+Content_Type+"\""
		Cache_Control=''
	else:
		File_Name=''
		S3CacheHeader=" --cache-control \"max-age=604800\" "
		Cache_Control="max-age:604800"
		
	#Debug(color.RED+"Almost there ... s3://"+S3_Bucket+Dir_Path+File_Name+color.END);
	try:
		#Debug(color.RED+"We are in ... s3://"+S3_Bucket+Dir_Path+File_Name+color.END);
		#AWS_S3_client = AWS_Session.resource('s3')
		#AWSobj = AWS_S3_client.Object(S3_Bucket,Dir_Path+File_Name)
		#AWSobj.put(Body=File_Path,ContentType=Content_Type,CacheControl=Cache_Control)
		
		#With --quiet set, no errors are recorded
		if not Dir_Path.endswith("/"):
			Dir_Path = Dir_Path+"/"
		Dir_Path = Dir_Path.replace("//","/")
		assert os.path.isfile(File_Path)
		s3cpcmd = "aws s3 cp "+File_Path+" s3://"+S3_Bucket+Dir_Path+File_Name+" "+S3CacheHeader+MimeTypeFlags
		#Debug(s3cpcmd)
		res = subprocess.Popen([s3cpcmd], stdout=subprocess.PIPE,stderr=subprocess.STDOUT, shell=True).communicate()[0]
		#Debug(str(res))
		Debug("S3 Single file cp End. Sent "+File_Path+" to s3://"+S3_Bucket+Dir_Path+File_Name+" . Total time: "+str(float(time.time())-float(time_start))+" seconds")
	except AssertionError as ae:
		Debug(File_Path+ " does not exist.")
		Debug(str(ae))
		exit()
	except Exception as e:
		print("AWS S3 cp failed for "+File_Path)
		print(str(e))
		r.set('error','s3CpError')
		exit()
	


# Send a redirect to Amazon S3.
# Redirects are handled differently from normal file uploads.
#
# @param string  File_Path     Path to the file
# @param string  File_Name     Name of the file
# @param string  Dir_Path      Directory path to use
# @param string  Content_Type  File HTTP content type
# @param string  Redirect_URL  URL to redirect to
def Send_redirect_to_S3(File_Path,File_Name,Dir_Path,Content_Type,Redirect_URL):
	time_start=time.time()
	try:
	### *************** THIS IS COMPLETELY UNTESTED CODE ***************
		File_Name="index.html"
		#AWS_S3_client = AWS_Session.resource('s3')
		#AWSobj = AWS_S3_client.Object(S3_Bucket,Dir_Path+File_Name)
		#AWSobj.put(Body=File_Path,ContentType=Content_Type,CacheControl=Cache_Control)
		Path_to_Blank_Redirect = os.path.dirname(os.path.realpath(__file__))
		if not Dir_Path.endswith("/"):
			Dir_Path = Dir_Path+"/"
		Dir_Path = Dir_Path.replace("//","/")
		#assert os.path.isfile(Path_to_Blank_Redirect+"/blank-redirect.html")
		
		res=subprocess.Popen(["aws s3 cp "+Path_to_Blank_Redirect+"/blank-redirect.html s3://"+S3_Bucket+Dir_Path+"index.html --no-guess-mime-type --content-type \""+Content_Type+"\" --website-redirect \""+Redirect_URL+"\""], stdout=subprocess.PIPE,stderr=subprocess.STDOUT, shell=True).communicate()[0]
		#Debug(res)
		Debug("S3 Redirect file cp End. Sent redirect for s3://"+S3_Bucket+Dir_Path+"index.html to S3 with redirect "+Redirect_URL+". Total time: "+str(float(time.time())-float(time_start))+" seconds")
	except AssertionError as ae:
		Debug("Could not find blank-redirect.html")
		print(str(ae))
		exit()
	except Exception as e:
		Debug("Send redirect to S3 failed.")
		print(e)
		exit()


# Fetch a URL.
#
# @param   string  Path            The path to be fetched
# @param   string  Home_URL        Home URL of the site
# @param   string  Dir_Path        The directory path without the filename
# @param   string  CloudFront_URL  CloudFront URL
# @param   string  User            User for HTTP authentication
# @param   string  Pass            Pass for HTTP authentication
# @param   string  URL_to_get      URL to get
# @param   string  Directory       The base filesystem directory
# @param   string  S3_Bucket       Amazon S3 bucket
# @param   bool    important_file  true if file is considered important
# @return  string  The URL to get

def URL_Fetch(Path,Home_URL,CloudFront_URL,User,Pass,URL_to_get,Directory,S3_Bucket,important_file):
	time_start=time.time()
	#Debug(color.YELLOW+Home_URL+color.END)
	#Debug(color.YELLOW+Path+color.END)
	if not Path.startswith("/"):
		Path = "/"+Path
	Path = Path.replace("//","/")
		
	URL_to_get=Home_URL+Path
	#Debug(color.CYAN+URL_to_get+color.END)

	# If this is listed as an important file, then increment the counter (used for calculating the percentage completed)
	if important_file is True:
		Number_of_Important_URLs_Processed=r.incr('number_important_urls_processed')

	try:
		fetched = r.sismember('log',URL_to_get)
		if fetched is True:
			Debug(URL_to_get+" already fetched (URL_Fetch)")
			return
		else:
			r.sadd('log',URL_to_get)
	except Exception as e:
		Debug("Redis URL log fetch fail")
		r.set('error','redisLogFetchFail')
		print(e)
		exit()

	# This loop checks against the EXTS array if the URL_to_get is a media/binary (non-html/php) file. It sets Is_Media_File to True if there's a match.	
	Is_Media_File = False
	try:
		for ext in FILE_EXTENSIONS:
			if URL_to_get.endswith(ext):
				Is_Media_File = True
				#break

		if Is_Media_File is True: # and os.path.isdir(File_Path):
			try:
				Dir_Path = urlparse(URL_to_get).path
				File_Name = os.path.basename(Dir_Path)
				File_Path=Directory+Dir_Path
				#File_Path = Dir_Path+File_Name
				HTTP_Response, HTTP_Content = Get_Opener(URL_to_get,User,Pass) 
				Parse_Home_URL = urlparse(Home_URL).netloc
				Parse_CloudFront_URL = urlparse(CloudFront_URL).netloc
				HTTP_Content = HTTP_Content.replace(bytes(Parse_Home_URL,encoding='utf-8'),bytes(Parse_CloudFront_URL,encoding='utf-8'))
				#Debug(">>Dir_Path>" + Dir_Path)
				#Debug(">>S3_Bucket>" + S3_Bucket)
				#Debug(">>File_Path>" + File_Path)
				AWS_S3_client = AWS_Session.client('s3')
				AWS_S3_client.put_object(Body=HTTP_Content, Bucket=str(S3_Bucket), Key=str(Dir_Path).strip("/"), ContentType=str(HTTP_Response['content-type']), ContentLength=int(HTTP_Response['content-length']), ACL='public-read')
				## with HTTP_Content the bytestream of the media file we can manipulate it without saving to file
				## Step 3: mogrify -quality (int) -resize '1260>'  $File_Path
				## Step 4: upload_date=$(redis-cli hget image:$File_Path upload_date | tr -d "/\"//"); if [[ redis_upload_date != file_ctime ]]; then #Step 2 #Step 3 #Step 5; fi
				## Step 5: redis-cli hmset image:$File_Path compressed_date $(date) upload_date $(date of file) orig_fsize $(fsize) comp_fsize $(cfsize)
				
				#assert os.path.isfile(File_Path)
				Debug("Sent Media file "+Dir_Path+" to S3.")
				#Send_file_to_S3(File_Path,File_Name,Dir_Path,'')
			except AssertionError as ae:
				Debug("Could not find file "+File_Path)
				Debug(str(ae))
				exit()
			except Exception as e:
				Debug("Sending media file failed")
				r.set('error','sendMediaError')
				print(e)
				exit()

		# If not a media file ...
		else:
			# Get the HTTP response from the URL (also includes page content)
			try:
				HTTP_Response, HTTP_Content = Get_Opener(URL_to_get,User,Pass) 
				HTTP_Content = HTTP_Content.decode('utf-8')
			except Exception as e:
				Debug("Failed to get response from server")
				Debug(URL_to_get)
				r.set('error','httpResponseError')
				print(e)
				exit()
			try:
				HTTP_Status = 0
				if HTTP_Response is not None:
					Parse_Home_URL = urlparse(Home_URL).netloc
					Parse_CloudFront_URL = urlparse(CloudFront_URL).netloc
					HTTP_Content = HTTP_Content.replace(Parse_Home_URL,Parse_CloudFront_URL)
					
					Escaped_Home_URL = Home_URL.replace("\\",'\\\\')
					Escaped_CloudFront_URL = CloudFront_URL.replace("\\","\\\\")
					HTTP_Content = HTTP_Content.replace(Escaped_Home_URL,Escaped_CloudFront_URL)
					
					# What is this meant to do?
					Home_Domain = Home_URL.replace("http:\/\/","")
					Home_Domain = Home_Domain.replace("https:\/\/","")
					CloudFront_Domain = CloudFront_URL.replace("http:\/\/","")
					CloudFront_Domain = CloudFront_Domain.replace("https:\/\/","")
					HTTP_Content = HTTP_Content.replace(Home_Domain,CloudFront_Domain)
					
					HTTP_Status = HTTP_Response.status
					Content_Type = HTTP_Response["content-type"]
				
			except Exception as e:
				Debug("Getting HTTP content from server failed")
				Debug(str(HTTP_Response))
				print(traceback.format_exc())
				r.set('error','httpContentFetchError')
				print(e)
				exit()

			if HTTP_Status == 200:
				try:
					# Create the directory for the index.html file
					Dir_Path=URL_to_get.replace(Home_URL,'')
					Local_Path="/tmp/"+S3_Bucket+Dir_Path
					mkdir_p(Local_Path)
				except Exception as e:
					Debug("Directory creation failed")
					r.set('error','dirCreateError')
					print(e)
					exit()
				# Store HTML on disk
				File_Name="index.html"
				File_Path=Local_Path+File_Name
				try:
					with open(File_Path,encoding='utf-8',mode="w+") as outfile:
						outfile.write(HTTP_Content)
						#Debug(color.GREEN+"Successfully created "+File_Path+color.END)
				except Exception as e:
					Debug("File write fail")
					r.set('error','fileWriteError')
					print(type(HTTP_Content))
					print(HTTP_Content)
					print(e)
					exit()

				Send_file_to_S3(File_Path,File_Name,Dir_Path,Content_Type)
			elif HTTP_Status in range(300,305):
				try:
					# Get the redirect URL
					Redirect_URL=HTTP_Response["location"]
					# Only process redirect if it's not redirecting to itself
					if Redirect_URL != URL_to_get:

						File_Name="index.html"
						Dir_Path=URL_to_get.replace(Home_URL,'')
						Local_Path="/tmp/"+S3_Bucket+Dir_Path
						File_Path=Local_Path+File_Name

						# Add trailing slash for S3
						if not Dir_Path.endswith("/"):
							Dir_Path = Dir_Path+"/"
						Dir_Path = Dir_Path.replace("//","/")
						Redirect_Path = urlparse(Redirect_URL).path

						# If redirect URL is for current site, then make sure we fetch it (in case it wasn't found by the API)
						if Redirect_URL.startswith(Home_URL):
							Dir_Path=Dir_Path.replace(Redirect_URL,Home_URL)
							#URL_Fetch(Path,Home_URL,CloudFront_URL,User,Pass,URL_to_get,Directory,S3_Bucket,important_file)
						
						Send_redirect_to_S3(File_Path,File_Name,Dir_Path,Content_Type,Redirect_Path)
						
				except Exception as e:
					Debug(str(HTTP_Response))
					Debug("Redirect fetch failed")
					r.set('error','redirectFetchFailed')
					print(e)
					exit()

			#Debug("Fetch of "+color.BLUE+URL_to_get+color.END+" complete. Total time: "+str(float(time.time())-float(time_start))+" seconds")
	except Exception as e:
		Debug("URL Fetch failed")
		r.set('error','urlFetchError')
		print(e)
		exit()

# Get the number of URLs for first and second stage.
# This is used for calculation of the percentage.
# It does not include all URLs.
#
# @param   array  Dump_of_URLS   Array of all URLs
# @return  int    Number of URLs
def get_Number_of_Important_URLs(Dump_of_URLS):
	second_stage_complete=False
	Number_of_Important_URLs=-1 # start from -1 as gain one due to the "end-first-stage" item
	#	List_of_URLS
	for path in Dump_of_URLS:
		if 'end-second-stage' == path:
			second_stage_complete=True
		#if second_stage_complete is True:
		if True!=second_stage_complete:
			Number_of_Important_URLs += 1

	return Number_of_Important_URLs

# Get the number of URLs for all stages.
# This is used for working out when all files have been sent.
#
# @param   array  Dump_of_URLS   Array of all URLs
# @return  int    Number of URLs
def get_Total_Number_of_URLs(Dump_of_URLS):
	return len(Dump_of_URLS) - 2;


# Primary deploy function.
# This is where it all begins.
#
# @todo   move away from global variables
def Deploy():
	global Strattic_Debug,Home_URL,User,Pass,CloudFront_URL,CloudFront_ID,S3_Bucket,Client_Email,Directory
	time_start = time.time()
	Number_of_Important_URLs = 0
	Dump_of_URLS = ''

	try:
		# Log when deployment was started
		r.set('message','acquiring-urls')
		Debug("Start of Deployment")
		total_files_syncd=0
		Dump_of_URLS=API_Get("/strattic-api/").split("\n")
		Number_of_Important_URLs=get_Number_of_Important_URLs(Dump_of_URLS)
		r.set('number_of_important_urls',Number_of_Important_URLs)
		r.set('total_number_of_urls',get_Total_Number_of_URLs(Dump_of_URLS))
		r.set('message','acquired-urls')
		Percentage=0
	
	except Exception as e:
		print("Error retrieving URLs")
		r.set('error','urlRetrieveError')
		print(e)
		sys.exit()

	try:
		pool = Pool(processes=8)
	
		for path in Dump_of_URLS:
			try:
				if 'end-first-stage' == path: 
					try:
						r.set('message','first-stage-complete')
						Invalidate_CloudFront()
						Debug("1s Invalidation triggered")
					except Exception as e:
						print("First Stage Invalidation error")
						r.set('error','1stCFInvalidationError')
						print(e)
						exit()
				elif 'end-second-stage' == path:
					try:
						r.set('message','second-stage-complete')
						Invalidate_CloudFront()
						Debug("2s Invalidate triggered")
					except Exception as e:
						print("Second stage Invalidation error")
						r.set('error','2ndCFInvalidationError')
						print(e)
						exit()
				elif path != "": # Checks that path is not empty
					try:
						important_file=False
						if Percentage < 100:
							important_file=True

						res=pool.apply_async(URL_Fetch, [path,Home_URL,CloudFront_URL,User,Pass,URL_to_get,Directory,S3_Bucket,important_file])
						total_files_syncd +=1
					except Exception as e:
						print("Failed to process path "+path)
						r.set('error','pathProcessError')
						print(e)
						exit()
				try:		
					Percentage=round(total_files_syncd*100/Number_of_Important_URLs)
				except Exception as e:
					print("Error calculating percentage")
					r.set('error','calcPercentError')
					print(e)
					exit()
			
			except Exception as e:
				print("Path loop error")
				r.set('error','pathLoopError')
				print(e)
				exit()
				
		#This will wait until all of the URLs are fetched.
		Debug("Waiting for the pool() to close")
		pool.close()
		pool.join()
		
	except Exception as e:
		print("Failed to process URLs")
		r.set('error','urlProcessError')
		print(e)
		exit()

	#M# This is a bit much for a try statement... Overkill on my part
	try:
		r.set('message','third-stage-complete')
		Debug("Deployment of pages found via API complete. Number of files synced: "+str(total_files_syncd)+". Total time: "+str(float(time.time())-float(time_start))+" seconds")
	except Exception as e:
		print("Third Stage Error")
		r.set('error','3rdStageError')
		print(e)
		exit()

	try:
		Debug("Copy_Static_Files start")
		Copy_Static_Files()
		# We should take advantage of any open pool workers here for speed.
		# apply() blocks until done, so it might suit our purposes better
		#csf=pool.apply(Copy_Static_Files)
		#pool.join()
		Debug(str(len(matches))+" media files copied to flattening dir /tmp/ Total time: "+str(float(time.time())-float(time_start))+" seconds")
	except Exception as e:
		Debug("Copy Static Files error")
		r.set('error','copyStaticFilesError')
		print(e)
		exit()
	
	try:
		Debug("S3 Sync start")
		S3_Sync()
		Debug("S3_Sync End. Total time: "+str(float(time.time())-float(time_start))+" seconds")
	except Exception as e:
		Debug("S3 Sync error")
		r.set('error','s3SyncError')
		print(e)
		exit()
	
	try:
		Debug("Send_Notification start")
		Send_Notification(Client_Email,S3_Bucket)
	except Exception as e:
		Debug("Notification error")
		r.set('error','sendNotificationError')
		print(e)
		exit()
	
	r.set('message ','complete')
	Debug("Deployment completed. Total time: "+str(float(time.time())-float(time_start))+" seconds")
	
	try:
		r.set('message','invalidating Cloudfront')
		Invalidate_CloudFront(True)
		
	except Exception as e:
		Debug("Second CF Invalidation error")
		r.set('error','2ndCFInvalidation')
		exit()

	# Closes pool to additional requests
	pool.close()
	# Wait for the worker processes to exit. 
	# This will cause an execution delay if there are still open pool workers, which makes the timing more accurate but execution longer.
	pool.join()
	#Hooray!
	r.delete('error')
	
#####

if args.debug is True:
	Strattic_Debug = True
else:
	Strattic_Debug = False

try:
	if args.cfurl is not None:
		CloudFront_URL=args.cfurl
	else:
		raise Exception
except Exception as e:
	print(color.RED+"No CloudFront URL provided. Exiting."+color.END)
	r.set('error','noCFUrlError')
	print(e)
	exit()

try:
	if args.cfid is not None:
		CloudFront_ID=args.cfid
	else:
		raise Exception
except Exception as e:
	print(color.RED+"No CloudFront ID provided. Exiting."+color.END)
	r.set('error','noCFIDError')
	print(e)
	exit()

try:
	if args.homeurl is not None:
		Home_URL=args.homeurl
	else:
		raise Exception
except Exception as e:
	print(color.RED+"No Home URL provided. Exiting."+color.END)
	r.set('error','noHomeURLError')
	print(e)
	exit()

try:
	if args.s3bucket is not None:
		S3_Bucket=args.s3bucket
	else:
		raise Exception
except Exception as e:
	print(color.RED+"No S3 Bucket provided. Exiting."+color.END)
	r.set('error','noS3BucketError')
	print(e)
	exit()

try:
	if args.clientemail is not None:
		Client_Email=args.clientemail
	else:
		raise Exception
except Exception as e:
	print(color.RED+"No client email provided. Exiting."+color.END)
	r.set('error','noClientEmailError')
	print(e)
	exit()

try:
	if args.username is not None:
		User=args.username
	else:
		raise Exception
except Exception as e:
	print(color.RED+"No username provided. Exiting."+color.END)
	r.set('error','noUsernameError')
	print(e)
	exit()

try:
	if args.password is not None:
		Pass=args.password
	else:
		raise Exception
except Exception as e:
	print(color.RED+"No password provided. Exiting."+color.END)
	r.set('error','noPasswordError')
	print(e)
	exit()

try:
	if args.dir is not None:
		Directory=args.dir
	else:
		raise Exception
except Exception as e:
	print(color.RED+"No directory provided. Exiting."+color.END)
	print(e)
	r.set('error','noDirectoryError')
	exit()

if __name__ == '__main__':
	try:
		deploy_start = time.time()
		
		pid = str(os.getpid())
		if os.path.isfile(pidfile):
			with open(pidfile, 'r') as myfile:
			    filepid=myfile.read()
			if pid == filepid:
				print(color.RED+"Script pid file exists, exiting"+color.END)
				r.set('error','pidFileExists')
				sys.exit()
		else:
			try:
				file = open(pidfile, 'w')
				file.write(pid)
				file.close()
			except Exception as e:
				Debug("Failed to create pid file.")
				print(e)
				r.set('error','pidFileWriteError')
				sys.exit()
				
		try:
			if os.path.isfile(os.environ["AWS_SHARED_CREDENTIALS_FILE"]):
				Debug("AWS Credentails exists.")
		except Exception as e:
			Debug("Failed to locate AWS credentials")
			print(e)
			r.set('error','awsCredError')
			sys.exit()
			
		try:
			if os.path.isfile(log):
				os.remove(log)
		except Exception as e:
			Debug("Failed to remove log file.")
			print(e)
			r.set('error','logFileRemove')
			sys.exit()

		r.delete('log')
		r.sadd('log','')
		r.set('message','init')

		Deploy()
	except Exception as e:
		print("Deployment Error")
		print(e)
		r.set('error','deployError')
		sys.exit()

	except KeyboardInterrupt as k:
		global pool
		Debug(color.BOLD+color.RED+"Deployment interrupted. Run time: "+str(float(time.time())-float(deploy_start))+" seconds"+color.END)
		pool.terminate()
		print(k)
		sys.exit()

	finally:
		if os.path.isfile(pidfile):
			os.remove(pidfile)
		if os.path.isdir("/tmp/"+S3_Bucket):
			rmtree("/tmp/"+S3_Bucket,True)

		r.delete('percentage')
		r.delete('message')
		r.delete('log')
		r.delete('files_processed')
		r.delete('number_of_important_urls')
		r.delete('number_important_urls_processed')
		
