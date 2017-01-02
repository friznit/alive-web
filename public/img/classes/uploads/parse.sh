#!/bin/sh

cd /var/lighttpd/1.4/vhosts/services.ligthert.net/resizer/files

if [ -e lock.file ]; then exit; fi

if [ -e *.png ]; then 

echo $$ > lock.file
rm *.7z
for filename in `ls *.png`; 
	do 
		mkdir -p large medium small thumbnail
		convert $filename -fuzz 18% -fill white -opaque \#E2E2E2 $filename
		convert $filename -trim $filename;
		convert $filename -resize 800x800 medium/800px-$filename;
		convert $filename -resize 300x300 small/300px-$filename;
		convert $filename -resize 150x150 thumbnail/150px-$filename;
		mv $filename large/
	done
7z a -t7z all.7z *
rm lock.file
fi
