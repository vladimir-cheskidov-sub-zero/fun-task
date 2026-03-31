<?php

declare(strict_types=1);

namespace FunTask\Domain\Category;

use FunTask\Domain\Category\Exception\TagFormatIsInvalid;
use FunTask\Domain\Category\Exception\TagHasNoParameter;
use FunTask\Domain\Category\Exception\TagIsNotRegional;
use FunTask\Domain\Category\Exception\TagIsNotRestricted;
use FunTask\Domain\Category\Exception\TagParameterIsMissing;
use FunTask\Domain\Category\Exception\TagValueCannotBeEmpty;
use FunTask\Domain\Category\Exception\UnexpectedTagParameter;
use FunTask\Domain\Category\Exception\UnknownRegionTagValue;
use FunTask\Domain\Category\Exception\UnknownRestrictedTagValue;
use FunTask\Domain\Category\Exception\UnknownTagType;

final class Tag
{
    private string $value;
    private TagType $type;
    private string $parameter;
    private bool $hasParameter;
    /**
     * @throws TagFormatIsInvalid
     * @throws TagParameterIsMissing
     * @throws TagValueCannotBeEmpty
     * @throws UnexpectedTagParameter
     * @throws UnknownRegionTagValue
     * @throws UnknownRestrictedTagValue
     * @throws UnknownTagType
     */
    public function __construct(string $value)
    {
        $normalizedValue = trim($value);
        if ($normalizedValue === '') {
            throw TagValueCannotBeEmpty::becauseValueIsEmpty();
        }
        $parts = explode(':', $normalizedValue);
        if (count($parts) > 2) {
            throw TagFormatIsInvalid::becauseValueHasUnexpectedStructure($normalizedValue);
        }
        $type = $this->createType($parts[0]);
        $hasParameter = count($parts) === 2;
        $parameter = $hasParameter ? trim($parts[1]) : '';
        if ($type->requiresParameter() && $parameter === '') {
            throw TagParameterIsMissing::becauseTypeRequiresParameter($type->getValue());
        }
        if (!$type->requiresParameter() && $hasParameter) {
            throw UnexpectedTagParameter::becauseTypeDoesNotAllowParameter($type->getValue());
        }
        if ($type->equals(TagType::RESTRICTED())) {
            $this->assertRestrictedValueIsSupported($parameter);
        }
        if ($type->equals(TagType::REGION())) {
            $this->assertRegionValueIsSupported($parameter);
        }
        $this->value = $normalizedValue;
        $this->type = $type;
        $this->parameter = $parameter;
        $this->hasParameter = $hasParameter;
    }
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
    public function hasParameter(): bool
    {
        return $this->hasParameter;
    }
    public function isOfType(TagType $type): bool
    {
        return $this->type->equals($type);
    }
    /**
     * @throws TagHasNoParameter
     */
    public function parameter(): string
    {
        if (!$this->hasParameter) {
            throw TagHasNoParameter::becauseTagIsSimple($this->value);
        }
        return $this->parameter;
    }
    /**
     * @throws TagIsNotRegional
     */
    public function region(): Region
    {
        if (!$this->type->equals(TagType::REGION())) {
            throw TagIsNotRegional::becauseTypeDiffers($this->value);
        }
        return Region::from($this->parameter);
    }
    /**
     * @throws TagIsNotRestricted
     */
    public function restrictedVisibility(): RestrictedVisibility
    {
        if (!$this->type->equals(TagType::RESTRICTED())) {
            throw TagIsNotRestricted::becauseTypeDiffers($this->value);
        }
        return RestrictedVisibility::from($this->parameter);
    }
    public function type(): TagType
    {
        return $this->type;
    }
    public function value(): string
    {
        return $this->value;
    }
    /**
     * @throws UnknownTagType
     */
    private function createType(string $rawType): TagType
    {
        try {
            return TagType::from($rawType);
        } catch (\UnexpectedValueException $exception) {
            throw UnknownTagType::becauseTypeIsUnsupported($rawType, $exception);
        }
    }
    /**
     * @throws UnknownRegionTagValue
     */
    private function assertRegionValueIsSupported(string $parameter): void
    {
        if (!Region::isValid($parameter)) {
            throw UnknownRegionTagValue::becauseValueIsUnsupported($parameter);
        }

        if ($parameter === Region::UNSPECIFIED()->getValue()) {
            throw UnknownRegionTagValue::becauseValueIsUnsupported($parameter);
        }
    }
    /**
     * @throws UnknownRestrictedTagValue
     */
    private function assertRestrictedValueIsSupported(string $parameter): void
    {
        if (!RestrictedVisibility::isValid($parameter)) {
            throw UnknownRestrictedTagValue::becauseValueIsUnsupported($parameter);
        }
    }
}
