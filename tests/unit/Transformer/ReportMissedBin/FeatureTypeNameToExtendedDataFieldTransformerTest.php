<?php

namespace Tests\Unit\Transformer\ReportMissedBin;

use LBHounslow\Bartec\Enum\BartecServiceEnum;
use LBHounslow\Bartec\Exception\TransformationException;
use LBHounslow\Bartec\Transformer\ReportMissedBin\FeatureTypeNameToExtendedDataFieldTransformer;
use Tests\Unit\BartecTestCase;

class FeatureTypeNameToExtendedDataFieldTransformerTest extends BartecTestCase
{
    public function testItConstructs()
    {
        $result = new FeatureTypeNameToExtendedDataFieldTransformer();
        $this->assertInstanceOf(FeatureTypeNameToExtendedDataFieldTransformer::class, $result);
    }

    public function testItThrowsTransformationExceptionForInvalidValue()
    {
        $this->expectException(TransformationException::class);
        $featureTypeNameToExtendedDataFieldTransformer = new FeatureTypeNameToExtendedDataFieldTransformer();
        $featureTypeNameToExtendedDataFieldTransformer->transform('INVALID');
    }

    /**
     * @param string $value
     * @param string $expectedResult
     * @dataProvider transformValueDataProvider
     */
    public function testItTransformsValuesAsExpected(string $value, string $expectedResult)
    {
        $featureTypeNameToExtendedDataFieldTransformer = new FeatureTypeNameToExtendedDataFieldTransformer();
        $result = $featureTypeNameToExtendedDataFieldTransformer->transform($value);
        $this->assertEquals($expectedResult, $result);
    }

    public function transformValueDataProvider()
    {
        return [
            [BartecServiceEnum::FEATURE_CANS_140, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_CANS_240, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_CANS_360, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_CANS_660, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_CANS_1100, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_CARDBOARD_1100, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_CARDBOARD_1280, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_CARDBOARD_140, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_CARDBOARD_240, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_CARDBOARD_360, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_CARDBOARD_660, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_GLASS_140, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_GLASS_360, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_GLASS_660, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_GLASS_240, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_PAPER_140, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_PAPER_240, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_PAPER_360, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_PAPER_660, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_PAPER_CARD_1280, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_PLASTIC_140, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_PLASTIC_240, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_PLASTIC_660, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_PLASTIC_1100, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_PLASTIC_360, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_PLASTIC_1280, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_PLASTIC_CANS_1280, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_RECYCLING_1280, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_RECYCLING_240, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_RECYCLING],
            [BartecServiceEnum::FEATURE_FOOD_23, BartecServiceEnum::EXTENDED_DATA_FIELD_FOOD_WASTE],
            [BartecServiceEnum::FEATURE_FOOD_140, BartecServiceEnum::EXTENDED_DATA_FIELD_FOOD_WASTE],
            [BartecServiceEnum::FEATURE_FOOD_240, BartecServiceEnum::EXTENDED_DATA_FIELD_FOOD_WASTE],
            [BartecServiceEnum::FEATURE_GARDEN_240, BartecServiceEnum::EXTENDED_DATA_FIELD_GARDEN_WASTE_COLLECTION],
            [BartecServiceEnum::FEATURE_GARDENSACK, BartecServiceEnum::EXTENDED_DATA_FIELD_GARDEN_WASTE_COLLECTION],
            [BartecServiceEnum::FEATURE_RESIDUAL_140, BartecServiceEnum::EXTENDED_DATA_WHEELED_BIN_REFUSE_COLLECTION],
            [BartecServiceEnum::FEATURE_RESIDUAL_240, BartecServiceEnum::EXTENDED_DATA_WHEELED_BIN_REFUSE_COLLECTION],
            [BartecServiceEnum::FEATURE_RESIDUAL_360, BartecServiceEnum::EXTENDED_DATA_WHEELED_BIN_REFUSE_COLLECTION],
            [BartecServiceEnum::FEATURE_RESIDUAL_660, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_REFUSE],
            [BartecServiceEnum::FEATURE_RESIDUAL_1100, BartecServiceEnum::EXTENDED_DATA_FIELD_BULK_BIN_REFUSE],
            [BartecServiceEnum::FEATURE_RECYCLING_BLUE, BartecServiceEnum::EXTENDED_DATA_FIELD_RECYCLING_BOX_COLLECTION],
            [BartecServiceEnum::FEATURE_RECYCLING_GREEN, BartecServiceEnum::EXTENDED_DATA_FIELD_RECYCLING_BOX_COLLECTION],
            [BartecServiceEnum::FEATURE_RECYCLING_RED, BartecServiceEnum::EXTENDED_DATA_FIELD_RECYCLING_BOX_COLLECTION],
            [BartecServiceEnum::FEATURE_ELECTRONIC_WASTE, BartecServiceEnum::EXTENDED_DATA_FIELD_RECYCLING_BOX_COLLECTION],
            [BartecServiceEnum::FEATURE_TEXTILES, BartecServiceEnum::EXTENDED_DATA_FIELD_RECYCLING_BOX_COLLECTION],
            [BartecServiceEnum::FEATURE_RESIDUAL_SACK, BartecServiceEnum::EXTENDED_DATA_FIELD_HOUSEHOLD_REFUSE_SACK_COLLECTION],
            [BartecServiceEnum::FEATURE_RESIDUAL_PURPLE_SACK, BartecServiceEnum::EXTENDED_DATA_FIELD_FLATS_ABOVE_SHOPS_REFUSE_SACK_COLLECTION],
            [BartecServiceEnum::FEATURE_RESIDUAL_1100_FAS, BartecServiceEnum::EXTENDED_DATA_FIELD_FLATS_ABOVE_SHOPS_REFUSE_SACK_COLLECTION],
            [BartecServiceEnum::FEATURE_RESIDUAL_140_FAS, BartecServiceEnum::EXTENDED_DATA_FIELD_FLATS_ABOVE_SHOPS_REFUSE_SACK_COLLECTION],
            [BartecServiceEnum::FEATURE_RESIDUAL_240_FAS, BartecServiceEnum::EXTENDED_DATA_FIELD_FLATS_ABOVE_SHOPS_REFUSE_SACK_COLLECTION],
            [BartecServiceEnum::FEATURE_RESIDUAL_360_FAS, BartecServiceEnum::EXTENDED_DATA_FIELD_FLATS_ABOVE_SHOPS_REFUSE_SACK_COLLECTION],
            [BartecServiceEnum::FEATURE_RESIDUAL_660_FAS, BartecServiceEnum::EXTENDED_DATA_FIELD_FLATS_ABOVE_SHOPS_REFUSE_SACK_COLLECTION],
            [BartecServiceEnum::FEATURE_PURPLE_RESIDUAL_1100, BartecServiceEnum::EXTENDED_DATA_FIELD_FLATS_ABOVE_SHOPS_REFUSE_SACK_COLLECTION],
            [BartecServiceEnum::FEATURE_RECYCLING_CLEAR_SACK, BartecServiceEnum::EXTENDED_DATA_FIELD_FLATS_ABOVE_SHOPS_RECYCLING_SACK_COLLECTION]
        ];
    }
}