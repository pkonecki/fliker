#!/bin/bash

## !! BUG !! need to check that no datafield has the same name than
##           a table, otherwise the datafield will be prefixed as well :(

SQLFILE=fliker.sql
OUTFILE=fliker.out
TMPFILE=fliker.tmp

PREFIX=fliker_

cp -arf $SQLFILE $OUTFILE
for table in `egrep -i '^(create table|create table if not exists|alter table|insert into) \`' $OUTFILE | grep -v '^\-\- ' | cut -d'\`' -f2 | sort -u` ; do sed "s/\`$table\`/\`$PREFIX$table\`/g" $OUTFILE > $TMPFILE ; mv -f $TMPFILE $OUTFILE ; done
rm -f  $TMPFILE
