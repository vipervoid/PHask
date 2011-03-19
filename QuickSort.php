<?php
// After QuickSort: http://en.literateprograms.org/Quicksort_(Haskell)
require_once "Prelude.php";

class QuickSort {
    private static function part($x, HList $xs, HList $l, HList $e, HList $g, HList $acc) {
        if (HList::null_($xs)) {
            return self::qsortPr($l, (HList::append($e, self::qsortPr($g, $acc))));
        } else {
            $z  = HList::head($xs);
            $zs = HList::tail($xs);

            if ($z > $x) {
                return self::part($x, $zs, $l, $e, new Cons($z, $g), $acc);
            } else if ($z < $x) {
                return self::part($x, $zs, new Cons($z, $l), $e, $g, $acc);
            } else {
                return self::part($x, $zs, $l, new Cons($z, $e), $g, $acc);
            }
        }
    }

    public static function qsort(HList $xs) {
        return self::qsortPr($xs, new Nil());
    }

    private static function qsortPr(HList $xs, HList $acc) {
        if (HList::null_($xs)) {
            return $acc;
        } else if (HList::null_(HList::tail($xs))) { // Check for singleton list...
            return new Cons(HList::head($xs), $acc);
        } else {
            $x = HList::head($xs);
            return self::part( $x
                             , HList::tail($xs)
                             , new Nil()
                             , new Cons($x, new Nil())
                             , new Nil()
                             , $acc
                             );
        }
    }
}

function main() {
    $arr = array();
    for ($i = 0; $i < 50; $i++) { $arr[] = rand(1,50); }

    $input = HList::fromArray($arr);
    echo "Input: " . $input . PHP_EOL;
    echo "Output: " . QuickSort::qsort($input) . PHP_EOL;
}

main();
