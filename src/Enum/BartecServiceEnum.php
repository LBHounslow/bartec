<?php

namespace LBHounslow\Bartec\Enum;

class BartecServiceEnum
{
    const SERVICE_REQUEST_CODE_PREFIX = 'SR';

    const SERVICE_REQUEST_CLASS_NAME_GARDEN_WASTE = 'Garden Waste';
    const SERVICE_REQUEST_CLASS_NAME_WASTE = 'Waste';
    const SERVICE_REQUEST_CLASS_NAME_GROUNDS = 'Grounds';
    const SERVICE_REQUEST_CLASS_NAME_QUALITY_ISSUE = 'Quality Issue';

    const SERVICE_REQUEST_TYPE_NAME_MISSED_BIN = 'Missed Bin';
    const SERVICE_REQUEST_TYPE_NAME_SUBSCRIPTION = 'Subscription';

    const SERVICE_NOTE_DESCRIPTION_GENERAL_NOTE = 'General Note';

    const STATUS_OPEN = 'OPEN';
    const STATUS_PENDING = 'PENDING';
    const STATUS_CLOSED = 'CLOSED';

    const DEFAULT_BIN_NOT_FOUND = 'Not Found';

    const RES = 'RES';
    const BULK_PLAS = 'BULK_PLAS';
    const BULK_CB = 'BULK_CB';
    const RCY = 'RCY';
    const GDN = 'GDN';

    const RESIDUAL = 'Residual';
    const PLASTIC = 'Plastic';
    const CARDBOARD = 'Cardboard';
    const RECYCLING = 'Recycling';
    const GARDEN = 'Garden';

    const FEATURE_STATE_IN_SERVICE = 'IN SERVICE';
    const FEATURE_STATE_OUT_OF_SERVICE = 'OUT OF SERVICE';
    const FEATURE_STATE_DAMAGED = 'DAMAGED';
    const FEATURE_STATE_ON_ORDER = 'ON ORDER';
    const FEATURE_STATE_TO_BE_REMOVED = 'TO BE REMOVED';

    const VALID_FEATURE_STATES = [
        self::FEATURE_STATE_IN_SERVICE,
        self::FEATURE_STATE_OUT_OF_SERVICE,
        self::FEATURE_STATE_DAMAGED,
        self::FEATURE_STATE_ON_ORDER,
        self::FEATURE_STATE_TO_BE_REMOVED
    ];

    const CREW_WORK_GROUP_NAME = 'Waste';
    const CREW_NUMBER_900 = 900;

    const SLA_NAME_1WD = 'SLA_1WD';
    const LAND_NAME_HH = 'HH';

    const FEATURE_RESIDUAL_SACK = 'ResidualSack';
    const FEATURE_RESIDUAL_140 = 'Residual140';
    const FEATURE_RESIDUAL_240 = 'Residual240';
    const FEATURE_RECYCLING_RED = 'RecyclingRed';
    const FEATURE_RECYCLING_GREEN = 'RecyclingGreen';
    const FEATURE_RECYCLING_BLUE = 'RecyclingBlue';
    const FEATURE_CARDBOARD_1280 = 'Cardboard1280';
    const FEATURE_PLASTIC_1280 = 'Plastic1280';
    const FEATURE_GARDEN_240 = 'Garden240';
    const FEATURE_GARDENSACK = 'GardenSack';
    const FEATURE_RESIDUAL_1100 = 'Residual1100';
    const FEATURE_RESIDUAL_360 = 'Residual360';
    const FEATURE_RESIDUAL_660 = 'Residual660';
    const FEATURE_RESIDUAL_940 = 'Residual940';
    const FEATURE_RECYCLING_SACK = 'RecyclingSack';
    const FEATURE_RESIDUAL_PURPLE_SACK = 'ResidualPurpleSack';
    const FEATURE_RECYCLING_1280 = 'Recycling1280';
    const FEATURE_CARDBOARD_240 = 'Cardboard240';
    const FEATURE_PLASTIC_240 = 'Plastic240';
    const FEATURE_FOOD_23 = 'Food23';
    const FEATURE_FOOD_140 = 'Food140';
    const FEATURE_FOOD_240 = 'Food240';

    const HOUSEHOLD_SUBSCRIBED_FOR = 'The household subscribed for %s';

    const DEFAULT_SERVICE_REQUEST_SOURCE = 'COLLECTIVE API';
    const DEFAULT_NOTE_COMMENT = 'automatically set by XFP webform integration';

    const FEATURE_CATEGORY_WASTE_CONTAINER = 'WASTE CONTAINER';

    const REPORTER_TYPE_PUBLIC = 'Public';
}