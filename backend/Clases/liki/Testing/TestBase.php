<?php
namespace Liki\Testing;

abstract class TestBase{
    public static function assertEqual(callable $f){
        $f();
    }
    public static function assertTrue(callable $f){
        $f();
    }
    public static function assertNull(callable $f){
        $f();
    }
    public static function assertThrows(callable $f){
        $f();
    }
}