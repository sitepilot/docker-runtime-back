#!/bin/bash

set -e
set -u
set -o pipefail

runtime-cli log "Initialize folders"
mkdir -p {{ $paths['home'] }}/logs
mkdir -p {{ $paths['home'] }}/public/{{ $docRoot }}
