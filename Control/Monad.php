<?php
interface IMonad {
    public static function bind(IMonad $ma, Closure $a2mb);
    public static function return_($a);

}

abstract class Monad implements IMonad {
    public static function bind_(IMonad $ma, IMonad $mb) {
        return Maybe::bind($ma, function ($a) use ($mb) { return $mb; });
    }

    public static function fail($str) {
        throw new MonadException($str);
    }
}


class MonadException extends Exception {}
