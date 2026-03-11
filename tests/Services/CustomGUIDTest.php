<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests\Services;

use Djalone\KkmServerClasses\Services\CustomGUID;
use PHPUnit\Framework\TestCase;

final class CustomGUIDTest extends TestCase
{
    public function test_guid_has_exact_length_40(): void
    {
        $guid = CustomGUID::getCommandGuid();
        $this->assertSame(40, strlen($guid), 'GUID should be exactly 40 characters long');
    }

    public function test_guid_has_prefix_and_uuid7_parts(): void
    {
        $guid = CustomGUID::getCommandGuid();
        $parts = explode('-', $guid, 2);
        $this->assertCount(2, $parts, 'GUID should split into prefix and UUID part by the first dash');
        [$prefix, $uuid] = $parts;

        $this->assertSame(3, strlen($prefix), 'Prefix should be exactly 3 characters long');
        $this->assertMatchesRegularExpression('/^[0-9a-f]{3}$/i', $prefix, 'Prefix should be hex characters');

        $this->assertSame(36, strlen($uuid), 'UUID part should be 36 characters long');
        $this->assertSame(4, substr_count($uuid, '-'), 'UUID part should contain four dashes');
    }

    public function test_guid_uuid_part_is_valid_uuid_v7_format(): void
    {
        $guid = CustomGUID::getCommandGuid();
        [, $uuid] = explode('-', $guid, 2);
        // Basic UUIDv7 regex (8-4-4-4-12 with version 7 and valid variant)
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid,
            'UUID part should match UUIDv7 format'
        );
    }

    public function test_multiple_calls_produce_unique_values(): void
    {
        $values = [];
        for ($i = 0; $i < 100; $i++) {
            $values[] = CustomGUID::getCommandGuid();
        }
        $this->assertSameSize($values, array_unique($values), 'Multiple calls should produce unique GUIDs');
    }

    public function test_prefix_is_always_three_hex_chars_across_many_calls(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $guid = CustomGUID::getCommandGuid();
            [$prefix, ] = explode('-', $guid, 2);
            $this->assertSame(3, strlen($prefix), 'Prefix should always be length 3');
            $this->assertMatchesRegularExpression('/^[0-9a-f]{3}$/i', $prefix, 'Prefix should be hex');
        }
    }
}
