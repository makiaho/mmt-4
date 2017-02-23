**********************************************************
** BACKUP SCRIPT FOR METRICS MONITORING TOOL'S DATABASE **
**********************************************************

by Ändi
16.3.2016

Use this however you want, I don't care
To contact me, use this email:
a (DOT) i (DOT) valjakka (AT) acm (DOT) org

**********************************************************

CONTAINS:

/README.TXT
/backup_script.sh

**********************************************************

INFO:

By default, this script creates a new folder for each new dump into directory  metrics/dumps/ .
New folders' names are timestamps.

You need both FileZilla and PuTTY for this. Equivalent software is fine too but instructions are only for these.


**********************************************************

SETUP:
THE SCRIPT

----------------

USING FILEZILLA:

* login to shell.sis.uta.fi
* create folder metrics, then inside that create dumps (so it will be <home>/metrics/dumps/
  -> it will contain your backup dumps. If you want to use different folders, you have to edit the script accordingly

* on your computer, open file backup_script.sh in any text editor
  -> edit variable backupfolder on line 8, if you made any other folder structure than the default
  -> insert database password inside the quotation marks in line 21
  -> replace YOUR_USER with your basic user account (e.g. aa12345) on lines 24 and 25

* use FileZilla to transfer backup_script.sh into your shell's home (root) directory

----------------

USING PUTTY:

* login to shell.sis.uta.fi

* after login, you are automatically in your home directory where the script also is
  -> test it by typing
     ./backup_script.sh
  
  -> it shouldn't make any prints to command line if it works properly

-----------------
* now you can use FileZilla to inspect the dump folder; it should have a folder with timestamp as name
  -> check inside the folder. Inspect the backup_<timestamp>.sql file to see if it looks right.
-----------------

* you can do the same with PuTTY too: 
  -> change directory to the dumps' location using command
     cd metrics/dumps
     (if non-default folders were made, adjust command accordingly)
     NOTE: use TAB-key to autofill commands

  -> list all the files in the folder using command
     ls
     so you can find the name of the new folder (which is the timestamp)
  -> use cd to go into the folder (use TAB to autofill the date)
  -> inside the metrics/dumps/<timestamp>, use command
     cat backup_<timestamp>.sql
     This will print the whole file to your shell prompt, so there will be a LOT of text. See if it looks fine

************************

SETUP:
AUTOMATION

-----------------

USING PUTTY:

* IF the script seems to work correctly, now you need to make it run automatically.

* in your shell, use command
  crontab -e
  (by default, it has comments that describe how it is used)
  
* below all the lines starting with # (comments), insert some line that indicates when the script should run, for example:
  59 23 * * * ~/backup_script.sh

  EXAMPLE:
  The basic structure of a line in crontab is:
  MINUTE HOUR DAY MONTH WEEKDAY <filepath>
  
  So line like
  59 23 * * * ~/backup_script.sh

  runs the backup_script.sh -file every weekday (*) of every month (*) in every day (*), when the time is 23:59 (in other words, every day at 23:59)
  
  To run it two times a day, you can use
  59 11,23 * * * ~/backup_script.
  Now it runs every day at 11:59 and 23:59

********************

HOW TO INSERT DUMP INTO DATABASE:

* simply login to mySQL in shell prompt (using PuTTY)
  -> inside mySQL, use command
     source <filepath>
     
     EXAMPLE:
     mysql>source ~/metrics/dumps/<timestamp>/backup_<timestamp>.sql
     
* and it's done
