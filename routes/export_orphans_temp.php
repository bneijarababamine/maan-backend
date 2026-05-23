<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

\DB::statement('SET NAMES utf8mb4');
\DB::statement('SET CHARACTER SET utf8mb4');

$orphans = \DB::table('orphans')
    ->select('full_name','birth_date','gender','school_name','grade','guardian_name','guardian_phone','address','is_active','notes')
    ->orderBy('guardian_name')
    ->orderBy('full_name')
    ->get();

$json = json_encode($orphans, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents(__DIR__ . '/../orphans_export.json', $json);
echo "Exporté : " . count($orphans) . " orphelins → orphans_export.json\n";
