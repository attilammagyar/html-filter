#!/bin/bash

# Based on
# http://benlimmer.com/2013/12/26/automatically-publish-javadoc-to-gh-pages-with-travis-ci/

PROJECT_NAME="attilammagyar/html-filter"

echo "Build report publisher:"
echo " - TRAVIS_BUILD_NUMBER=$TRAVIS_BUILD_NUMBER"
echo " - TRAVIS_BRANCH=$TRAVIS_BRANCH"
echo " - TRAVIS_REPO_SLUG=$TRAVIS_REPO_SLUG"
echo " - TRAVIS_PULL_REQUEST=$TRAVIS_PULL_REQUEST"


[[ "$TRAVIS_REPO_SLUG" == "$PROJECT_NAME" ]] || exit 0
[[ "$TRAVIS_PULL_REQUEST" == "false" ]] || exit 0
[[ "$TRAVIS_BRANCH" == "master" ]] || exit 0

echo "Publishing build report..."

git config --global user.email "travis@travis-ci.org"
git config --global user.name "travis-ci"

cp -R docs "$HOME"/docs

git clone --quiet \
          --branch=gh-pages \
          "https://${GHTOKEN}@github.com/$PROJECT_NAME" \
          "$HOME"/gh-pages >/dev/null 2>/dev/null

rm -rf "$HOME"/gh-pages/{api,reports,index.html}
mv "$HOME"/docs/* "$HOME"/gh-pages/

cd "$HOME"/gh-pages/
git add -f . >/dev/null 2>/dev/null
git commit -m "Publishing build report: '$TRAVIS_BUILD_NUMBER'" >/dev/null 2>/dev/null
git push -fq origin gh-pages >/dev/null 2>/dev/null
git remote rm origin

cd
rm -rf "$HOME"/gh-pages

echo "Finished publishing build report."
