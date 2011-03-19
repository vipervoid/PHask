<?php
abstract class HList implements IMonad, IFunctor, IShow {
    private static function isHList($ma) {
        if (!($ma instanceof HList)) {
            throw new MonadException("Need HList value");
        }
    }

    public static function bind(IMonad $ma, Closure $a2mb) {
    /*
    m >>= k             = foldr ((++) . k) [] m
    TODO: >> test.. current implementation is completely untested
*/    
        self::isHList($ma);

        $fn = function ($xs, $x) use ($a2mb) {
            return HList::append($a2mb($x), $xs);
        };
        
        return self::foldr($fn, new Nil(), $ma);
    }

    public static function bind_(IMonad $ma, IMonad $mb) {
        // TODO: Implement
        // foldr ((++) . (\ _ -> k)) [] m
    }

    public static function return_($a) {
        return new Cons($a, new Nil());
    }

    public static function fail($str) {
        return new Nil();
    }

    public static function fmap(Closure $a2b, $fa) {
        self::isHList($fa);

        if ($fa instanceof Nil) return new Nil();

        return new Cons($a2b(HList::head($fa)), HList::fmap($a2b, HList::tail($fa)));
    }

    public static function append(HList $l1, HList $l2) {
        if ($l1 instanceof Nil) return $l2;

        return new Cons(HList::head($l1), self::append(HList::tail($l1), $l2));
    }

    public static function foldr(Closure $f, $acc, HList $xs) {
        if ($xs instanceof Nil) return $acc;

        return $f(HList::head($xs), self::foldr($f, $acc, HList::tail($xs)));
    }

    public static function foldl(Closure $f, $acc, HList $xs) {
        if ($xs instanceof Nil) return $acc;

        return self::foldl($f, $f($acc, HList::head($xs)), HList::tail($xs));
    }

    public static function show(IShow $xs) {
        return '[' . implode(',', self::toArray($xs)) . ']';
    }

    public static function head(Cons $l) {
        return $l->x();
    }

    public static function tail(Cons $l) {
        return $l->xs();
    }

    public static function null_(HList $l) {
        return ($l instanceof Nil);
    }

    public function __toString() {
        return self::show($this);
    }

    public static function fromArray(array $arr) {
        return array_reduce(
            array_reverse($arr),
            function ($xs, $x) { return new Cons($x, $xs); },
            new Nil());
    }

    public static function toArray(HList $xs) {
        return self::foldr(
            function ($x, $xs) { array_unshift($xs, $x); return $xs; },
            array(), $xs
        );
    }
}

final class Nil extends HList { }
final class Cons extends HList {
    private $x;
    private $xs;

    public function x() {
        return $this->x;
    }

    public function xs() {
        return $this->xs;
    }

    public function __construct($x, HList $xs) {
        $this->x  = $x;
        $this->xs = $xs;
    }
}

