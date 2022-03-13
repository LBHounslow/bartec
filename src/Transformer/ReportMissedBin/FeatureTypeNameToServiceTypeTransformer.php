<?php

namespace LBHounslow\Bartec\Transformer\ReportMissedBin;

use LBHounslow\Bartec\Enum\BartecServiceEnum;
use LBHounslow\Bartec\Transformer\TransformerInterface;

class FeatureTypeNameToServiceTypeTransformer implements TransformerInterface
{
    /**
     * @param $value
     * @return string
     */
    public function transform($value)
    {
        switch ($value)
        {
            case BartecServiceEnum::FEATURE_CARDBOARD_1100:
            case BartecServiceEnum::FEATURE_CARDBOARD_1280:
            case BartecServiceEnum::FEATURE_CARDBOARD_140:
            case BartecServiceEnum::FEATURE_CARDBOARD_240:
            case BartecServiceEnum::FEATURE_CARDBOARD_360:
            case BartecServiceEnum::FEATURE_CARDBOARD_660:
                return BartecServiceEnum::SERVICE_TYPE_CARDBOARD;

            case BartecServiceEnum::FEATURE_FOOD_23:
            case BartecServiceEnum::FEATURE_FOOD_140:
            case BartecServiceEnum::FEATURE_FOOD_240:
                return BartecServiceEnum::SERVICE_TYPE_FOOD;

            case BartecServiceEnum::FEATURE_GARDEN_240:
            case BartecServiceEnum::FEATURE_GARDENSACK:
                return BartecServiceEnum::SERVICE_TYPE_GARDEN;

            case BartecServiceEnum::FEATURE_PLASTIC_140:
            case BartecServiceEnum::FEATURE_PLASTIC_240:
            case BartecServiceEnum::FEATURE_PLASTIC_660:
            case BartecServiceEnum::FEATURE_PLASTIC_1100:
            case BartecServiceEnum::FEATURE_PLASTIC_360:
            case BartecServiceEnum::FEATURE_PLASTIC_1280:
            case BartecServiceEnum::FEATURE_PLASTIC_CANS_1280:
                return BartecServiceEnum::SERVICE_TYPE_PLASTIC;

            case BartecServiceEnum::FEATURE_CANS_140:
            case BartecServiceEnum::FEATURE_CANS_240:
            case BartecServiceEnum::FEATURE_CANS_360:
            case BartecServiceEnum::FEATURE_CANS_660:
            case BartecServiceEnum::FEATURE_CANS_1100:
            case BartecServiceEnum::FEATURE_GLASS_140:
            case BartecServiceEnum::FEATURE_GLASS_360:
            case BartecServiceEnum::FEATURE_GLASS_660:
            case BartecServiceEnum::FEATURE_GLASS_240:
            case BartecServiceEnum::FEATURE_GLASS_1280:
            case BartecServiceEnum::FEATURE_PAPER_140:
            case BartecServiceEnum::FEATURE_PAPER_240:
            case BartecServiceEnum::FEATURE_PAPER_360:
            case BartecServiceEnum::FEATURE_PAPER_660:
            case BartecServiceEnum::FEATURE_PAPER_CARD_1280:
            case BartecServiceEnum::FEATURE_RECYCLING_1280:
            case BartecServiceEnum::FEATURE_RECYCLING_240:
            case BartecServiceEnum::FEATURE_RECYCLING_BLUE:
            case BartecServiceEnum::FEATURE_RECYCLING_GREEN:
            case BartecServiceEnum::FEATURE_RECYCLING_RED:
            case BartecServiceEnum::FEATURE_RECYCLING_CLEAR_SACK:
            // Electronic Waste and Textiles should be separated once these are available as feature type names
            case BartecServiceEnum::FEATURE_ELECTRONIC_WASTE:
            case BartecServiceEnum::FEATURE_TEXTILES:
                return BartecServiceEnum::SERVICE_TYPE_RECYCLING;

            case BartecServiceEnum::FEATURE_RESIDUAL_140:
            case BartecServiceEnum::FEATURE_RESIDUAL_240:
            case BartecServiceEnum::FEATURE_RESIDUAL_360:
            case BartecServiceEnum::FEATURE_RESIDUAL_660:
            case BartecServiceEnum::FEATURE_RESIDUAL_1100:
            case BartecServiceEnum::FEATURE_RESIDUAL_SACK:
            case BartecServiceEnum::FEATURE_RESIDUAL_PURPLE_SACK:
            case BartecServiceEnum::FEATURE_RESIDUAL_1100_FAS:
            case BartecServiceEnum::FEATURE_RESIDUAL_140_FAS:
            case BartecServiceEnum::FEATURE_RESIDUAL_240_FAS:
            case BartecServiceEnum::FEATURE_RESIDUAL_360_FAS:
            case BartecServiceEnum::FEATURE_RESIDUAL_660_FAS:
            case BartecServiceEnum::FEATURE_PURPLE_RESIDUAL_1100:
                return BartecServiceEnum::SERVICE_TYPE_RESIDUAL;
            default:
                return $value;
        }
    }
}