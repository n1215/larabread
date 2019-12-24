<?php
declare(strict_types=1);

namespace N1215\Larabread;

use PHPUnit\Framework\TestCase;

/**
 * Class BreadcrumbTest
 * @package N1215\Larabread
 */
class BreadcrumbTest extends TestCase
{
    public function test_getTitle(): void
    {
        $breadcrumb = new Breadcrumb('Home');
        $this->assertEquals('Home', $breadcrumb->getTitle());
    }

    /**
     * @dataProvider dataProvider_getUrl
     * @param string|null $url
     */
    public function test_getUrl(?string $url): void
    {
        $breadcrumbs = new Breadcrumb('dummy', $url);
        $this->assertEquals($url, $breadcrumbs->getUrl());
    }

    public function dataProvider_getUrl(): array
    {
        return [
            ['https://example.com/test'],
            ['/home'],
            [null],
        ];
    }

    /**
     * @dataProvider dataProvider_getAttribute
     * @param mixed|null $expected
     * @param string $key
     * @param mixed|null $default
     */
    public function test_getAttribute($expected, string $key, $default): void
    {
        $attributes = [
            'key' => 'value',
        ];

        $breadcrumb = new Breadcrumb('dummy', 'https://example.com', $attributes);

        $this->assertEquals($expected, $breadcrumb->getAttribute($key, $default));
    }

    public function dataProvider_getAttribute(): array
    {
        return [
            ['value', 'key', null],
            ['value', 'key', 'default'],
            [null, 'other_key', null],
            ['default', 'other_key', 'default'],
        ];
    }

    public function test_json_encode(): void
    {
        $breadcrumb = new Breadcrumb('Home', '/home', ['key' => 'value']);

        $expected = '{"title":"Home","url":"\/home","attributes":{"key":"value"}}';

        $this->assertEquals($expected, json_encode($breadcrumb));
    }

    public function test_json_encode_for_empty_attributes(): void
    {
        $breadcrumb = new Breadcrumb('Home', '/home', []);

        $expected = '{"title":"Home","url":"\/home","attributes":{}}';

        $this->assertEquals($expected, json_encode($breadcrumb));
    }
}