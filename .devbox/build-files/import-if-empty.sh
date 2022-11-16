#!/bin/bash

if ! mysql -e 'SELECT * FROM tt_content;' db > /dev/null; then
  echo 'Importing Database from file'
  gzip -dc /var/www/html/build-files/db.sql.gz | mysql db
fi

