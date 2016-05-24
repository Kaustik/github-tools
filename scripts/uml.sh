#!/bin/bash
PULL_REQUEST_NUMBER=${SCRUTINIZER_PR_NUMBER}
HASH=$(echo "${PULL_REQUEST_NUMBER}dfjhfd90dfjfd7"| sha1sum | awk '{print $1}')
FILENAME=${PULL_REQUEST_NUMBER}_${HASH}.png
FILEPATH="/tmp/${FILENAME}"
URL="https://kaustik-uml.s3.amazonaws.com/${FILENAME}
cd ../
vendor/PhUML/src/app/umlforpullrequest.php -t=${GITHUB_TOKEN} -o=${FILEPATH} -b ./ -p=${PULL_REQUEST_NUMBER}
scripts/s3signature_and_upload.sh ${AWS_ACCESS_KEY_ID} ${AWS_SECRET_ACCESS_KEY} ${FILENAME}
app/console github-tools:upsertimageinpullrequest ${PULL_REQUEST_NUMBER} ${GITHUB_TOKEN} ${URL}