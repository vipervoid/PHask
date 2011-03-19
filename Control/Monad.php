<?php
interface IMonad {
    public static function bind(IMonad $ma, Closure $a2mb);
    public static function bind_(IMonad $ma, IMonad $mb);
    public static function return_($a);
    public static function fail($str);
}

abstract class Monad implements IMonad {
    public static function bind_(IMonad $ma, IMonad $mb) {
        return static::bind($ma, function ($a) use ($mb) { return $mb; });
    }
}

class MonadException extends Exception {}
