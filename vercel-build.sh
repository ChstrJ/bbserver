#!/bin/bash

if [[ "$VERCEL_ENV" == "production" ]]; then
  echo "Running migrations for production..."
  php artisan migrate:fresh --force
fi
