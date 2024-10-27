#!/usr/bin/env bash

python3 -m venv /venv
. /venv/bin/activate
set -x
exec "$@" > /dev/stdout 2> /dev/stderr