#!/bin/sh
set -x
WEB=/var/www/html ; export WEB;
echo $WEB
echo "$WEB"
SMS=$WEB/sms; export SMS;
API=$SMS/api; export API;
MSH=$SMS/sh; export MSH;
HOME=/home;             export HOME; 
USR=$HOME/iexploit;       export USR;
LOG=$USR/log; export LOG;