<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 15:54
 */

namespace Core\Abstracts\Aggregate\Structs\ValueObjects;


use Carbon\Carbon;
use Core\Abstracts\Aggregate\Contracts\ValueObjects\ValueObjectInterface;

abstract class DateTimeObject implements ValueObjectInterface
{
    public $value;

    /**
     * DateTimeObject constructor.
     * @param $value
     * @throws \Exception
     */
    public function __construct($value)
    {
        if (is_string($value)) {
            $this->value = Carbon::parse($value);

            return;
        }

        if ($value instanceof Carbon) {
            $this->value = $value;

            return;
        }

        throw new \Exception('can not transform to DateTimeObject:' . $value);
    }

    abstract function show();
}