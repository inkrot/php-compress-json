# php-compress-json

A PHP library to compress large JSON documents with repeated structures and values.
Helps to reduce the size of a JSON file, for example, during storage or transmission over the network, while preserving the document structure and key names.

Inspired by [compress-json](https://github.com/beenotung/compress-json). And thanks to [@beenotung](https://github.com/beenotung).

This library is fully compatible with [compress-json](https://github.com/beenotung/compress-json) for NodeJS.

## Features

* Supports all JSON types
* Object key order is preserved
* Repeated values are stored only once
* Numbers are encoded in [base62](https://en.wikipedia.org/wiki/Base62) format (0-9A-Za-z)
* Custom Memory providers for store and cache during compress heavy data (see class [AbstractMemory](./src/Memory/AbstractMemory.php)).
  You can pass your custom memory provider through the ``Compressor::createWithMemory()`` method
* Passing custom ``json_encode`` or ``json_decode`` arguments in ``toJson`` or ``decompressJson`` methods respectively


## Install

Via Composer

``` bash
composer require inkrot/php-compress-json
```

## Usage

### Compress

```php
use CompressJson\Core\Compressor;

$data = [
    'key1' => 'value1',
    'key2' => 'value3',
    'key3' => [
        'nestedKey1' => 'value1'
    ]
];
$compressedJson = Compressor::create()
    ->compress($data)
    ->toJson();

print_r($compressedJson); // encoded string in JSON format
```

Output:
```
[["key1","key2","key3","a|0|1|2","value1","value3","nestedKey1","a|6","o|7|4","o|3|4|5|8"],"9"]
```

<hr>

### Decompress

```php
use CompressJson\Core\Compressor;

$compressedJson = '[["key1","key2","key3","a|0|1|2","value1","value3","nestedKey1","a|6","o|7|4","o|3|4|5|8"],"9"]';
$data = Compressor::create()
    ->decompressJson($compressedJson);

print_r($data); // array
```

Output:
```
Array
(
    [key1] => value1
    [key2] => value3
    [key3] => Array
        (
            [nestedKey1] => value1
        )

)
```

<hr>

### Example structure for efficient compression
```php
use CompressJson\Core\Compressor;
$data = [
    'count' => 3,
    'names' => ['New York', 'London', 'Paris', 'Beijing', 'Moscow'],
    'cities' => [
        [
            'id' => 1,
            'name' => 'New York',
            'countryName' => 'USA',
            'location' => [
                'latitude' => 40.714606,
                'longitude' => -74.002800,
            ],
            'localityType' => 'BIG_CITY',
        ],
        [
            'id' => 2,
            'name' => 'London',
            'countryName' => 'UK',
            'location' => [
                'latitude' => 51.507351,
                'longitude' => -0.127696,
            ],
            'localityType' => 'COUNTRY_CAPITAL',
        ],
        [
            'id' => 3,
            'name' => 'Paris',
            'countryName' => 'France',
            'location' => [
                'latitude' => 48.856663,
                'longitude' => 2.351556,
            ],
            'localityType' => 'COUNTRY_CAPITAL',
        ],
        [
            'id' => 4,
            'name' => 'Beijing',
            'countryName' => 'China',
            'location' => [
                'latitude' => 39.901850,
                'longitude' => 116.391441,
            ],
            'localityType' => 'COUNTRY_CAPITAL',
        ],
        [
            'id' => 5,
            'name' => 'Moscow',
            'countryName' => 'Russia',
            'location' => [
                'latitude' => 55.755864,
                'longitude' => 37.617698,
            ],
            'localityType' => 'COUNTRY_CAPITAL',
        ],
    ]
];
$compressedJson = Compressor::create()
    ->compress($data)
    ->toJson();
    
print_r(json_encode($data));
echo PHP_EOL;
print_r($compressedJson);
```
Pure JSON (749 chars)
```
{"count":3,"names":["New York","London","Paris","Beijing","Moscow"],"cities":[{"id":1,"name":"New York","countryName":"USA","location":{"latitude":40.714606,"longitude"
:-74.0028},"localityType":"BIG_CITY"},{"id":2,"name":"London","countryName":"UK","location":{"latitude":51.507351,"longitude":-0.127696},"localityType":"COUNTRY_CAPITAL
"},{"id":3,"name":"Paris","countryName":"France","location":{"latitude":48.856663,"longitude":2.351556},"localityType":"COUNTRY_CAPITAL"},{"id":4,"name":"Beijing","coun
tryName":"China","location":{"latitude":39.90185,"longitude":116.391441},"localityType":"COUNTRY_CAPITAL"},{"id":5,"name":"Moscow","countryName":"Russia","location":{"l
atitude":55.755864,"longitude":37.617698},"localityType":"COUNTRY_CAPITAL"}]}
```
Compressed JSON (562 chars)
```
[["count","names","cities","a|0|1|2","n|3","New York","London","Paris","Beijing","Moscow","a|5|6|7|8|9","id","name","countryName","location","localityType","a|B|C|D|E|F
","n|1","USA","latitude","longitude","a|J|K","n|e.2Xkv","n|-1C.28G","o|L|M|N","BIG_CITY","o|G|H|5|I|O|P","n|2","UK","n|p.dz7","n|-0.2vFR","o|L|T|U","COUNTRY_CAPITAL","o
|G|R|6|S|V|W","France","n|m.1XNq","n|2.2kQz","o|L|Z|a","o|G|4|7|Y|b|W","n|4","China","n|d.F7F","n|1s.bVh","o|L|f|g","o|G|d|8|e|h|W","n|5","Russia","n|t.1xtN","n|b.3lHA"
,"o|L|l|m","o|G|j|9|k|n|W","a|Q|X|c|i|o","o|3|4|A|p"],"q"]
```

In this example, compression gives an efficiency of 25%. However, the more complex and repetitive the structure, the greater the compression efficiency.

## Testing

```bash
composer test
```

## Credits

- [Islam Zaripov (author)](https://github.com/inkrot)
- [compress-json by Beeno Tung (the same library for NodeJS)](https://github.com/beenotung/compress-json)

## License

[BSD 2-Clause License](./LICENSE) (Free Open Sourced Software)