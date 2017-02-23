#!/bin/sh

# variables
now="$(date +'%d_%m_%Y_%H_%M_%S')" # fetches current time
filename="backup_$now".sql # file containing the dump

# customize these directories to suit your needs (script doesn't make these, so make them yourself)
backupfolder="metrics/dumps"

# creates a new folder (name will be timestamp) that will contain the dump and log
mkdir "$backupfolder/$now"

# full path
fullpathbackupfile="$backupfolder/$now/$filename"

# logfile
logfile="$backupfolder/$now/"backup_log_"$now".txt
echo "mysqldump started at $(date +'%d-%m-%Y %H:%M:%S')" >> "$logfile"

# runs the actual dump command; insert correct password (and username, if necessary)
mysqldump -h db2.sis.uta.fi --user=metrics --password="PASSWORD_HERE" --default-character-set=utf8 metrics > "$fullpathbackupfile"

# change ownership to your user; modify this
chown YOUR_USER "$fullpathbackupfile"
chown YOUR_USER "$logfile"

# update log after completion
echo "mysqldump finished at $(date +'%d-%m-%Y %H:%M:%S')" >> "$logfile"
echo "file permission changed" >> "$logfile"

# these lines can be modified to delete old files; currently not in effect
# find "$backupfolder" -name backup_* -mtime +8 -exec rm {} \;
# echo "old files deleted" >> "$logfile"

echo "operation finished at $(date +'%d-%m-%Y %H:%M:%S')" >> "$logfile"
echo "*****************" >> "$logfile"
exit 0
