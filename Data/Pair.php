<?php
final class Pair {
    private $a;
    private $b;

    public function __construct($a, $b) {
        $this->a = $a;
        $this->b = $b;
    }

    public function a() {
        return $this->a;
    }

    public function b() {
        return $this->b();
    }

    public static function fst(Pair $p) {
        return $pair->a();
    }

    public static function snd(Pair $p) {
        return $pair->b();
    }

    public static function curry($f, $a, $b) {
        return $f(new Pair($a, $b));
    }

    public static function uncurry($f, Pair $p) {
        return $f(self::fst($p), self::snd($p));
    }

    public static functions swap(Pair $p) {
        return new Pair(self::snd($p), self::fst($p));
    }
}
