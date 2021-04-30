#!/bin/sh
#
set -x

# who
# pwd

# sh login.sh 

# echo $API

# php -f $API/getSmsLogs.php >> $LOG/getSmsLogs.log
php -f /var/www/html/sms/api/loadAbonnement2.php >> /home/iexploit/log/loadAbonnement2.log

