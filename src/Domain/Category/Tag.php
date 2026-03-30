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

        if ($type->equals(new TagType(TagType::RESTRICTED))) {
            $this->assertRestrictedValueIsSupported($parameter);
        }

        if ($type->equals(new TagType(TagType::REGION))) {
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
        if (!$this->type->equals(new TagType(TagType::REGION))) {
            throw TagIsNotRegional::becauseTypeDiffers($this->value);
        }

        return new Region($this->parameter);
    }

    /**
     * @throws TagIsNotRestricted
     */
    public function restrictedVisibility(): RestrictedVisibility
    {
        if (!$this->type->equals(new TagType(TagType::RESTRICTED))) {
            throw TagIsNotRestricted::becauseTypeDiffers($this->value);
        }

        return new RestrictedVisibility($this->parameter);
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
        $supportedTypes = [
            TagType::ROOT,
            TagType::MENU,
            TagType::PROMO,
            TagType::HIDDEN,
            TagType::SEARCHABLE,
            TagType::RESTRICTED,
            TagType::REGION,
        ];

        if (!in_array($rawType, $supportedTypes, true)) {
            throw UnknownTagType::becauseTypeIsUnsupported($rawType);
        }

        return new TagType($rawType);
    }

    /**
     * @throws UnknownRegionTagValue
     */
    private function assertRegionValueIsSupported(string $parameter): void
    {
        $supportedValues = [
            Region::KG,
            Region::RU,
        ];

        if (!in_array($parameter, $supportedValues, true)) {
            throw UnknownRegionTagValue::becauseValueIsUnsupported($parameter);
        }
    }

    /**
     * @throws UnknownRestrictedTagValue
     */
    private function assertRestrictedValueIsSupported(string $parameter): void
    {
        $supportedValues = [
            RestrictedVisibility::STAFF_ONLY,
            RestrictedVisibility::ADULTS_ONLY,
        ];

        if (!in_array($parameter, $supportedValues, true)) {
            throw UnknownRestrictedTagValue::becauseValueIsUnsupported($parameter);
        }
    }
}
