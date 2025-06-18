<?php

namespace Tests\AssetProcessor;

use Packback\Lti1p3\AssetProcessor\Report;
use Tests\TestCase;

class ReportTest extends TestCase
{
    private Report $report;
    public const INITIAL_ASSET_ID = 'test-asset-123';
    public const INITIAL_TYPE = 'image';
    public const INITIAL_PROCESSING_PROGRESS = 'Processed';
    public const INITIAL_PRIORITY = 2;
    public const INITIAL_TIMESTAMP = '2024-01-15T10:30:00Z';

    protected function setUp(): void
    {
        $this->report = new Report(
            self::INITIAL_ASSET_ID,
            self::INITIAL_TYPE,
            self::INITIAL_PROCESSING_PROGRESS,
            self::INITIAL_PRIORITY,
            self::INITIAL_TIMESTAMP
        );
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(Report::class, $this->report);
    }

    public function test_it_creates_a_new_instance()
    {
        $report = Report::new(
            'new-asset-456',
            'document',
            'Processing',
            1,
            '2024-01-16T11:00:00Z'
        );
        $this->assertInstanceOf(Report::class, $report);
    }

    public function test_it_gets_asset_id()
    {
        $result = $this->report->getAssetId();
        $this->assertEquals(self::INITIAL_ASSET_ID, $result);
    }

    public function test_it_gets_type()
    {
        $result = $this->report->getType();
        $this->assertEquals(self::INITIAL_TYPE, $result);
    }

    public function test_it_gets_processing_progress()
    {
        $result = $this->report->getProcessingProgress();
        $this->assertEquals(self::INITIAL_PROCESSING_PROGRESS, $result);
    }

    public function test_it_gets_priority()
    {
        $result = $this->report->getPriority();
        $this->assertEquals(self::INITIAL_PRIORITY, $result);
    }

    public function test_it_gets_timestamp()
    {
        $result = $this->report->getTimestamp();
        $this->assertEquals(self::INITIAL_TIMESTAMP, $result);
    }

    public function test_it_gets_title()
    {
        $result = $this->report->getTitle();
        $this->assertNull($result);
    }

    public function test_it_sets_title()
    {
        $expected = 'Test Report Title';
        $result = $this->report->setTitle($expected);

        $this->assertSame($this->report, $result);
        $this->assertEquals($expected, $this->report->getTitle());
    }

    public function test_it_gets_comment()
    {
        $result = $this->report->getComment();
        $this->assertNull($result);
    }

    public function test_it_sets_comment()
    {
        $expected = 'Processing completed successfully';
        $result = $this->report->setComment($expected);

        $this->assertSame($this->report, $result);
        $this->assertEquals($expected, $this->report->getComment());
    }

    public function test_it_gets_indication_alt()
    {
        $result = $this->report->getIndicationAlt();
        $this->assertNull($result);
    }

    public function test_it_sets_indication_alt()
    {
        $expected = 'Green indicates success';
        $result = $this->report->setIndicationAlt($expected);

        $this->assertSame($this->report, $result);
        $this->assertEquals($expected, $this->report->getIndicationAlt());
    }

    public function test_it_gets_indication_color()
    {
        $result = $this->report->getIndicationColor();
        $this->assertNull($result);
    }

    public function test_it_sets_indication_color()
    {
        $expected = '#00FF00';
        $result = $this->report->setIndicationColor($expected);

        $this->assertSame($this->report, $result);
        $this->assertEquals($expected, $this->report->getIndicationColor());
    }

    public function test_it_gets_score_given()
    {
        $result = $this->report->getScoreGiven();
        $this->assertNull($result);
    }

    public function test_it_sets_score_given()
    {
        $expected = 85.5;
        $result = $this->report->setScoreGiven($expected);

        $this->assertSame($this->report, $result);
        $this->assertEquals($expected, $this->report->getScoreGiven());
    }

    public function test_it_gets_score_maximum()
    {
        $result = $this->report->getScoreMaximum();
        $this->assertNull($result);
    }

    public function test_it_sets_score_maximum()
    {
        $expected = 100.0;
        $result = $this->report->setScoreMaximum($expected);

        $this->assertSame($this->report, $result);
        $this->assertEquals($expected, $this->report->getScoreMaximum());
    }

    public function test_it_gets_error_code()
    {
        $result = $this->report->getErrorCode();
        $this->assertNull($result);
    }

    public function test_it_sets_error_code()
    {
        $expected = 'ASSET_TOO_LARGE';
        $result = $this->report->setErrorCode($expected);

        $this->assertSame($this->report, $result);
        $this->assertEquals($expected, $this->report->getErrorCode());
    }

    public function test_it_creates_array_with_no_optional_properties()
    {
        $expected = [
            'assetId' => self::INITIAL_ASSET_ID,
            'type' => self::INITIAL_TYPE,
            'processingProgress' => self::INITIAL_PROCESSING_PROGRESS,
            'priority' => self::INITIAL_PRIORITY,
            'timestamp' => self::INITIAL_TIMESTAMP,
        ];

        $result = $this->report->toArray();
        $this->assertEquals($expected, $result);
    }

    public function test_it_creates_array_with_defined_optional_properties()
    {
        $expectedTitle = 'Test Report';
        $expectedComment = 'Processing successful';
        $expectedIndicationAlt = 'Success indicator';
        $expectedIndicationColor = '#00FF00';
        $expectedScoreGiven = 95.0;
        $expectedScoreMaximum = 100.0;
        $expectedErrorCode = null;

        $this->report->setTitle($expectedTitle);
        $this->report->setComment($expectedComment);
        $this->report->setIndicationAlt($expectedIndicationAlt);
        $this->report->setIndicationColor($expectedIndicationColor);
        $this->report->setScoreGiven($expectedScoreGiven);
        $this->report->setScoreMaximum($expectedScoreMaximum);

        $expected = [
            'assetId' => self::INITIAL_ASSET_ID,
            'type' => self::INITIAL_TYPE,
            'processingProgress' => self::INITIAL_PROCESSING_PROGRESS,
            'priority' => self::INITIAL_PRIORITY,
            'timestamp' => self::INITIAL_TIMESTAMP,
            'indicationAlt' => $expectedIndicationAlt,
            'indicationColor' => $expectedIndicationColor,
            'scoreGiven' => $expectedScoreGiven,
            'scoreMaximum' => $expectedScoreMaximum,
            'title' => $expectedTitle,
            'comment' => $expectedComment,
        ];

        $result = $this->report->toArray();
        $this->assertEquals($expected, $result);
    }

    public function test_it_creates_array_with_error_code()
    {
        $expectedErrorCode = 'UNSUPPORTED_ASSET_TYPE';
        $this->report->setErrorCode($expectedErrorCode);

        $expected = [
            'assetId' => self::INITIAL_ASSET_ID,
            'type' => self::INITIAL_TYPE,
            'processingProgress' => self::INITIAL_PROCESSING_PROGRESS,
            'priority' => self::INITIAL_PRIORITY,
            'timestamp' => self::INITIAL_TIMESTAMP,
            'errorCode' => $expectedErrorCode,
        ];

        $result = $this->report->toArray();
        $this->assertEquals($expected, $result);
    }

    public function test_fluent_interface_chaining()
    {
        $result = $this->report
            ->setTitle('Chained Title')
            ->setComment('Chained Comment')
            ->setIndicationAlt('Chained Alt')
            ->setIndicationColor('#FF0000')
            ->setScoreGiven(75.5)
            ->setScoreMaximum(100.0)
            ->setErrorCode('EULA_NOT_ACCEPTED');

        $this->assertSame($this->report, $result);
        $this->assertEquals('Chained Title', $this->report->getTitle());
        $this->assertEquals('Chained Comment', $this->report->getComment());
        $this->assertEquals('Chained Alt', $this->report->getIndicationAlt());
        $this->assertEquals('#FF0000', $this->report->getIndicationColor());
        $this->assertEquals(75.5, $this->report->getScoreGiven());
        $this->assertEquals(100.0, $this->report->getScoreMaximum());
        $this->assertEquals('EULA_NOT_ACCEPTED', $this->report->getErrorCode());
    }

    public function test_get_array_returns_raw_array_with_nulls()
    {
        $this->report->setTitle('Test Title');

        $expected = [
            'assetId' => self::INITIAL_ASSET_ID,
            'type' => self::INITIAL_TYPE,
            'processingProgress' => self::INITIAL_PROCESSING_PROGRESS,
            'priority' => self::INITIAL_PRIORITY,
            'timestamp' => self::INITIAL_TIMESTAMP,
            'errorCode' => null,
            'indicationAlt' => null,
            'indicationColor' => null,
            'scoreGiven' => null,
            'scoreMaximum' => null,
            'title' => 'Test Title',
            'comment' => null,
        ];

        $result = $this->report->getArray();
        $this->assertEquals($expected, $result);
    }

    public function test_to_array_filters_out_nulls()
    {
        $this->report->setTitle('Test Title');

        $expected = [
            'assetId' => self::INITIAL_ASSET_ID,
            'type' => self::INITIAL_TYPE,
            'processingProgress' => self::INITIAL_PROCESSING_PROGRESS,
            'priority' => self::INITIAL_PRIORITY,
            'timestamp' => self::INITIAL_TIMESTAMP,
            'title' => 'Test Title',
        ];

        $result = $this->report->toArray();
        $this->assertEquals($expected, $result);
    }

    public function test_processing_progress_valid_values()
    {
        $validValues = ['Processed', 'Processing', 'PendingManual', 'Failed', 'NotProcessed', 'NotReady'];

        foreach ($validValues as $value) {
            $report = Report::new('test-id', 'test-type', $value, 1, '2024-01-01T00:00:00Z');
            $this->assertEquals($value, $report->getProcessingProgress());
        }
    }

    public function test_priority_boundary_values()
    {
        $boundaryValues = [0, 1, 2, 3, 4, 5];

        foreach ($boundaryValues as $priority) {
            $report = Report::new('test-id', 'test-type', 'Processed', $priority, '2024-01-01T00:00:00Z');
            $this->assertEquals($priority, $report->getPriority());
        }
    }

    public function test_error_code_valid_values()
    {
        $validErrorCodes = [
            'UNSUPPORTED_ASSET_TYPE',
            'ASSET_TOO_LARGE',
            'ASSET_TOO_SMALL',
            'EULA_NOT_ACCEPTED',
            'DOWNLOAD_FAILED',
        ];

        foreach ($validErrorCodes as $errorCode) {
            $this->report->setErrorCode($errorCode);
            $this->assertEquals($errorCode, $this->report->getErrorCode());
        }
    }

    public function test_hex_color_format()
    {
        $validHexColors = ['#000000', '#FFFFFF', '#FF0000', '#00FF00', '#0000FF', '#123ABC'];

        foreach ($validHexColors as $color) {
            $this->report->setIndicationColor($color);
            $this->assertEquals($color, $this->report->getIndicationColor());
        }
    }

    public function test_score_precision()
    {
        $preciseScores = [0.0, 0.1, 50.5, 99.99, 100.0];

        foreach ($preciseScores as $score) {
            $this->report->setScoreGiven($score);
            $this->report->setScoreMaximum($score);
            $this->assertEquals($score, $this->report->getScoreGiven());
            $this->assertEquals($score, $this->report->getScoreMaximum());
        }
    }
}
