<?php

namespace Rentalhost\VanillaData;

use PHPUnit_Framework_TestCase;

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

        $this->assertSame([], $data->getArray());

        $this->assertSame(null, $data->get("key1"));
        $this->assertSame("default", $data->get("key1-default", "default"));

        $this->assertSame(null, $data->getHTML("key1"));
        $this->assertSame("default", $data->getHTML("key1-default", "default"));

        $this->assertSame(null, $data->key1);
        $this->assertSame(null, $data["key1"]);

        $data->set("key1", "value1");
        $this->assertSame("value1", $data->get("key1"));
        $this->assertSame("value1", $data->getHTML("key1"));
        $this->assertTrue($data->has("key1"));

        $data->key2 = "value2";
        $this->assertSame("value2", $data->key2);
        $this->assertTrue(isset($data->key2));

        $data["key3"] = "value3";
        $this->assertSame("value3", $data["key3"]);
        $this->assertTrue(isset($data["key3"]));

        $this->assertSame([
            "key1" => "value1",
            "key2" => "value2",
            "key3" => "value3"
        ], $data->getArray());
        $this->assertSame(3, count($data));

        $data->remove("key1");
        $this->assertSame([
            "key2" => "value2",
            "key3" => "value3"
        ], $data->getArray());
        $this->assertSame(2, count($data));

        unset($data->key2);
        $this->assertSame([
            "key3" => "value3"
        ], $data->getArray());
        $this->assertSame(1, count($data));

        unset($data["key3"]);
        $this->assertSame([], $data->getArray());
        $this->assertSame(0, count($data));

        $data->set("key4", "value4");
        $this->assertSame([
            "key4" => "value4"
        ], $data->getArray());
        $this->assertSame(1, count($data));

        $data->clear();
        $this->assertSame([], $data->getArray());
        $this->assertSame(0, count($data));
    }

    /**
     * Test getHTML method.
     * @covers Rentalhost\VanillaData\Data::getHTML
     */
    public function testGetHTML()
    {
        $data = new Data;
        $data->key1 = "<value1>";

        $this->assertSame("<value1>", $data->get("key1"));
        $this->assertSame("&lt;value1&gt;", $data->getHTML("key1"));
    }

    /**
     * Test array on constructor.
     * @covers Rentalhost\VanillaData\Data::__construct
     */
    public function testArrayConstructor()
    {
        $data = new Data([ "key1" => "value1" ]);

        $this->assertSame([ "key1" => "value1" ], $data->getArray());
    }

    /**
     * Test self instance on constructor.
     * @covers Rentalhost\VanillaData\Data::__construct
     */
    public function testSelfInstanceConstructor()
    {
        $data = new Data(new Data([ "key1" => "value1" ]));

        $this->assertSame([ "key1" => "value1" ], $data->getArray());
    }

    /**
     * Test non-scalar get.
     * @covers Rentalhost\VanillaData\Data::get
     */
    public function testNonScalarGet()
    {
        $data = new Data;

        $this->assertNull($data->get(null));
        $this->assertNull($data->get(new Data));
        $this->assertNull($data->get(true));
        $this->assertNull($data->get(false));

        $this->assertSame("default", $data->get(null, "default"));
    }

    /**
     * Test iterator.
     * @covers Rentalhost\VanillaData\Data::getIterator
     */
    public function testIterator()
    {
        $array = [
            "key1" => "value1",
            "key2" => "value2",
            "key3" => "value3",
        ];
        $data = new Data($array);

        // Basic foreach.
        $result = [];
        foreach ($data->getIterator() as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertSame($result, $array);

        // Test iterator.
        $dataIterator = $data->getIterator();

        $this->assertSame(3, $dataIterator->count());

        $this->assertSame("key1", $dataIterator->key());
        $this->assertSame("value1", $dataIterator->current());
        $this->assertSame(true, $dataIterator->valid());

        // Test line-by-line.
        $dataIterator->next();
        $dataIterator->rewind();
        $this->assertSame("key1", $dataIterator->key());
        $this->assertSame("value1", $dataIterator->current());
        $this->assertSame(true, $dataIterator->valid());

        $dataIterator->next();
        $this->assertSame("key2", $dataIterator->key());
        $this->assertSame("value2", $dataIterator->current());
        $this->assertSame(true, $dataIterator->valid());

        $dataIterator->next();
        $this->assertSame("key3", $dataIterator->key());
        $this->assertSame("value3", $dataIterator->current());
        $this->assertSame(true, $dataIterator->valid());

        $dataIterator->next();
        $this->assertSame(null, $dataIterator->key());
        $this->assertSame(null, $dataIterator->current());
        $this->assertSame(false, $dataIterator->valid());
    }

    /**
     * Test the getSelf method.
     * @covers Rentalhost\VanillaData\Data::getSelf
     */
    public function testGetSelf()
    {
        $data = new Data([ "key1" => [ "key1a" => 1, "key1b" => 2 ] ]);
        $dataSelf = $data->getSelf("key1");

        $this->assertInstanceOf(Data::class, $dataSelf);
        $this->assertSame([ "key1a" => 1, "key1b" => 2 ], $dataSelf->getArray());
        $this->assertSame(2, count($dataSelf));
    }

    /**
     * Test a getSelf over invalid key.
     * @covers Rentalhost\VanillaData\Data::getSelf
     */
    public function testGetSelfEmpty()
    {
        $data = new Data;
        $dataSelf = $data->getSelf("invalid");

        $this->assertInstanceOf(Data::class, $dataSelf);
        $this->assertSame([], $dataSelf->getArray());

        $dataSelf = $data->getSelf("invalid", [ "key1" => "value1" ]);

        $this->assertInstanceOf(Data::class, $dataSelf);
        $this->assertSame([ "key1" => "value1" ], $dataSelf->getArray());

        $dataSelf = $data->getSelf("invalid", false);

        $this->assertFalse($dataSelf);
    }

    /**
     * Test setArray.
     * @covers Rentalhost\VanillaData\Data::setArray
     */
    public function testSetArray()
    {
        $data = new Data([ "key1" => "value1" ]);

        $this->assertSame([ "key1" => "value1" ], $data->getArray());

        $data->setArray([ "key2" => "value2" ]);
        $this->assertSame([ "key1" => "value1", "key2" => "value2" ], $data->getArray());

        $data->setArray(new Data([ "key3" => "value3" ]));
        $this->assertSame([ "key1" => "value1", "key2" => "value2", "key3" => "value3" ], $data->getArray());

        // Replaces.
        $data->setArray(new Data([ "key3" => "value3b" ]));
        $this->assertSame([ "key1" => "value1", "key2" => "value2", "key3" => "value3b" ], $data->getArray());

        $data->setArray(new Data([ "key3" => "value3c", "key4" => "value4" ]), false);
        $this->assertSame([ "key1" => "value1", "key2" => "value2", "key3" => "value3b", "key4" => "value4" ], $data->getArray());

        unset($data->key2, $data->key3, $data->key4);
        $this->assertSame([ "key1" => "value1" ], $data->getArray());

        $data->setArray(new Data([ "key1" => "value1b" ]), false);
        $this->assertSame([ "key1" => "value1" ], $data->getArray());

        $data->setArray(new Data([ "key1" => "value1b" ]));
        $this->assertSame([ "key1" => "value1b" ], $data->getArray());

        $data->key3 = "value3";
        $data->setArray(new Data([ "key1" => "value1", "key3" => "value3b", "key4" => "value4" ]), false);
        $this->assertSame([ "key1" => "value1b", "key3" => "value3", "key4" => "value4" ], $data->getArray());
    }

    /**
     * Test reconfigureArray.
     * @covers Rentalhost\VanillaData\Data::reconfigureArray
     */
    public function testReconfigureArray()
    {
        $data = new Data([ "key1" => "value1" ]);

        $this->assertSame([ "key1" => "value1" ], $data->getArray());

        $data->reconfigureArray();
        $this->assertSame([], $data->getArray());

        $data->reconfigureArray([ "key2" => "value2" ]);
        $this->assertSame([ "key2" => "value2" ], $data->getArray());

        $data->reconfigureArray(new Data([ "key3" => "value3" ]));
        $this->assertSame([ "key3" => "value3" ], $data->getArray());
    }

    /**
     * Test the InvalidDataTypeException on setArray.
     * @dataProvider dataInvalidDataTypeException
     */
    public function testInvalidDataTypeExceptionOnSetArray($invalidData)
    {
        $this->setExpectedException(Exception\InvalidDataTypeException::class);
        (new Data())->setArray("invalid");
    }

    /**
     * Test the InvalidDataTypeException.
     * @dataProvider dataInvalidDataTypeException
     */
    public function testInvalidDataTypeException($invalidData)
    {
        $this->setExpectedException(Exception\InvalidDataTypeException::class);
        new Data($invalidData);
    }

    public function dataInvalidDataTypeException()
    {
        return [
            [ "string" ],
            [ 1 ],
            [ true ],
            [ false ],
            [ (object) [] ]
        ];
    }
}
