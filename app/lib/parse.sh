#!/bin/sh

cd /var/www/html/img/classes/uploads

if [ -e lock.file ]; then exit; fi

# if [ -e *.png ]; then 

echo $$ > lock.file
for filename in `ls *.png`; 
	do
	#	if [ -e ../large/$filename ]; then 
	#		echo "File '$filename' Exists"
	#	else
			echo Converting $filename ...  
			convert $filename -fuzz 18% -trim +repage $filename;
			convert $filename -bordercolor white -border 5 $filename;
			/var/www/app/lib/magicwand.sh 6,6 -t 5 -r outside -c "trans" $filename $filename;
			convert $filename -resize 800x800 ../medium/800px-$filename;
			convert $filename -resize 300x300 ../small/300px-$filename;
			convert $filename -resize 150x150 ../thumbs/150px-$filename;
			mv -f $filename ../large/
	#	fi
	done
rm lock.file
# fi
