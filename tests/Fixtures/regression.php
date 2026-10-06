<?php

/*
 * Historical behaviour that must keep working.
 *
 * Format: [input, separator, ascii, language, expected]
 */

return [
    // 2.x test suite
    ['hello world', '-', false, 'en', 'hello-world'],
    ['hello-world', '-', false, 'en', 'hello-world'],
    ['hello_world', '-', false, 'en', 'hello-world'],
    ['hello_world', '_', false, 'en', 'hello_world'],
    ['user@host', '-', false, 'en', 'user-at-host'],
    ['سلام دنیا', '-', false, 'en', 'سلام-دنیا'],
    ['some text', '', false, 'en', 'sometext'],
    ['', '', false, 'en', ''],
    ['', '-', false, 'en', ''],
    ['There is FAQ module here', '-', false, 'en', 'there-is-faq-module-here'],
    ['methodNameInCamelCase', '-', false, 'en', 'method-name-in-camel-case'],
    ['ClassNameIn-PascalCase', '_', false, 'en', 'class_name_in_pascal_case'],

    // 2.x outputs captured before the rebuild
    ['Hello World', '-', false, 'en', 'hello-world'],
    ['  hello   world  ', '-', false, 'en', 'hello-world'],
    ['hello---world', '-', false, 'en', 'hello-world'],
    ['hello___world', '-', false, 'en', 'hello-world'],
    ['hello - _ world', '-', false, 'en', 'hello-world'],
    ['---', '-', false, 'en', ''],
    ['___', '_', false, 'en', ''],
    ['   ', '-', false, 'en', ''],
    ['😀🎉', '-', false, 'en', ''],
    ['A & B', '-', false, 'en', 'a-b'],
    ['1+1=2', '-', false, 'en', '1-1-2'],
    ['50%', '-', false, 'en', '50'],
    ['$100', '-', false, 'en', '100'],
    ['helloWorld', '-', false, 'en', 'hello-world'],
    ['HelloWorld', '-', false, 'en', 'hello-world'],
    ['getUserID', '-', false, 'en', 'get-user-id'],
    ['PharaonicPHP', '-', false, 'en', 'pharaonic-php'],
    ['iPhone 15', '-', false, 'en', 'i-phone-15'],
    ['hello.world', '-', false, 'en', 'hello-world'],
    ['a~b', '-', false, 'en', 'a-b'],
    ['مرحبا بالعالم', '-', false, 'en', 'مرحبا-بالعالم'],
    ['مُحَمَّد', '-', false, 'en', 'محمد'],
    ['مرحبا بالعالم', '-', true, 'en', 'mrhba-balaaalm'],
    ['سلام دنیا', '-', true, 'en', 'slam-dnya'],
    ['Привет мир', '-', true, 'en', 'privet-mir'],
    ['Crème brûlée', '-', true, 'en', 'creme-brulee'],
    ['München', '-', true, 'en', 'munchen'],
    ['München', '-', true, 'de', 'muenchen'],
    ['Äpfel und Öl', '-', true, 'en', 'apfel-und-ol'],
    ['Äpfel und Öl', '-', true, 'de', 'aepfel-und-oel'],
    ['Straße', '-', true, 'en', 'strasse'],
    ['çay şeker', '-', true, 'en', 'cay-seker'],
    ['١٢٣ ٤٥٦', '-', true, 'en', '123-456'],

    // 2.x bugs, fixed in 8.0 (see CHANGELOG.md)
    ['0', '-', false, 'en', '0'],
    ['XMLHttpRequest', '-', false, 'en', 'xml-http-request'],
    ['APIResponse', '-', false, 'en', 'api-response'],
    ['پژوهش گروه', '-', false, 'en', 'پژوهش-گروه'],
    ['नमस्ते दुनिया', '-', false, 'en', 'नमस्ते-दुनिया'],
    ['user@host', '-', true, 'en', 'user-at-host'],
    ['۱۲۳', '-', true, 'en', '123'],
    ['你好世界', '-', true, 'en', 'ni-hao-shi-jie'],
    ['İstanbul', '-', false, 'en', 'istanbul'],
];
