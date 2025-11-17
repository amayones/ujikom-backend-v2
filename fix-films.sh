#!/bin/bash

# Script untuk fix film data di production
# Usage: ./fix-films.sh [production-url]

if [ -z "$1" ]; then
    echo "❌ Error: URL production tidak diberikan"
    echo "Usage: ./fix-films.sh https://your-domain.com"
    exit 1
fi

PRODUCTION_URL=$1

echo "🎬 Fixing films data di production..."
echo "URL: $PRODUCTION_URL"
echo ""

# Call fix endpoint
response=$(curl -s -w "\n%{http_code}" "$PRODUCTION_URL/api/fix-films-production")
http_code=$(echo "$response" | tail -n1)
body=$(echo "$response" | sed '$d')

if [ "$http_code" -eq 200 ]; then
    echo "✅ Success! Films berhasil di-fix"
    echo ""
    echo "Response:"
    echo "$body" | jq '.' 2>/dev/null || echo "$body"
else
    echo "❌ Error! HTTP Code: $http_code"
    echo ""
    echo "Response:"
    echo "$body"
    exit 1
fi

echo ""
echo "🎉 Done! Silakan cek frontend untuk melihat poster dan trailer"
