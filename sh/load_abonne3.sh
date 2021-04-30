#!/bin/sh
#
set -x

# who
# pwd

# sh login.sh 

# echo $API

# php -f $API/getSmsLogs.php >> $LOG/getSmsLogs.log
# php -f /var/www/html/sms/api/loadAbonnement3.php >> /var/www/html/sms/api/log/loadAbonnement3.log
php -f /var/www/html/sms/api/loadAbonnement4.php >> /var/www/html/sms/api/log/loadAbonnement4.log

