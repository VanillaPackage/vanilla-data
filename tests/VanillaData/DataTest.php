<?php

namespace Rentalhost\VanillaData;

use PHPUnit_Framework_TestCase;

/**
 * Class DataTest
 * @package Rentalhost\VanillaData
 */
class DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test basic methods.
     * @covers Rentalhost\VanillaData\Data::__construct
     * @covers Rentalhost\VanillaData\Data::get
     * @covers Rentalhost\VanillaData\Data::getHTML
     * @covers Rentalhost\VanillaData\Data::getArray
     * @covers Rentalhost\VanillaData\Data::clear
     * @covers Rentalhost\VanillaData\Data::__get
     * @covers Rentalhost\VanillaData\Data::__set
     * @covers Rentalhost\VanillaData\Data::__isset
     * @covers Rentalhost\VanillaData\Data::__unset
     * @covers Rentalhost\VanillaData\Data::get
     * @covers Rentalhost\VanillaData\Data::set
     * @covers Rentalhost\VanillaData\Data::has
     * @covers Rentalhost\VanillaData\Data::remove
     * @covers Rentalhost\VanillaData\Data::offsetGet
     * @covers Rentalhost\VanillaData\Data::offsetSet
     * @covers Rentalhost\VanillaData\Data::offsetExists
     * @covers Rentalhost\VanillaData\Data::offsetUnset
     * @covers Rentalhost\VanillaData\Data::count
     */
    public function testBasic()
    {
        $data = new Data;

        static::assertSame([ ], $data->getArray());

        static::assertSame(null, $data->get('key1'));
        static::assertSame('default', $data->get('key1-default', 'default'));

        static::assertSame(null, $data->getHTML('key1'));
        static::assertSame('default', $data->getHTML('key1-default', 'default'));

        /** @noinspection PhpUndefinedFieldInspection */
        static::assertSame(null, $data->key1);
        static::assertSame(null, $data['key1']);

        $data->set('key1', 'value1');
        static::assertSame('value1', $data->get('key1'));
        static::assertSame('value1', $data->getHTML('key1'));
        static::assertTrue($data->has('key1'));

        /** @noinspection PhpUndefinedFieldInspection */
        $data->key2 = 'value2';
        /** @noinspection PhpUndefinedFieldInspection */
        static::assertSame('value2', $data->key2);
        static::assertTrue(isset( $data->key2 ));

        $data['key3'] = 'value3';
        static::assertSame('value3', $data['key3']);
        /** @noinspection UnSafeIsSetOverArrayInspection */
        static::assertTrue(isset( $data['key3'] ));

        static::assertSame([
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ], $data->getArray());
        static::assertSame(3, count($data));

        $data->remove('key1');
        static::assertSame([
            'key2' => 'value2',
            'key3' => 'value3',
        ], $data->getArray());
        static::assertSame(2, count($data));

        unset( $data->key2 );
        static::assertSame([
            'key3' => 'value3',
        ], $data->getArray());
        static::assertSame(1, count($data));

        unset( $data['key3'] );
        static::assertSame([ ], $data->getArray());
        static::assertSame(0, count($data));

        $data->set('key4', 'value4');
        static::assertSame([
            'key4' => 'value4',
        ], $data->getArray());
        static::assertSame(1, count($data));

        $data->clear();
        static::assertSame([ ], $data->getArray());
        static::assertSame(0, count($data));
    }

    /**
     * Test getHTML method.
     * @covers Rentalhost\VanillaData\Data::getHTML
     */
    public function testGetHTML()
    {
        $data = new Data;
        /** @noinspection PhpUndefinedFieldInspection */
        /** @noinspection HtmlUnknownTag */
        $data->key1 = '<value1>';

        /** @noinspection HtmlUnknownTag */
        static::assertSame('<value1>', $data->get('key1'));
        static::assertSame('&lt;value1&gt;', $data->getHTML('key1'));
    }

    /**
     * Test array on constructor.
     * @covers Rentalhost\VanillaData\Data::__construct
     */
    public function testArrayConstructor()
    {
        $data = new Data([ 'key1' => 'value1' ]);

        static::assertSame([ 'key1' => 'value1' ], $data->getArray());
    }

    /**
     * Test self instance on constructor.
     * @covers Rentalhost\VanillaData\Data::__construct
     */
    public function testSelfInstanceConstructor()
    {
        $data = new Data(new Data([ 'key1' => 'value1' ]));

        static::assertSame([ 'key1' => 'value1' ], $data->getArray());
    }

    /**
     * Test non-scalar get.
     * @covers Rentalhost\VanillaData\Data::get
     */
    public function testNonScalarGet()
    {
        $data = new Data;

        static::assertNull($data->get(null));
        static::assertNull($data->get(new Data));
        static::assertNull($data->get(true));
        static::assertNull($data->get(false));

        static::assertSame('default', $data->get(null, 'default'));
    }

    /**
     * Test iterator.
     * @covers Rentalhost\VanillaData\Data::getIterator
     */
    public function testIterator()
    {
        $array = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];
        $data  = new Data($array);

        // Basic foreach.
        $result = [ ];
        foreach ($data->getIterator() as $key => $value) {
            $result[$key] = $value;
        }

        static::assertSame($result, $array);

        // Test iterator.
        $dataIterator = $data->getIterator();

        static::assertSame(3, $dataIterator->count());

        static::assertSame('key1', $dataIterator->key());
        static::assertSame('value1', $dataIterator->current());
        static::assertSame(true, $dataIterator->valid());

        // Test line-by-line.
        $dataIterator->next();
        $dataIterator->rewind();
        static::assertSame('key1', $dataIterator->key());
        static::assertSame('value1', $dataIterator->current());
        static::assertSame(true, $dataIterator->valid());

        $dataIterator->next();
        static::assertSame('key2', $dataIterator->key());
        static::assertSame('value2', $dataIterator->current());
        static::assertSame(true, $dataIterator->valid());

        $dataIterator->next();
        static::assertSame('key3', $dataIterator->key());
        static::assertSame('value3', $dataIterator->current());
        static::assertSame(true, $dataIterator->valid());

        $dataIterator->next();
        static::assertSame(null, $dataIterator->key());
        static::assertSame(null, $dataIterator->current());
        static::assertSame(false, $dataIterator->valid());
    }

    /**
     * Test a self iterator.
     * @covers Rentalhost\VanillaData\Data::getIterator
     * @covers Rentalhost\VanillaData\DataIterator::current
     */
    public function testIteratorSelf()
    {
        $data = new Data([ 'outer' => [ 'inner' => 'value' ] ]);

        static::assertInternalType('array', $data->outer);

        foreach ($data->getIterator(true) as $item) {
            static::assertInstanceOf(Data::class, $item);
            static::assertSame('value', $item->inner);
        }
    }

    /**
     * Test the getSelf method.
     * @covers Rentalhost\VanillaData\Data::getSelf
     */
    public function testGetSelf()
    {
        $data     = new Data([ 'key1' => [ 'key1a' => 1, 'key1b' => 2 ] ]);
        $dataSelf = $data->getSelf('key1');

        static::assertInstanceOf(Data::class, $dataSelf);
        static::assertSame([ 'key1a' => 1, 'key1b' => 2 ], $dataSelf->getArray());
        static::assertSame(2, count($dataSelf));

        $dataInner   = new Data([ 'key1' => 'value1' ]);
        $dataWrapper = new Data([ 'key2' => $dataInner ]);

        static::assertInstanceOf(Data::class, $dataWrapper->get('key2'));
        static::assertInstanceOf(Data::class, $dataWrapper->getSelf('key2'));
        static::assertSame($dataInner, $dataWrapper->getSelf('key2'));
        static::assertSame('value1', $dataWrapper->getSelf('key2')->key1);
    }

    /**
     * Test a getSelf over invalid key.
     * @covers Rentalhost\VanillaData\Data::getSelf
     */
    public function testGetSelfEmpty()
    {
        $data     = new Data;
        $dataSelf = $data->getSelf('invalid');

        static::assertInstanceOf(Data::class, $dataSelf);
        static::assertSame([ ], $dataSelf->getArray());

        $dataSelf = $data->getSelf('invalid', [ 'key1' => 'value1' ]);

        static::assertInstanceOf(Data::class, $dataSelf);
        static::assertSame([ 'key1' => 'value1' ], $dataSelf->getArray());

        $dataSelf = $data->getSelf('invalid', false);

        static::assertFalse($dataSelf);
    }

    /**
     * Test extend method.
     * @covers Rentalhost\VanillaData\Data::extend
     */
    public function testExtend()
    {
        $data = Data::extend();

        static::assertSame([ ], $data->getArray());

        $data = Data::extend([ ]);

        static::assertSame([ ], $data->getArray());

        $data = Data::extend([ ], [ ]);

        static::assertSame([ ], $data->getArray());

        $data = Data::extend([ 'foo' => 'bar' ], [ ]);

        static::assertSame([ 'foo' => 'bar' ], $data->getArray());

        $data = Data::extend([ ], [ 'foo' => 'bar' ]);

        static::assertSame([ 'foo' => 'bar' ], $data->getArray());

        $data = Data::extend([ 'foo' => 'old' ], [ 'foo' => 'bar' ]);

        static::assertSame([ 'foo' => 'bar' ], $data->getArray());

        $data = Data::extend([ 'foo1' => 'value1', 'foo2' => 'value2' ], [ 'foo1' => 'newValue' ]);

        static::assertSame([ 'foo1' => 'newValue', 'foo2' => 'value2' ], $data->getArray());

        $data = Data::extend([ 'foo1' => 'value1' ], [ 'foo1' => 'newValue', 'foo2' => 'value2' ]);

        static::assertSame([ 'foo1' => 'newValue', 'foo2' => 'value2' ], $data->getArray());
    }

    /**
     * Test from method.
     * @covers Rentalhost\VanillaData\Data::from
     */
    public function testFrom()
    {
        $data = new Data();

        static::assertSame($data, Data::from($data));
        static::assertNotSame(new Data([ 'key' => 'value' ]), Data::from([ 'key' => 'value' ]));
    }

    /**
     * Test setArray.
     * @covers Rentalhost\VanillaData\Data::setArray
     */
    public function testSetArray()
    {
        $data = new Data([ 'key1' => 'value1' ]);

        static::assertSame([ 'key1' => 'value1' ], $data->getArray());

        $data->setArray([ 'key2' => 'value2' ]);
        static::assertSame([ 'key1' => 'value1', 'key2' => 'value2' ], $data->getArray());

        $data->setArray(new Data([ 'key3' => 'value3' ]));
        static::assertSame([ 'key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3' ], $data->getArray());

        // Replaces.
        $data->setArray(new Data([ 'key3' => 'value3b' ]));
        static::assertSame([ 'key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3b' ], $data->getArray());

        $data->setArray(new Data([ 'key3' => 'value3c', 'key4' => 'value4' ]), false);
        static::assertSame([ 'key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3b', 'key4' => 'value4' ], $data->getArray());

        unset( $data->key2, $data->key3, $data->key4 );
        static::assertSame([ 'key1' => 'value1' ], $data->getArray());

        $data->setArray(new Data([ 'key1' => 'value1b' ]), false);
        static::assertSame([ 'key1' => 'value1' ], $data->getArray());

        $data->setArray(new Data([ 'key1' => 'value1b' ]));
        static::assertSame([ 'key1' => 'value1b' ], $data->getArray());

        /** @noinspection PhpUndefinedFieldInspection */
        $data->key3 = 'value3';
        $data->setArray(new Data([ 'key1' => 'value1', 'key3' => 'value3b', 'key4' => 'value4' ]), false);
        static::assertSame([ 'key1' => 'value1b', 'key3' => 'value3', 'key4' => 'value4' ], $data->getArray());

        $data = new Data([ 'key1' => 'value1' ]);
        $data->setArray(false);
        $data->setArray(null);
        $data->setArray(0);
        $data->setArray('');
        static::assertSame([ 'key1' => 'value1' ], $data->getArray());
    }

    /**
     * Test reconfigureArray.
     * @covers Rentalhost\VanillaData\Data::reconfigureArray
     */
    public function testReconfigureArray()
    {
        $data = new Data([ 'key1' => 'value1' ]);

        static::assertSame([ 'key1' => 'value1' ], $data->getArray());

        $data->reconfigureArray();
        static::assertSame([ ], $data->getArray());

        $data = new Data([ 'key1' => 'value1' ]);

        $data->reconfigureArray(false);
        static::assertSame([ ], $data->getArray());

        $data->reconfigureArray([ 'key2' => 'value2' ]);
        static::assertSame([ 'key2' => 'value2' ], $data->getArray());

        $data->reconfigureArray(new Data([ 'key3' => 'value3' ]));
        static::assertSame([ 'key3' => 'value3' ], $data->getArray());
    }

    /**
     * Test the InvalidDataTypeException on setArray.
     * @dataProvider dataInvalidDataTypeException
     */
    public function testInvalidDataTypeExceptionOnSetArray()
    {
        static::setExpectedException(Exception\InvalidDataTypeException::class);
        (new Data())->setArray('invalid');
    }

    /**
     * Test the InvalidDataTypeException.
     *
     * @param mixed $invalidData Invalid data to throws the exception.
     *
     * @dataProvider dataInvalidDataTypeException
     */
    public function testInvalidDataTypeException($invalidData)
    {
        static::setExpectedException(Exception\InvalidDataTypeException::class);
        new Data($invalidData);
    }

    /**
     * @return array
     */
    public function dataInvalidDataTypeException()
    {
        return [
            [ '' ],
            [ 'string' ],
            [ 1 ],
            [ 0 ],
            [ true ],
            [ (object) [ ] ],
        ];
    }
}
