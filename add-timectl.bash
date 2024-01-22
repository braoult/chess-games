#!/usr/bin/env bash

str='[TimeControl "3600"]'
str='[TimeControl "40/7200:20/3600:3600"]'
filein="$1"
fileout="$filein.new"
echo "in=$filein out=$fileout"
sed -e '/^\[Black ".*"\]/a '"$str" < "$filein" > "$fileout"
