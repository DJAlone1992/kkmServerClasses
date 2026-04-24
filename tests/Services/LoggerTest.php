<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests\Services;

use Djalone\KkmServerClasses\Services\Logger;
use Monolog\Logger as MonologLogger;
use PHPUnit\Framework\TestCase;

final class LoggerTest extends TestCase
{
    public function test_get_instance_returns_same_logger(): void
    {
        if (defined('KKM_SERVER_LOGS_DIR')) {
            $this->markTestSkipped('KKM_SERVER_LOGS_DIR already defined in this process.');
        }

        $tempDir = sys_get_temp_dir() . '/kkm_server_logger_' . uniqid('', true);
        define('KKM_SERVER_LOGS_DIR', $tempDir);

        $this->resetLoggerInstance();

        $loggerOne = Logger::getInstance();
        $loggerTwo = Logger::getInstance();

        $this->assertInstanceOf(MonologLogger::class, $loggerOne);
        $this->assertSame($loggerOne, $loggerTwo);
        $this->assertDirectoryExists($tempDir);

        $this->removeDirectory($tempDir);
    }

    private function resetLoggerInstance(): void
    {
        $reflection = new \ReflectionClass(Logger::class);
        $property = $reflection->getProperty('instance');
        $property->setValue(null, null);
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }

        rmdir($directory);
    }
}
