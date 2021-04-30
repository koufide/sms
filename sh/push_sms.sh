#!/bin/sh
#
# push_sms.sh
set -x

# envoi des sms sans verifications des abonnements. le format est specifique 1 ieme diffusion
php -f /var/www/html/sms/api/m_pushsms.php >> /var/www/html/sms/api/log/m_pushsms.php.log

# envoi des sms sans verifications des abonnements. le format est specifique  2 ieme diffusion
php -f /var/www/html/sms/api/m2_pushsms.php >> /var/www/html/sms/api/log/m2_pushsms.php.log

# envoi des sms sans verifications des abonnements. le format est specifique  3 ieme diffusion
php -f /var/www/html/sms/api/m3_pushsms.php >> /var/www/html/sms/api/log/m3_pushsms.php.log

# envoi des sms sans verifications des abonnements. le format est specifique  4 ieme diffusion
php -f /var/www/html/sms/api/m4_pushsms.php >> /var/www/html/sms/api/log/m4_pushsms.php.log

# envoi des sms DIFFERE sans verifications des abonnements. le format est specifique.
#php -f /var/www/html/sms/api/d2_pushsms.php >> /var/www/html/sms/api/log/d2_pushsms.php.log

php -f /var/www/html/sms/api/chargesms.php >> /var/www/html/sms/api/log/chargesms.php.log

#php -f /var/www/html/sms/api/alert.php >> /var/www/html/sms/api/log/alert.php.log
php -f /var/www/html/sms/api/alert2.php >> /var/www/html/sms/api/log/alert2.php.log

php -f /var/www/html/sms/api/diffuser.php >> /var/www/html/sms/api/log/diffuser.php.log



