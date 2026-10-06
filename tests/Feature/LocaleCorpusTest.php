<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Tests\TestCase;
use Pharaonic\Slugify\Transliteration\PortableAsciiTransliterator;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The locale evaluation corpus (tests/Fixtures/Transliteration).
 *
 * Besides checking the expected output, it enforces the package rule:
 * a Pharaonic locale override exists only if the generic behavior fails
 * that locale's corpus, and every override has a corpus.
 */
final class LocaleCorpusTest extends TestCase
{
    private const FIXTURES = __DIR__ . '/../Fixtures/Transliteration';

    private const LOCALES = __DIR__ . '/../../src/Resources/locales';

    /**
     * @return array<string, array{string, string, string, string|null}>
     */
    public static function corpusProvider(): array
    {
        $cases = [];

        foreach (self::corpus() as $locale => $samples) {
            foreach ($samples as $i => $sample) {
                $cases[$locale . ' #' . $i] = [
                    $locale,
                    $sample['input'],
                    $sample['expected_ascii'],
                    $sample['expected_unicode'] ?? null,
                ];
            }
        }

        return $cases;
    }

    #[DataProvider('corpusProvider')]
    public function testCorpus(string $locale, string $input, string $ascii, ?string $unicode): void
    {
        $this->assertSame($ascii, Slugify::of($input)->locale($locale)->ascii()->toString());

        if ($unicode !== null) {
            $this->assertSame($unicode, Slugify::of($input)->locale($locale)->toString());
        }
    }

    /**
     * @return array<string, array{string}>
     */
    public static function overrideProvider(): array
    {
        $locales = [];

        foreach (glob(self::LOCALES . '/*.php') ?: [] as $file) {
            $locale = basename($file, '.php');
            $locales[$locale] = [$locale];
        }

        return $locales;
    }

    #[DataProvider('overrideProvider')]
    public function testEveryOverrideIsJustifiedByTheCorpus(string $locale): void
    {
        $corpus = self::corpus();
        $this->assertArrayHasKey($locale, $corpus, "The [$locale] override has no corpus.");

        /** @var array<string, array<string, string>> $overrides */
        $overrides = require self::LOCALES . '/' . $locale . '.php';
        $this->assertNotEmpty($overrides);

        if (isset($overrides['ascii'])) {
            $this->assertTrue(
                $this->someSampleFails($corpus[$locale], function (string $input) use ($locale): string {
                    return Slugify::of($input)
                        ->locale($locale)
                        ->ascii()
                        ->transliterator(new PortableAsciiTransliterator())
                        ->toString();
                }, 'expected_ascii'),
                "The generic transliterator passes the [$locale] corpus: the ASCII override is not needed."
            );
        }

        if (isset($overrides['lowercase'])) {
            $this->assertTrue(
                $this->someSampleFails($corpus[$locale], function (string $input): string {
                    return Slugify::make($input);
                }, 'expected_unicode'),
                "Generic lowercasing passes the [$locale] corpus: the lowercase override is not needed."
            );
        }
    }

    /**
     * @return array<string, array{string}>
     */
    public static function genericLocaleProvider(): array
    {
        return array_diff_key(
            array_map(static function (string $locale): array {
                return [$locale];
            }, array_combine(array_keys(self::corpus()), array_keys(self::corpus())) ?: []),
            self::overrideProvider()
        );
    }

    /**
     * Locales without an override are fully served by portable-ascii.
     */
    #[DataProvider('genericLocaleProvider')]
    public function testLocalesWithoutOverrideUseTheGenericTransliterator(string $locale): void
    {
        foreach (self::corpus()[$locale] as $sample) {
            $generic = Slugify::of($sample['input'])
                ->locale($locale)
                ->ascii()
                ->transliterator(new PortableAsciiTransliterator())
                ->toString();

            $this->assertSame($sample['expected_ascii'], $generic);
        }
    }

    /**
     * @param list<array<string, string>> $samples
     * @param callable(string): string    $slug
     */
    private function someSampleFails(array $samples, callable $slug, string $key): bool
    {
        foreach ($samples as $sample) {
            if (isset($sample[$key]) && $slug($sample['input']) !== $sample[$key]) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, list<array<string, string>>>
     */
    private static function corpus(): array
    {
        $corpus = [];

        foreach (glob(self::FIXTURES . '/*.php') ?: [] as $file) {
            /** @var list<array<string, string>> $samples */
            $samples = require $file;
            $corpus[basename($file, '.php')] = $samples;
        }

        return $corpus;
    }
}
