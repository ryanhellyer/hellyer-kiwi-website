#!/bin/bash
clear

i=0;
for filename in *.jpg;
do
	echo $filename": "$i;
	fileslug=${filename::-4}
	cwebp $filename -o $fileslug.webp

	i=$((i + 1))
done

i=0;
for filename in *.png;
do
	echo $filename": "$i;
	fileslug=${filename::-4}
	cwebp $filename -o $fileslug.webp

	i=$((i + 1))
done