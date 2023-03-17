<?php

namespace CompressJson\Memory;

use CompressJson\Core\Utils;
use CompressJson\Core\Value;
use CompressJson\DataTypes\JsonBoolean;
use CompressJson\DataTypes\JsonNumber;
use CompressJson\DataTypes\JsonString;
use CompressJson\Exception\UnsupportedDataTypeException;

abstract class AbstractMemory
{
    // Items: key -> value
    protected StoreInterface $store;

    // Items: value -> key
    protected CacheInterface $cache;

    // key increment counter
    protected int $keyCount = 0;

    public function __construct(StoreInterface $store, CacheInterface $cache)
    {
        $this->store = $store;
        $this->cache = $cache;
    }

    protected function getNextKeyCount(): int {
        return $this->keyCount++;
    }

    /**
     * @return string[]
     */
    public function memToStringValues(): array
    {
        $arr = $this->store->toArray();
        return array_map(
            fn($el) => (string) $el,
            $arr
        );
    }

    public function getValueKey(Value $value): string
    {
        if ($this->cache->hasValue($value)) {
            return $this->cache->getValue($value);
        }
        $id = $this->getNextKeyCount();
        $key = JsonNumber::num_to_s($id);
        $this->store->add($value);
        $this->cache->setValue($value, new Value($key));
        return $key;
    }

    /**
     * @param $keys string[]
     */
    public function getSchema(array $keys): Value
    {
        /*if (config.sort_key) {
          keys.sort()
        }*/
        $schema = implode(',', $keys);
        if ($this->cache->hasSchema($schema)) {
            return $this->cache->getSchema($schema);
        }
        $keyId = new Value($this->addValue($keys, null));
        $this->cache->setSchema($schema, $keyId);
        return $keyId;
    }

    /**
     * @param $data object|string|array|bool|int|null|float
     * @param $parent array|object|null
     * @return Value
     * @throws UnsupportedDataTypeException
     */
    public function addValue(mixed $data, mixed $parent): string
    {
        if ($data === null) {
            return '';
        }
        else if (is_object($data) || Utils::isAssocArray($data)) {
            $dataArray = $data;
            if (is_object($data)) {
                $dataArray = get_object_vars($data);
            }
            $keys = array_keys($dataArray);
            if (count($keys) === 0) {
                return $this->getValueKey(
                    new Value('o|')
                );
            }
            $acc = 'o';
            $keyId = $this->getSchema($keys);
            $acc .= '|' . $keyId;
            foreach ($keys as $key) {
                $item = $dataArray[$key];
                $v = $this->addValue($item, $data);
                $acc .= '|' . $v;
            }
            return $this->getValueKey(
                new Value($acc)
            );
        }
        else if (is_array($data)) {
            $acc = 'a';
            foreach ($data as $item) {
                $key = $item === null ? '_' : $this->addValue($item, $data);
                $acc .= '|' . $key;
            }
            if ($acc === 'a') {
                $acc = 'a|';
            }
            return $this->getValueKey(
                new Value($acc)
            );
        }
        else if (is_bool($data)) {
            return $this->getValueKey(
                new Value(
                    JsonBoolean::encodeBoolean($data)
                )
            );
        }
        else if (is_string($data)) {
            return $this->getValueKey(
                new Value(
                    JsonString::encodeString($data)
                )
            );
        }
        else if (is_numeric($data)) {
            return $this->getValueKey(
                new Value(
                    JsonNumber::encodeNumber($data)
                )
            );
        }
        throw new UnsupportedDataTypeException($data);
    }
}