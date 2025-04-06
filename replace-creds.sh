#!/bin/bash
git filter-branch --force --index-filter \
  "git ls-files -z | xargs -0 sed -i '' -e 's/1677503575724/REMOVED_USERNAME/g' -e 's/12tY5LWi/REMOVED_PASSWORD/g'" \
  --prune-empty --tag-name-filter cat -- --all
