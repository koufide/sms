#!/bin/bash
set -x

#ladate=`date +"%d%m%Y"`
ladate=`date +"%d%m%Y%H%M%S"`
ladate_str=`date +"%d-%m-%Y %T"`

echo "===== execution save_html ==== $ladate_str"

backup_html=/media/sf_Logiciels/backup_html/$ladate
echo "backup_html: $backup_html"

if [ ! -e $backup_html ]; then
    mkdir -pv $backup_html
    echo "CREATION DU REPERTOIRE $backup_html"
fi


# compresser le dossier dans backup
zip -r $backup_html/html_$ladate.zip /var/www/html

#export la base de donnees
####Export: mysqldump -u root -p --all-databases > all_dbs.sql
####Import: mysql -u root -p < all_dbs.sql


mysqldump -u root --all-databases > $backup_html/all_dbs_${ladate}.sql

echo "=== fin $ladate_str"