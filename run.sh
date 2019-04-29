#!/usr/bin/env bash

git fetch
git rebase origin/master
echo "RUNNING"
php convert.php