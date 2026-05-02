<?php
// Положи этот файл в public/check_python.php
// Открой в браузере: http://localhost/check_python.php
// УДАЛИ после диагностики!

echo "<pre>";

echo "=== where python ===\n";
echo shell_exec('where python 2>&1') . "\n";

echo "=== where python3 ===\n";
echo shell_exec('where python3 2>&1') . "\n";

echo "=== py --version (Python Launcher) ===\n";
echo shell_exec('py --version 2>&1') . "\n";

echo "=== Типичные пути ===\n";
$paths = [
    'C:/Python313/python.exe',
    'C:/Python312/python.exe',
    'C:/Python311/python.exe',
    'C:/Python310/python.exe',
    'C:/Python39/python.exe',
    'C:/Program Files/Python313/python.exe',
    'C:/Program Files/Python312/python.exe',
    'C:/Program Files/Python311/python.exe',
];
foreach ($paths as $p) {
    echo file_exists($p) ? "✅ НАЙДЕН: $p\n" : "❌ $p\n";
}

echo "</pre>";
