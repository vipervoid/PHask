<?php
abstract class Maybe implements IMonad, IFunctor, IShow {
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
        return $a2mb($ma->a());
    }

    public static function bind_(IMonad $ma, IMonad $mb) {
        return self::bind($ma, function ($a) use ($mb) { return $mb; });
    }

    public static function return_($a) {
        return new Just($a);
    }

    public static function fail($str) {
        return new Nothing();
    }

    public static function fmap(Closure $a2b, $fa) {
        self::isMaybe($fa);

        // If we have a Nothing, we return Nothing
        if ($fa instanceof Nothing) return new Nothing();

        // TODO: Add check for $a2b
        return new Just($a2b($fa->a()));
    }

    public function __toString() {
        return static::show($this);
    }
}

final class Nothing extends Maybe {
    public static function show(IShow $show) {
        return "Nothing";
    }
}

final class Just extends Maybe {
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
