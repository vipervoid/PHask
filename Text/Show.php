<?php
interface IShow {
    public function __toString();
    public static function show(IShow $show);
}
