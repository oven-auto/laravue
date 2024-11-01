#!/bin/sh
# Запустить в cron под sudo
make -C /home/user/www/crm/laravue dump-base 
cd /home/user/www/crm/laravue/backup
test="$(ls -1t | head -n 1)"
cp ${test} /home/user/DUMP