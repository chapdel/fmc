
#!/usr/bin/env bash

set -e
set -x

function remote()
{
    git remote add $1 $2 || true
}

function split()
{
    SHA1=`splitsh-lite --prefix=$1`
    git push $2 "$SHA1:master" -f
}

git pull origin master

remote tests git@github.com:spatie/laravel-mailcoach-tests.git

split 'tests' tests
