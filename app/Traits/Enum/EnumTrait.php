<?php

namespace App\Traits\Enum;

use Illuminate\Support\Str;
use ReflectionClassConstant;
use App\Enums\Attributes\Description;
use App\Enums\Attributes\ServiceClass;

trait EnumTrait
{
    public static function toAssociativeArray(): array
    {
        foreach(self::cases() as $case) {
            $array[] = ['name' => self::getDescription($case), 'value' => $case->value];
            //$array[] = ['name' => $case->name, 'value' => $case->value];
        }
        return $array;
    }

    public function toArray() {
        return ['name' => self::getDescription($this), 'value' => $this->value];
        //return ['name' => $this->name, 'value' => $this->value];
    }

    public function toFullArray() {
        return ['name' => self::getDescription($this), 'value' => $this->value, 'serviceclass' => self::getServiceClass($this)];;
    }

    /**
     * @param self $enum
     */
    private static function getDescription(self $enum): string
    {
        $ref = new ReflectionClassConstant(self::class, $enum->name);
        $classAttributes = $ref->getAttributes(Description::class);

        if (count($classAttributes) === 0) {
            return Str::headline($enum->value);
        }

        return $classAttributes[0]->newInstance()->description;
    }

    /**
     * @param self $enum
     */
    private static function getServiceClass(self $enum): string
    {
        $ref = new ReflectionClassConstant(self::class, $enum->name);
        $classAttributes = $ref->getAttributes(ServiceClass::class);

        if (count($classAttributes) === 0) {
            return Str::headline($enum->value);
        }

        return $classAttributes[0]->newInstance()->serviceclass;
    }

    /**
     * @return array<string,string>
     */
    public static function asSelectArray(): array
    {
        /** @var array<string,string> $values */
        return collect(self::cases())
            ->map(function ($enum) {
                return [
                    'name' => self::getDescription($enum),
                    'value' => $enum->value,
                ];
            })->toArray();
    }
}
