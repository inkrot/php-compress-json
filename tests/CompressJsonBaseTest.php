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

    public function testShortString()
    {
        $data = [
            '',
            'p',
            'pp'
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

    public function testSecondValueAsNumericalString()
    {
        //raw json
        $rawJson = '["0","1","2","3","4","5","6","7","8","9",{"A":"h"},{"B":"c"},{"C":"E"},{"D":"b"},{"E":"L"},{"F":"S"},{"G":"C"},{"H":"G"},{"I":"L"},{"J":"I"},{"K":"R"},{"L":"l"},{"M":"6"},{"N":"I"},{"O":"k"}]';
        //compress-json.js generated compressed json
        $compressedJson = '[["0","1","2","3","4","5","6","7","8","9","A","a|A","h","o|B|C","B","a|E","c","o|F|G","C","a|I","E","o|J|K","D","a|M","b","o|N|O","a|K","L","o|Q|R","F","a|T","S","o|U|V","G","a|X","o|Y|I","H","a|a","o|b|X","I","a|d","o|e|R","J","a|g","o|h|d","K","a|j","R","o|k|l","a|R","l","o|n|o","M","a|q","o|r|6","N","a|t","o|u|d","O","a|w","k","o|x|y","a|0|1|2|3|4|5|6|7|8|9|D|H|L|P|S|W|Z|c|f|i|m|p|s|v|z"],"10"]';
        $data=json_decode($rawJson,true);
        $decompressed = Compressor::create()
            ->decompressJson($compressedJson);
        // Assert
        $this->assertEquals($data, $decompressed);
    }
}