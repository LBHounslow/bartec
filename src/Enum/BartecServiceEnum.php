<?php

namespace LBHounslow\Bartec\Enum;

class BartecServiceEnum
{
    const BINS = 'Bins';
    const CARDBOARD = 'Cardboard';

    const CREW_RESIDUAL = 'RES';
    const CREW_BULKY_PLASTIC = 'BULK_PLAS';
    const CREW_BULKY_CB = 'BULK_CB';
    const CREW_RECYCLING = 'RCY';
    const CREW_GARDEN = 'GDN';

    const CREW_WORK_GROUP_NAME = 'Waste';
    const CREW_NUMBER_900 = 900;

    const DEFAULT_BIN_NOT_FOUND = 'Not Found';
    const DEFAULT_NOTE_COMMENT = 'automatically set by XFP webform integration';
    const DEFAULT_SERVICE_REQUEST_SOURCE = 'COLLECTIVE API';

    const EVENT_NO_ACCESS = 'NO ACCESS';
    const EVENT_NOT_OUT = 'NOT OUT';
    const EVENT_NOT_AT_CURTILAGE = 'NOT AT CURTILAGE';
    const EVENT_CONTAMINATED_RECYCLING = 'CONTAMINATED - REC';
    const EVENT_CONTAMINATED_GARDEN_WASTE = 'CONTAM - GARDEN';
    const EVENT_OVERWEIGHT = 'OVERWEIGHT';
    const EVENT_EXCESS = 'EXCESS';
    const EVENT_LID_NOT_CLOSED = 'LID NOT CLOSED';
    const EVENT_CONTAMINATED_KITCHEN = 'CONTAM - KITCHEN';
    const EVENT_NON_COUNCIL_BIN = 'NON COUNCIL BIN';
    const EVENT_CONTAMINATED_WASTE = 'CONTAMINATED - RES';
    const EVENT_CONTAMINATED_OTHER = 'CONTAMINATED-OTHER';
    const EVENT_RECYCLING_BOX_NOT_SORTED = '_REC BOX-NOT SORTED';
    const EVENT_RECYCLING_OLD_BOX_NOT_SORTED = 'REC - OLD BOX UNSORT';
    const EVENT_CONTAMINATED_FOOD_BIN = 'CONTAM FOOD BIN';

    const FEATURE_CATEGORY_WASTE_CONTAINER = 'WASTE CONTAINER';
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

    const GARDEN = 'Garden';
    const HOUSEHOLD_SUBSCRIBED_FOR = 'The household subscribed for %s';
    const NO = 'No';
    const PLASTIC = 'Plastic';
    const RECYCLING = 'Recycling';
    const REPORTER_TYPE_PUBLIC = 'Public';
    const RESIDUAL = 'Residual';

    const SACKS = 'Sacks';

    const SERVICE_NOTE_DESCRIPTION_GENERAL_NOTE = 'General Note';
    const SERVICE_REQUEST_CODE_PREFIX = 'SR';

    const SERVICE_REQUEST_CLASS_NAME_GARDEN_WASTE = 'Garden Waste';
    const SERVICE_REQUEST_CLASS_NAME_WASTE = 'Waste';
    const SERVICE_REQUEST_CLASS_NAME_GROUNDS = 'Grounds';
    const SERVICE_REQUEST_CLASS_NAME_QUALITY_ISSUE = 'Quality Issue';

    const SERVICE_REQUEST_TYPE_NAME_MISSED_BIN = 'Missed Bin';
    const SERVICE_REQUEST_TYPE_NAME_SUBSCRIPTION = 'Subscription';
    const SERVICE_REQUEST_TYPE_NAME_BIN_ORDER = 'Bin Order';
    const SERVICE_REQUEST_TYPE_NAME_CONTAINER_ORDER = 'Container Order';

    const STATUS_OPEN = 'OPEN';
    const STATUS_PENDING = 'PENDING';
    const STATUS_ASSIGNED = 'ASSIGNED';
    const STATUS_IN_PROGRESS = 'IN PROGRESS';
    const STATUS_UNABLE_TO_COMPLETE = 'UNABLE TO COMPLETE';
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_REJECT = 'REJECT';
    const STATUS_CANCELLED = 'CANCELLED';

    const SLA_NAME_1WD = 'SLA_1WD';
    const LAND_NAME_HH = 'HH';
    const YES = 'Yes';
}