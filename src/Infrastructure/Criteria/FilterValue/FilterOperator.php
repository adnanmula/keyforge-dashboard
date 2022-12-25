<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Criteria\FilterValue;

enum FilterOperator
{
    case EQUAL;
    case NOT_EQUAL;
    case GREATER;
    case GREATER_OR_EQUAL;
    case LESS;
    case LESS_OR_EQUAL;
    case CONTAINS;
    case NOT_CONTAINS;
    case IN;
    case NOT_IN;
    case IS_NULL;
    case IS_NOT_NULL;
    case IN_ARRAY;
}
