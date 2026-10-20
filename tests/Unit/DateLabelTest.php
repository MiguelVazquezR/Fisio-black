<?php

use App\Support\DateLabel;

test('it formats a range within the same month', function () {
    expect(DateLabel::range('2026-10-19', '2026-10-25'))
        ->toBe('19 – 25 de octubre de 2026');
});

test('it formats a range across two months of the same year', function () {
    expect(DateLabel::range('2026-09-28', '2026-10-04'))
        ->toBe('28 de septiembre – 4 de octubre de 2026');
});

test('it formats a range across two years', function () {
    expect(DateLabel::range('2025-12-29', '2026-01-04'))
        ->toBe('29 de diciembre de 2025 – 4 de enero de 2026');
});
