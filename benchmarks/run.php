<?php

/*
 * Rough throughput benchmark: composer benchmark
 *
 * Not a micro-benchmark suite; it exists to catch large regressions
 * (e.g. a dictionary being scanned on every call).
 */

use Pharaonic\Slugify\Slugify;

require __DIR__ . '/../vendor/autoload.php';

$kilobyte = str_repeat('The quick brown fox jumps over the lazy dog. ', 23);

$cases = [
    'short ascii' => ['Hello World from Pharaonic', false],
    'short unicode' => ['مرحبا بالعالم Привет мир', false],
    '1 KB' => [$kilobyte, false],
    '10 KB' => [str_repeat($kilobyte, 10), false],
    'ascii transliteration' => ['Crème brûlée à la française, Привет мир', true],
];

$iterations = (int) ($argv[1] ?? 2000);

printf("%-24s %12s %12s\n", 'case', 'ops/sec', 'µs/op');

foreach ($cases as $name => [$input, $ascii]) {
    Slugify::make($input, '-', $ascii);

    $start = hrtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        Slugify::make($input, '-', $ascii);
    }

    $elapsed = (hrtime(true) - $start) / 1e9;

    printf("%-24s %12s %12.2f\n", $name, number_format($iterations / $elapsed), $elapsed / $iterations * 1e6);
}

printf("\nPHP %s, %d iterations per case\n", PHP_VERSION, $iterations);
