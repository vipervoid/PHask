<?php
interface IFunctor {
    public static function fmap($a2b, $fa);
}

// Make abstract and implement bind_ and fail?
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

abstract class Maybe implements IMonad, IFunctor {
    private static function isMaybe($ma) {
        if (!($ma instanceof Maybe)) {
            throw new MonadException("Need Maybe value");
        }
    }

    public static function bind(IMonad $ma, Closure $a2mb) {
        self::isMaybe($ma);

        // If we have a Nothing, we return Nothing
        if ($ma instanceof Nothing) return new Nothing();

        // Otherwise we have a Just and we call the
        // a2mb function
        // TODO: Add checks to verify type sig of function
        return $a2mb($ma->a);
    }

    public static function return_($a) {
        return new Just($a);
    }

    public static function fail($str) {
        return new Nothing();
    }

    public static function fmap($a2b, $fa) {
        self::isMaybe($fa);

        // If we have a Nothing, we return Nothing
        if ($fa instanceof Nothing) return new Nothing();

        // TODO: Add check for $a2b
        return new Just($a2b($fa->a));
    }

    public static function show(Maybe $m) {
        if ($m instanceof Nothing) return "Nothing" . PHP_EOL;
        return "Just " . $m->a . PHP_EOL;
    }
}

final class Nothing extends Maybe { }
final class Just extends Maybe {
    public $a;
    public function __construct($a) {
        $this->a = $a;
    }
}

class MonadException extends Exception {}


abstract class HList implements IMonad, IFunctor {
    private static function isHList($ma) {
        if (!($ma instanceof HList)) {
            throw new MonadException("Need HList value");
        }
    }

    public static function bind(IMonad $ma, Closure $a2mb) {
    /*
    m >>= k             = foldr ((++) . k) [] m
    m >> k              = foldr ((++) . (\ _ -> k)) [] m

    TODO: >> and test
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

    public static function fmap($a2b, $fa) {
        self::isHList($fa);

        if ($fa instanceof Nil) return new Nil();

        return new Cons($a2b($fa->a), HList::fmap($a2b, $fa->la));
    }

    public static function append(HList $l1, HList $l2) {
        if ($l1 instanceof Nil) return $l2;

        return new Cons($l1->a, self::append($l1->la, $l2));
    }

    public static function foldr(Closure $f, $i, HList $xs) {
        if ($xs instanceof Nil) return $i;

        return $f($xs->a, self::foldr($f, $i, $xs->la));
    }

    public static function show(HList $xs) {
        $it = $xs;
        $arr = array();
        while (!($it instanceof Nil)) {
            $arr[] = $it->a;
            $it = $it->la;
        }

        return '[' . implode(',', $arr) . ']' . PHP_EOL;
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
    public $a;
    public $la;
    public function __construct($a, HList $la) {
        $this->a  = $a;
        $this->la = $la;
    }
}

function main() {
    // Expected result: Just(14)
    $m1 = Maybe::bind(new Just(4), function($a) { return new Just($a + 10); });
    echo Maybe::show($m1);

    // Expected result: Just(5)
    $m2 = Monad::bind_(new Just(4), new Just(5));
    echo Maybe::show($m2);

    $f1 = Maybe::fmap(function($x) { return 5*$x; }, new Just(5));
    echo Maybe::show($f1);

    $c123 = HList::fromArray(range(1,3));
    $c45  = HList::fromArray(range(4,6));
    var_dump(HList::toArray($c123));
    $f2 = HList::fmap(function($x) { return 2*$x; }, $c123);
    echo HList::show($f2);

    $l1 = HList::append(new Cons(1, new Nil()), new Cons(2, new Nil()));
    echo HList::show($l1);
    
    $fold = HList::foldr(function ($xs, $x) { return $x + $xs; }, 0, $c123);
    var_dump($fold);

//    $b = HList::bind($c123, function($xs) use ($c45) { return HList::append($xs, $c45); } );
//    echo HList::show($b);
    echo HList::show(HList::fromArray(array(1,2,3)));
}

main();
