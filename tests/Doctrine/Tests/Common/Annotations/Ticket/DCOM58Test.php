<?php
namespace Doctrine\Tests\Common\Annotations\Ticket;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use Doctrine\Common\Annotations\SimpleAnnotationReader;

//Some class named Entity in the global namespace
include __DIR__ .'/DCOM58Entity.php';

/**
 * @group DCOM58
 */
class DCOM58Test extends \PHPUnit_Framework_TestCase
{
    public function testIssue()
    {
        $reader     = new AnnotationReader();

        self::assertTrue(class_exists(\Doctrine\Common\Annotations\Annotation\IgnoreAnnotation::class), false);

        $result     = $reader->getClassAnnotations(new \ReflectionClass(__NAMESPACE__ . '\MappedClass'));
        self::assertInstanceOf(\Entity::class, $result[0]); // Global entity inside DCOM58Entity.php

        $classAnnotations = array_combine(
            array_map('get_class', $result),
            $result
        );

        self::assertArrayNotHasKey('', $classAnnotations, 'Class "xxx" is not a valid entity or mapped super class.');
    }

    public function testIssueGlobalNamespace()
    {
        $docblock   = '@Entity';
        $parser     = new DocParser();
        $parser->setImports(array(
            '__NAMESPACE__' => 'Doctrine\Tests\Common\Annotations\Ticket\Doctrine\ORM\Mapping'
        ));

        $annots     = $parser->parse($docblock);

        self::assertCount(1, $annots);
        self::assertInstanceOf(Doctrine\ORM\Mapping\Entity::class, $annots[0]);
    }

    public function testIssueNamespaces()
    {
        $docblock   = '@Entity';
        $parser     = new DocParser();
        $parser->addNamespace('Doctrine\Tests\Common\Annotations\Ticket\Doctrine\ORM');

        $annots     = $parser->parse($docblock);

        self::assertCount(1, $annots);
        self::assertInstanceOf(Doctrine\ORM\Entity::class, $annots[0]);
    }

    public function testChoseFirstOneIfHaveMultipleNamespaces()
    {
        $docblock   = '@Entity';
        $parser     = new DocParser();
        $parser->addNamespace('Doctrine\Tests\Common\Annotations\Ticket\Doctrine\ORM\Mapping');
        $parser->addNamespace('Doctrine\Tests\Common\Annotations\Ticket\Doctrine\ORM');

        $annots     = $parser->parse($docblock);

        self::assertCount(1, $annots);
        self::assertInstanceOf(Doctrine\ORM\Mapping\Entity::class, $annots[0]);
    }

    public function testIssueWithNamespacesOrImports()
    {
        $docblock   = '@Entity';
        $parser     = new DocParser();
        $annots     = $parser->parse($docblock);

        self::assertCount(1, $annots);
        self::assertInstanceOf(\Entity::class, $annots[0]);
    }


    public function testIssueSimpleAnnotationReader()
    {
        $reader     = new SimpleAnnotationReader();
        $reader->addNamespace('Doctrine\Tests\Common\Annotations\Ticket\Doctrine\ORM\Mapping');
        $annots     = $reader->getClassAnnotations(new \ReflectionClass(__NAMESPACE__."\MappedClass"));

        self::assertCount(1, $annots);
        self::assertInstanceOf(Doctrine\ORM\Mapping\Entity::class, $annots[0]);
    }

}

/**
 * @Entity
 */
class MappedClass
{

}


namespace Doctrine\Tests\Common\Annotations\Ticket\Doctrine\ORM\Mapping;
/**
* @Annotation
*/
class Entity
{

}

namespace Doctrine\Tests\Common\Annotations\Ticket\Doctrine\ORM;
/**
* @Annotation
*/
class Entity
{

}
