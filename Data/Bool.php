<?php
abstract class HBool implements IShow {
    public static function otherwise() {
        return new HTrue();
    }

    public static function hand(HBool $l, HBool $r) {
        return ($l instanceof HTrue) && ($r instanceof HTrue);
    }

    public static function hor(HBool $l, HBool $r) {
        return ($l instanceof HTrue) || ($r instanceof HTrue);
    }

    public static function not(HBool $b) {
        if ($l instanceof HTrue) return new HFalse();
        return new HTrue();
    }

    public function __toString() {
        return static::show($this);
    }
}

final class HTrue extends HBool {
    public static function show(IShow $show) {
        return "True";
    }
}

final class HFalse extends HBool {
    public static function show(IShow $show) {
        return "False";
    }
}
