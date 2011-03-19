<?php
final class MaybeFactory {
    private static $nothing;

    public static function nothing() {
        if (!self::$nothing) self::$nothing = new Nothing();
        return self::$nothing;
    }

    public static function just($a) {
        return new Just($a);
    }
}

abstract class Maybe extends Monad implements IMonad, IFunctor, IShow {
    private static function isMaybe($ma) {
        if (!($ma instanceof Maybe)) {
            throw new MonadException("Need Maybe value");
        }
    }

    public static function bind(IMonad $ma, Closure $a2mb) {
        // If we have a Nothing, we return Nothing
        if (self::isNothing($ma)) return MaybeFactory::nothing();

        // Otherwise we have a Just and we call the
        // a2mb function
        // TODO: Add checks to verify type sig of function
        return $a2mb($ma->a());
    }
    
    public static function return_($a) {
        return MaybeFactory::just($a);
    }

    public static function fail($str) {
        return MaybeFactory::nothing();
    }

    public static function fmap(Closure $a2b, $fa) {
        // If we have a Nothing, we return Nothing
        if (self::isNothing($fa)) return MaybeFactory::nothing();

        // TODO: Add check for $a2b
        return MaybeFactory::just($a2b($fa->a()));
    }
    
    public static function maybe_($b, Closure $f, Maybe $a) {
        if (self::isNothing($a)) return $b;

        return $f($a->a());
    }

    public static function fromJust(Maybe $a) {
        if (self::isNothing($a)) {
            throw new MaybeException("Nothing!");
        }

        return $a->a();
    }

    public static function fromMaybe($d, Maybe $a) {
        if (self::isNothing($a)) return $d;
        return $a->a();
    }

    public static function maybeToList(Maybe $a) {
        if (self::isNothing($a)) return HListFactory::nil();
        return HListFactory::cons($a->a(), HListFactory::nil());
    }

    public static function listToMabye(HList $l) {
        if (HList::null_($l)) return MaybeFactory::nothing();
        return MaybeFactory::just(HList::head($l));
    }

    public static function catMaybes(HList $l) {
        return HList::foldr(function ($x, $xs) {
            if (self::isJust($x)) { $xs[] = $x->a(); }
            return $xs;
        }, HListFactory::nil(), $l);
    }

    public static function mapMaybe(Closure $f, HList $as) {
        if (HList::null_($as)) return HListFactory::nil();
        $rs = self::mapMaybe($f, HList::tail($as));
        $m = $f(HList::head($as));
        if (self::isNothing($m)) {
            return $rs;
        } else {
            return HListFactory::cons($m->a(), $rs);
        }
    }

    public static function isJust(Maybe $a) {
        self::isMaybe($a);
        return ($a instanceof IMaybeJust);
    }

    public static function isNothing(Maybe $a) {
        self::isMaybe($a);
        return ($a instanceof IMaybeNothing);
    }

    public function __toString() {
        return static::show($this);
    }
}

interface IMaybeNothing {}

final class Nothing extends Maybe implements IMaybeNothing {
    public static function show(IShow $show) {
        return "Nothing";
    }
}

interface IMaybeJust {}

final class Just extends Maybe implements IMaybeJust {
    private $a;

    public function a() {
        return $this->a;
    }

    public function __construct($a) {
        $this->a = $a;
    }

    public static function show(IShow $show) {
        return "Just " . $show->a();
    }
}

class MaybeException extends Exception {}
