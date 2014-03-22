#!/bin/bash

echo "Build report publisher:"
echo " - TRAVIS_BRANCH=$TRAVIS_BRANCH"
echo " - TRAVIS_REPO_SLUG=$TRAVIS_REPO_SLUG"
echo " - TRAVIS_PULL_REQUEST=$TRAVIS_PULL_REQUEST"

[[ "$TRAVIS_REPO_SLUG" == "attilammagyar/html-filter" ]] || exit 0
[[ "$TRAVIS_PULL_REQUEST" == "false" ]] || exit 0
[[ "$TRAVIS_BRANCH" == "master" ]] || exit 0

echo "Publishing build report..."

git config --global user.email "travis@travis-ci.org"
git config --global user.name "travis-ci"

cp -R docs "$HOME"/docs

git clone --quiet \
          --branch=gh-pages \
          "https://${GHTOKEN}@github.com/attilammagyar/html-filter" \
          "$HOME"/gh-pages

rm -rf "$HOME"/gh-pages/{api,reports,index.html}
mv "$HOME"/docs/* "$HOME"/gh-pages/

cd "$HOME"/gh-pages/
git add -f api reports index.html
git commit -m "Publishing build report: '$TRAVIS_BUILD_NUMBER'"
git push -fq origin gh-pages

echo "Published build report."
