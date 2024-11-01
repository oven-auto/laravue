#!/bin/sh
curDate=$(date +"%Y-%m-%d_%H:%M")
path='/mnt/mysql/backup/'
file=${path}${curDate}.sql
mysqldump -uroot -psecret app > ${file}

