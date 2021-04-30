#!/bin/bash

set -x

#ladate=`date +"%d%m%Y"`
ladate=`date +"%d%m%Y%H%M%S"`
ladate_str=`date +"%d-%m-%Y %T"`

lock_charge_sms=/var/www/html/sms/api/log/lock_charge_sms.run

echo "===== execution save_ougoing ==== $ladate_str"

if test -f "$lock_charge_sms"; then
    echo "$lock_charge_sms exist"
    exit 0
fi

touch $lock_charge_sms

# backup_outgoing=/home/iexploit/backup_outgoing/$ladate
backup_outgoing=/data/sms/backup_outgoing/$ladate
echo "backup_outgoing: $backup_outgoing"

sleep 5

if [ ! -e $backup_outgoing ]; then
    mkdir -pv $backup_outgoing
    echo "CREATION DU REPERTOIRE $backup_outgoing"
fi


# compresser le dossier dans backup
zip -r $backup_outgoing/outgoing_$ladate.zip /home/iexploit/outgoing

rm -fvr  /home/iexploit/outgoing/*


rm -vf /var/www/html/sms/api/log/lock_charge_sms.run

echo "=== fin $ladate_str"  