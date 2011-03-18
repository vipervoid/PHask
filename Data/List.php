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
    m >> k              = foldr ((++) . (\ _ -> k)) [] m

    TODO: >> and test.. current implementation is completely untested
*/    
        self::isHList($ma);

        $fn = function ($xs, $x) use ($a2mb) {
            return HList::append($a2mb($x), $xs);
        };
        
        return self::foldr($fn, new Nil(), $ma);
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

        return new Cons($a2b($fa->head()), HList::fmap($a2b, $fa->tail()));
    }

    public static function append(HList $l1, HList $l2) {
        if ($l1 instanceof Nil) return $l2;

        return new Cons($l1->head(), self::append($l1->tail(), $l2));
    }

    public static function foldr(Closure $f, $acc, HList $xs) {
        if ($xs instanceof Nil) return $acc;

        return $f($xs->head(), self::foldr($f, $acc, $xs->tail()));
    }

    public static function foldl(Closure $f, $acc, HList $xs) {
        if ($xs instanceof Nil) return $acc;

        return self::foldl($f, $f($acc, $xs->head()), $xs->tail());
    }

    public static function show(IShow $xs) {
        $it = $xs;
        $arr = array();
        while (!($it instanceof Nil)) {
            $arr[] = $it->head();
            $it = $it->tail();
        }

        return '[' . implode(',', $arr) . ']';
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
        return array_reverse(self::foldr(
            function ($x, $xs) { $xs[] = $x; return $xs; },
            array(), $xs
        ));
    }
}

final class Nil extends HList { }
final class Cons extends HList {
    private $head;
    private $tail;

    public function head() {
        return $this->head;
    }

    public function tail() {
        return $this->tail;
    }

    public function __construct($head, HList $tail) {
        $this->head = $head;
        $this->tail = $tail;
    }
}

