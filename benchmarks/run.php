<?php

/*
 * Rough throughput benchmark: composer benchmark [iterations]
 *
 * Not a micro-benchmark suite; it exists to catch large regressions
 * (e.g. a dictionary being scanned on every call, or every locale file
 * being loaded up front).
 *
 * "first call" runs in a fresh process, so it includes autoloading and the
 * lazy loading of the resources that case needs.
 */

use Pharaonic\Slugify\Policies\EmojiPolicy;
use Pharaonic\Slugify\Policies\SymbolPolicy;
use Pharaonic\Slugify\Slugger;
use Pharaonic\Slugify\Slugify;

require __DIR__ . '/../vendor/autoload.php';

$kilobyte = str_repeat('The quick brown fox jumps over the lazy dog. ', 23);

/** @var array<string, callable(): Slugger> $cases */
$cases = [
    'plain ascii' => static fn (): Slugger => Slugify::of('Hello World from Pharaonic'),
    'unicode latin' => static fn (): Slugger => Slugify::of('Crème brûlée à la française'),
    'arabic' => static fn (): Slugger => Slugify::of('مرحبا بالعالم الإصدار ١٢'),
    'cyrillic' => static fn (): Slugger => Slugify::of('Привет мир, как дела'),
    'chinese' => static fn (): Slugger => Slugify::of('你好世界，欢迎光临'),
    'emoji-heavy' => static fn (): Slugger => Slugify::of('PHP 🚀 👨‍👩‍👧‍👦 🇪🇬 ❤️ 👍🏽 rocks 1️⃣'),
    '1 KB' => static fn (): Slugger => Slugify::of($kilobyte),
    '10 KB' => static fn (): Slugger => Slugify::of(str_repeat($kilobyte, 10)),
    'ascii mode' => static fn (): Slugger => Slugify::of('Crème brûlée, Привет мир, 你好')->ascii(),
    'locale override (uk)' => static fn (): Slugger => Slugify::of('Київ Запоріжжя Щастя')->locale('uk')->ascii(),
    'locale (tr), unicode' => static fn (): Slugger => Slugify::of('IŞIK İstanbul')->locale('tr'),
    'symbol words' => static fn (): Slugger => Slugify::of('R&D 50% €100')->symbols(SymbolPolicy::words()),
    'emoji words' => static fn (): Slugger => Slugify::of('PHP 🚀 Rocks')->emoji(EmojiPolicy::custom(['🚀' => 'rocket'])),
];

if (isset($argv[1]) && $argv[1] === '--first-call') {
    $start = hrtime(true);
    $cases[$argv[2] ?? '']()->toString();
    echo (hrtime(true) - $start) / 1e3, ' ', count(get_included_files()), "\n";

    exit;
}

$iterations = (int) ($argv[1] ?? 2000);

printf("%-24s %12s %12s %16s %14s\n", 'case', 'ops/sec', 'µs/op', 'first call µs', 'files loaded');

foreach ($cases as $name => $case) {
    $command = sprintf(
        '%s %s --first-call %s',
        escapeshellarg(PHP_BINARY),
        escapeshellarg(__FILE__),
        escapeshellarg($name)
    );
    [$firstCall, $files] = explode(' ', trim((string) shell_exec($command))) + ['?', '?'];

    $slugger = $case();
    $slugger->toString();

    $start = hrtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $slugger->toString();
    }

    $elapsed = (hrtime(true) - $start) / 1e9;

    printf(
        "%-24s %12s %12.2f %16s %14s\n",
        $name,
        number_format($iterations / $elapsed),
        $elapsed / $iterations * 1e6,
        is_numeric($firstCall) ? number_format((float) $firstCall) : $firstCall,
        $files
    );
}

printf(
    "\nPHP %s, intl %s, %d iterations per case, peak memory %.1f MB\n",
    PHP_VERSION,
    extension_loaded('intl') ? 'on' : 'off (polyfill)',
    $iterations,
    memory_get_peak_usage() / 1048576
);
