#!/bin/bash

export SIMPLETEST_BASE_URL="http://nginx"
export SIMPLETEST_DB="mysql://${DB_USER}:${DB_PASSWORD}@${DB_HOST}-us/${DB_NAME}_us"
export BROWSERTEST_OUTPUT_DIRECTORY="$(pwd)/web/sites/default/files/simpletest"