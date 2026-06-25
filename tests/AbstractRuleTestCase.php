<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

abstract class AbstractRuleTestCase extends AbstractRectorTestCase
{
    abstract protected function getRuleClass(): string;

    /**
     * @return array<string, mixed>
     */
    protected function getRuleConfiguration(): array
    {
        return [];
    }

    final public function provideConfigFilePath(): string
    {
        $configFilePath = $this->getTempDirectory() . '/config.php';
        $ruleClass = '\\' . $this->getRuleClass();
        $ruleConfiguration = $this->getRuleConfiguration();
        $ruleRegistration = $ruleConfiguration === []
            ? sprintf('$rectorConfig->rule(%s::class);', $ruleClass)
            : sprintf('$rectorConfig->ruleWithConfiguration(%s::class, ', $ruleClass) . var_export($ruleConfiguration, return: true) . ');';

        file_put_contents(
            $configFilePath,
            <<<PHP
                <?php

                declare(strict_types=1);

                use Rector\\Config\\RectorConfig;

                return static function (RectorConfig \$rectorConfig): void {
                    {$ruleRegistration}
                };
                PHP
        );

        return $configFilePath;
    }

    protected function doTestCode(string $input, ?string $expected = null): void
    {
        $fixtureFilePath = $this->createFixtureFile($input, $expected);

        $this->doTestFile($fixtureFilePath);
    }

    private function createFixtureFile(string $input, ?string $expected): string
    {
        $fixtureDirectory = $this->getTempDirectory() . '/fixtures';

        if (!is_dir($fixtureDirectory)) {
            mkdir($fixtureDirectory, 0o777, recursive: true);
        }

        $fixtureFilePath = $fixtureDirectory . '/' . uniqid('fixture_', more_entropy: true) . '.php.inc';
        $content = $expected === null
            ? $input
            : $input . PHP_EOL . '-----' . PHP_EOL . $expected;

        file_put_contents($fixtureFilePath, $content);

        return $fixtureFilePath;
    }

    private function getTempDirectory(): string
    {
        $directory = sys_get_temp_dir() . '/rector-rules-tests/' . str_replace('\\', '_', static::class);

        if (!is_dir($directory)) {
            mkdir($directory, 0o777, recursive: true);
        }

        return $directory;
    }
}
