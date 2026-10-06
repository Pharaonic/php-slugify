<?php

/*
 * Vietnamese. Generic result is correct, including "Đ" => "d"
 * (unlike Serbian "dj"). Pharaonic override: no.
 */

return [
    ['input' => 'Việt Nam Hà Nội', 'expected_ascii' => 'viet-nam-ha-noi', 'expected_unicode' => 'việt-nam-hà-nội'],
    ['input' => 'Đà Nẵng Phở Nguyễn', 'expected_ascii' => 'da-nang-pho-nguyen'],
];
