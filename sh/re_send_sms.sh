#!/bin/sh
#
set -x

php -f /var/www/html/sms/api/reSendSMS.php >> /var/www/html/sms/api/log/reSendSMS.log

