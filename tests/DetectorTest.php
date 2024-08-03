<?php

use Tal7aouy\Detector;

beforeEach(function () {
    $this->validUrl = 'https://google.com';
    $this->invalidUrl = 'not-a-url';
});


test("onstructor initializes with valid URL", function () {
    $detector = new Detector($this->validUrl);
    expect($detector)->toBeInstanceOf(Detector::class);
});
test('constructor throws exception with invalid URL', function () {
    expect(fn () => new Detector($this->invalidUrl))->toThrow(InvalidArgumentException::class);
});

test("fetchWebsite method fetches website", function () {
    $detector = new Detector($this->validUrl);
    expect($detector->detectTechnologies())->not->toBeEmpty();
});
