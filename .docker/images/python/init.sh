#!/usr/bin/env bash

python3 -m venv /venv
. /venv/bin/activate
python3 -m pip install -r /app/requirements.txt --upgrade -U
