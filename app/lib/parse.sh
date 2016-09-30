#!/bin/sh

cd /var/www/html/img/classes/uploads

if [ -e lock.file ]; then exit; fi

if [ -e *.png ]; then 

echo $$ > lock.file
for filename in `ls *.png`; 
	do 
		convert $filename -fuzz 18% -fill white -opaque \#E2E2E2 $filename
		convert $filename -trim $filename;
		convert $filename -resize 800x800 ../medium/800px-$filename;
		convert $filename -resize 300x300 ../small/300px-$filename;
		convert $filename -resize 150x150 ../thumbs/150px-$filename;
		mv $filename ../large/
	done
rm lock.file
fi
