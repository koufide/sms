#!/bin/bash
set -x

#ladate=`date +"%d%m%Y"`
ladate=`date +"%d%m%Y%H%M%S"`
ladate_str=`date +"%d-%m-%Y %T"`

echo "===== execution save_sms ==== $ladate_str"

 php /var/www/html/sms/bin/console cache:clear && rm -rfv /var/www/html/sms/var/cache/* && rm -rfv /var/www/html/sms/var/log/* && php /var/www/html/sms/bin/console assets:install public

 

# backup_sms=/media/sf_Logiciels/backup_sms/$ladate
backup_sms=/media/sf_Downloads/backup_sms/$ladate
echo "backup_sms: $backup_sms"

sleep 5

if [ ! -e $backup_sms ]; then
    mkdir -pv $backup_sms
    echo "CREATION DU REPERTOIRE $backup_sms"
fi


# compresser le dossier dans backup
zip -r $backup_sms/sms_$ladate.zip /var/www/html/sms

#export la base de donner
#mysqldump -u root -p bbg_sms >  $backup_sms/bbg_sms_${ladate}.sql
mysqldump -u root sms >  $backup_sms/sms_${ladate}.sql


#sh /var/www/html/sf42w/sh/save_sf42w.sh

echo "=== fin $ladate_str"  