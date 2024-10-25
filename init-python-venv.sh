#!/bin/bash

python3.11 -m venv .pyVenv
. .pyVenv/bin/activate
python3 -m pip install -r "pyCore/requirements.txt" --upgrade -U
