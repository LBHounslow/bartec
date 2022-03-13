<?php

namespace Tests\Unit\Transformer\ReportMissedBin;

use LBHounslow\Bartec\Enum\BartecServiceEnum;
use LBHounslow\Bartec\Transformer\ReportMissedBin\FeatureTypeNameToServiceTypeTransformer;
use Tests\Unit\BartecTestCase;

class FeatureTypeNameToServiceTypeTransformerTest extends BartecTestCase
{
    public function testItConstructs()
    {
        $result = new FeatureTypeNameToServiceTypeTransformer();
        $this->assertInstanceOf(FeatureTypeNameToServiceTypeTransformer::class, $result);
    }

    /**
     * @param string $value
     * @param string $expectedResult
     * @dataProvider transformValueDataProvider
     */
    public function testItTransformsValuesAsExpected(string $value, string $expectedResult)
    {
        $featureTypeNameToExtendedDataFieldTransformer = new FeatureTypeNameToServiceTypeTransformer();
        $result = $featureTypeNameToExtendedDataFieldTransformer->transform($value);
        $this->assertEquals($expectedResult, $result);
    }

    public function transformValueDataProvider()
    {
        return [
            [BartecServiceEnum::FEATURE_CARDBOARD_1100, BartecServiceEnum::SERVICE_TYPE_CARDBOARD],
            [BartecServiceEnum::FEATURE_CARDBOARD_1280, BartecServiceEnum::SERVICE_TYPE_CARDBOARD],
            [BartecServiceEnum::FEATURE_CARDBOARD_140, BartecServiceEnum::SERVICE_TYPE_CARDBOARD],
            [BartecServiceEnum::FEATURE_CARDBOARD_240, BartecServiceEnum::SERVICE_TYPE_CARDBOARD],
            [BartecServiceEnum::FEATURE_CARDBOARD_360, BartecServiceEnum::SERVICE_TYPE_CARDBOARD],
            [BartecServiceEnum::FEATURE_CARDBOARD_660, BartecServiceEnum::SERVICE_TYPE_CARDBOARD],
            [BartecServiceEnum::FEATURE_FOOD_23, BartecServiceEnum::SERVICE_TYPE_FOOD],
            [BartecServiceEnum::FEATURE_FOOD_140, BartecServiceEnum::SERVICE_TYPE_FOOD],
            [BartecServiceEnum::FEATURE_FOOD_240, BartecServiceEnum::SERVICE_TYPE_FOOD],
            [BartecServiceEnum::FEATURE_GARDEN_240, BartecServiceEnum::SERVICE_TYPE_GARDEN],
            [BartecServiceEnum::FEATURE_GARDENSACK, BartecServiceEnum::SERVICE_TYPE_GARDEN],
            [BartecServiceEnum::FEATURE_PLASTIC_140, BartecServiceEnum::SERVICE_TYPE_PLASTIC],
            [BartecServiceEnum::FEATURE_PLASTIC_240, BartecServiceEnum::SERVICE_TYPE_PLASTIC],
            [BartecServiceEnum::FEATURE_PLASTIC_660, BartecServiceEnum::SERVICE_TYPE_PLASTIC],
            [BartecServiceEnum::FEATURE_PLASTIC_1100, BartecServiceEnum::SERVICE_TYPE_PLASTIC],
            [BartecServiceEnum::FEATURE_PLASTIC_360, BartecServiceEnum::SERVICE_TYPE_PLASTIC],
            [BartecServiceEnum::FEATURE_PLASTIC_1280, BartecServiceEnum::SERVICE_TYPE_PLASTIC],
            [BartecServiceEnum::FEATURE_PLASTIC_CANS_1280, BartecServiceEnum::SERVICE_TYPE_PLASTIC],
            [BartecServiceEnum::FEATURE_CANS_140, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_CANS_240, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_CANS_360, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_CANS_660, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_CANS_1100, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_GLASS_140, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_GLASS_360, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_GLASS_660, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_GLASS_240, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_GLASS_1280, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_PAPER_140, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_PAPER_240, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_PAPER_360, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_PAPER_660, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_PAPER_CARD_1280, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_RECYCLING_1280, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_RECYCLING_240, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_RECYCLING_BLUE, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_RECYCLING_GREEN, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_RECYCLING_RED, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_RECYCLING_CLEAR_SACK, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_ELECTRONIC_WASTE, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_TEXTILES, BartecServiceEnum::SERVICE_TYPE_RECYCLING],
            [BartecServiceEnum::FEATURE_RESIDUAL_140, BartecServiceEnum::SERVICE_TYPE_RESIDUAL],
            [BartecServiceEnum::FEATURE_RESIDUAL_240, BartecServiceEnum::SERVICE_TYPE_RESIDUAL],
            [BartecServiceEnum::FEATURE_RESIDUAL_360, BartecServiceEnum::SERVICE_TYPE_RESIDUAL],
            [BartecServiceEnum::FEATURE_RESIDUAL_660, BartecServiceEnum::SERVICE_TYPE_RESIDUAL],
            [BartecServiceEnum::FEATURE_RESIDUAL_1100, BartecServiceEnum::SERVICE_TYPE_RESIDUAL],
            [BartecServiceEnum::FEATURE_RESIDUAL_SACK, BartecServiceEnum::SERVICE_TYPE_RESIDUAL],
            [BartecServiceEnum::FEATURE_RESIDUAL_PURPLE_SACK, BartecServiceEnum::SERVICE_TYPE_RESIDUAL],
            [BartecServiceEnum::FEATURE_RESIDUAL_1100_FAS, BartecServiceEnum::SERVICE_TYPE_RESIDUAL],
            [BartecServiceEnum::FEATURE_RESIDUAL_140_FAS, BartecServiceEnum::SERVICE_TYPE_RESIDUAL],
            [BartecServiceEnum::FEATURE_RESIDUAL_240_FAS, BartecServiceEnum::SERVICE_TYPE_RESIDUAL],
            [BartecServiceEnum::FEATURE_RESIDUAL_360_FAS, BartecServiceEnum::SERVICE_TYPE_RESIDUAL],
            [BartecServiceEnum::FEATURE_RESIDUAL_660_FAS, BartecServiceEnum::SERVICE_TYPE_RESIDUAL],
            [BartecServiceEnum::FEATURE_PURPLE_RESIDUAL_1100, BartecServiceEnum::SERVICE_TYPE_RESIDUAL],
        ];
    }
}