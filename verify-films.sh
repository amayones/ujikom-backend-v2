#!/bin/bash

echo "🔍 Verifying Films Data..."

php artisan tinker --execute="
echo '=== FILMS VERIFICATION ===' . PHP_EOL;
echo 'Total Films: ' . \App\Models\Film::count() . PHP_EOL . PHP_EOL;

\$films = \App\Models\Film::all();
foreach (\$films as \$film) {
    echo '📽️  ' . \$film->title . PHP_EOL;
    echo '   Status: ' . \$film->status . PHP_EOL;
    echo '   Poster: ' . (\$film->poster ? '✓ ' . substr(\$film->poster, 0, 50) . '...' : '✗ MISSING') . PHP_EOL;
    echo '   Trailer: ' . (\$film->trailer ? '✓ ' . substr(\$film->trailer, 0, 50) . '...' : '✗ MISSING') . PHP_EOL;
    echo PHP_EOL;
}
"
