#!/bin/sh
curDate=$(date +"%Y-%m-%d_%H:%M")
path='/home/user/DUMP/'
file=${path}${curDate}.sql
mysqldump -uroot -prerfhfxf app > ${file}

