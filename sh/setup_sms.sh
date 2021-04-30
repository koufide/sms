#!/bin/sh
#
set -x

HOME_LOG=/home/ftpuser/ftp/outgoing ; export HOME_LOG;

LADATE=`date +"%Y%m%d"`

echo "========== SETUP SMS ======== $LADATE"

echo $LADATE
echo $HOME_LOG

cd $HOME_LOG
ls -ltrh $HOME_LOG

LADATE=`date +"%Y%m%d%H%M%S"`
#mv TODO.txt "PUSH_"$LADATE".txt"
mv TODO.txt "MPUSH_"$LADATE".txt"

ls -ltrh $HOME_LOG


# # attendre 3 min avant execution
sleep 2

# LADATE=`date +"%Y%m%d%H%M%S"`
echo "==========FIN TRAITEMENT SETUP SMS ======== $LADATE"



