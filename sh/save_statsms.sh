#!/bin/sh
#
set -x

HOME_LOG=/home/iexploit/log ; export HOME_LOG;
API_LOG=/var/www/html/sms/api/log ; export API_LOG;

LADATE=`date +"%Y%m%d"`
API_LOGS_REP=$HOME_LOG"/api/"$LADATE 

echo "========== SAUVEGARDE STATISTIQUE SMS  ======== $LADATE"

echo $LADATE
echo $HOME_LOG
echo $API_LOG
echo $REP_API_LOGS

php -f /var/www/html/sms/api/statsms.php >> /var/www/html/sms/api/log/statsms.php.log



