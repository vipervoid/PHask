<?php
require_once "Prelude.php";

function main() {
    // Expected result: Just(14)
    $m1 = Maybe::bind(new Just(4), function($a) { return new Just($a + 10); });
    echo 'Just 4 >>= \a -> Just $ a+10: ' . $m1 . PHP_EOL;

    // Expected result: Just(5)
    $m2 = Maybe::bind_(new Just(4), new Just(5));
    echo 'Just 4 >> Just 5: ' . $m2 . PHP_EOL;

    $f1 = Maybe::fmap(function($x) { return 5*$x; }, new Just(5));
    echo 'fmap (*5) (Just 5): ' . $f1 . PHP_EOL;

    $c123 = HList::fromArray(range(1,3));
    $c456 = HList::fromArray(range(4,6));
    
    $f2 = HList::fmap(function($x) { return 2*$x; }, $c123);
    echo 'fmap (*2) [1,2,3]: ' . $f2 . PHP_EOL;

    $l1 = HList::append(new Cons(1, new Nil()), new Cons(2, new Nil()));
    echo '[1]++[2]: ' . $l1 . PHP_EOL;
    
    $fr = HList::foldr(function ($x, $xs) { return $x - $xs; }, 0, $c123);
    echo "foldr (-) 0 [1,2,3]: " . $fr . PHP_EOL;

    $fl = HList::foldl(function ($x, $xs) { return $x - $xs; }, 0, $c123);
    echo "foldl (-) 0 [1,2,3]: " . $fl . PHP_EOL;

//    $b = HList::bind($c123, function($xs) use ($c45) { return HList::append($xs, $c45); } );
//    echo HList::show($b);
    echo 'fromArray: ' . HList::fromArray(array(1,2,3)) . PHP_EOL;
}

main();
