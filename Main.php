<?php
require_once "Prelude.php";

function main() {
    // Expected result: Just(14)
    $m1 = Maybe::bind(new Just(4), function($a) { return new Just($a + 10); });
    echo $m1;

    // Expected result: Just(5)
    $m2 = Maybe::bind_(new Just(4), new Just(5));
    echo $m2;

    $f1 = Maybe::fmap(function($x) { return 5*$x; }, new Just(5));
    echo $f1;

    $c123 = HList::fromArray(range(1,3));
    $c456 = HList::fromArray(range(4,6));
    print_r(HList::toArray($c123));
    $f2 = HList::fmap(function($x) { return 2*$x; }, $c123);
    echo $f2;

    $l1 = HList::append(new Cons(1, new Nil()), new Cons(2, new Nil()));
    echo $l1;
    
    $fold = HList::foldr(function ($xs, $x) { return $x + $xs; }, 0, $c123);
    var_dump($fold);

//    $b = HList::bind($c123, function($xs) use ($c45) { return HList::append($xs, $c45); } );
//    echo HList::show($b);
    echo HList::fromArray(array(1,2,3));
}

main();