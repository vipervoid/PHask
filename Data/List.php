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

        return new Cons($a2b($fa->a()), HList::fmap($a2b, $fa->la()));
    }

    public static function append(HList $l1, HList $l2) {
        if ($l1 instanceof Nil) return $l2;

        return new Cons($l1->a(), self::append($l1->la(), $l2));
    }

    public static function foldr(Closure $f, $i, HList $xs) {
        if ($xs instanceof Nil) return $i;

        return $f($xs->a(), self::foldr($f, $i, $xs->la()));
    }

    public static function foldl(Closure $f, $i, HList $xs) {
        if ($xs instanceof Nil) return $i;

        return self::foldl($f, $f($i, $xs->a()), $xs->la());
    }

    public static function show(IShow $xs) {
        $it = $xs;
        $arr = array();
        while (!($it instanceof Nil)) {
            $arr[] = $it->a();
            $it = $it->la();
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
    private $a;
    private $la;

    public function a() {
        return $this->a;
    }

    public function la() {
        return $this->la;
    }

    public function __construct($a, HList $la) {
        $this->a  = $a;
        $this->la = $la;
    }
}

