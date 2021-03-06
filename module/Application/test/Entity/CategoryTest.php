<?php

namespace ApplicationTest\Entity;

use Application\Entity\Category;
use Application\Entity\User;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testGettersWithoutSetter(): void
    {
        $id = 1;
        $category = new Category();
        $reflectionClass = new \ReflectionClass($category);
        $reflectedProperty = $reflectionClass->getProperty('id');
        $reflectedProperty->setAccessible(true);

        $reflectedProperty->setValue($category, $id);

        self::assertSame($id, $category->getId());
    }

    public function testSetterAndGetters(): void
    {
        $category = new Category();

        $user = new User();
        $category->setUser($user);
        self::assertSame($user, $category->getUser());

        $description = 'Description';
        $category->setDescription($description);
        self::assertSame($description, $category->getDescription());

        $status = Category::STATUS_ACTIVE;
        $category->setStatus($status);
        self::assertSame($status, $category->getStatus());

        self::assertInstanceOf(InputFilterInterface::class, $category->getInputFilter());
    }

    public function testSetInputFilterException(): void
    {
        $category = new Category();
        self::expectException(\Exception::class);
        $category->setInputFilter(new InputFilter());
    }

    public function testStatusException(): void
    {
        $category = new Category();
        self::expectException(\Exception::class);
        $category->setStatus(2);
    }

    public function testArrayExchangeAndCopy(): void
    {
        $category = new Category();

        $user = new User();
        $description = 'description';
        $status = Category::STATUS_INACTIVE;

        $category->exchangeArray([
            'user' => $user,
            'description' => $description,
            'status' => $status,
        ]);

        $copy = $category->getArrayCopy();

        self::assertSame($copy['user'], $user);
        self::assertSame($copy['description'], $description);
        self::assertSame($copy['status'], $status);
    }
}
