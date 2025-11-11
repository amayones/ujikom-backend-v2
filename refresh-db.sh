#!/bin/bash
# Script to manually refresh database with new seeders

docker exec backend php artisan migrate:fresh --seed --force
