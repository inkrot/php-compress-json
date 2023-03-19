<?php

use CompressJson\Core\Compressor;
use PHPUnit\Framework\TestCase;

class CompressJsonBaseTest extends TestCase
{
    public function testBase()
    {
        ini_set('precision', 16);
        $longStr = 'A very very long string, that is repeated';
        $data = [
            'int' => 42,
            'float' => 12.34,
            'str' => 'Alice',
            'longStr',
            'longNum' => 9876543210.123455,
            'bool' => true,
            'bool2' => false,
            'arr' => [42, $longStr],
            'arr2' => [42, $longStr], // identical values will be deduplidated, including array and object
            'obj' => [ // nested values are supported
                'id' => 123,
                'name' => 'Alice',
                'role' => ['Admin', 'User', 'Guest'],
                'longStr' => 'A very very long string, that is repeated',
                'longNum' => 9876543210.123455
            ],
            'escape' => ['s|str', 'n|123', 'o|1', 'a|1', 'b|T', 'b|F']
        ];
        $compressed = Compressor::create()
            ->compress($data);
        $compressedJson = $compressed
            ->toJson();
        $decompressed = Compressor::create()
            ->decompressJson($compressedJson);
        // Assert
        $this->assertInstanceOf('CompressJson\Core\Compressed', $compressed);
        $this->assertIsString($compressedJson);
        $this->assertEquals($data, $decompressed);
    }

    public function testDeepNestedData()
    {
        function getObj($id) {
            // Current max available deep
            // TODO make deeper
            if ($id > 27) {
                return null;
            }
            return [
                'id' => $id,
                'name' => 'Object',
                'nested' => getObj($id + 1)
            ];
        }
        $data = [
            'obj' => getObj(1),
        ];
        $compressed = Compressor::create()
            ->compress($data);
        $compressedJson = $compressed
            ->toJson();
        $decompressed = Compressor::create()
            ->decompressJson($compressedJson);
        // Assert
        $this->assertEquals($data, $decompressed);
    }

    public function testNullValue()
    {
        $data = [
            'key' => null
        ];
        $compressed = Compressor::create()
            ->compress($data);
        $compressedJson = $compressed
            ->toJson();
        $decompressed = Compressor::create()
            ->decompressJson($compressedJson);
        // Assert
        $this->assertEquals($data, $decompressed);
    }

    public function testNestedWithSameKey()
    {
        $data = [
            'objects' => [
                'objects' => [
                    'key' => 'value'
                ]
            ]
        ];
        $compressed = Compressor::create()
            ->compress($data);
        $compressedJson = $compressed
            ->toJson();
        $decompressed = Compressor::create()
            ->decompressJson($compressedJson);
        // Assert
        $this->assertEquals($data, $decompressed);
    }
}