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
                        /var/www/app/lib/magicwand.sh 5,5 -t 7 -r outside -c "trans" $filename 1-$filename;
			convert 1-$filename -shave 2x2 -fuzz 18% -trim +repage 2-$filename;
			# convert 1-$filename -bordercolor white -border 5 2-$filename;

	#	fi
	done
rm lock.file
# fi
