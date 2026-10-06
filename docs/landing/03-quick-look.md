---
view: components.packages.quick-look
title: A quick look
subtitle: Unicode by default, ASCII on demand, and a fluent builder when you need it.
file: app/Support/Permalinks.php
language: php
code: |
  use Pharaonic\Slugify\Slugify;

  Slugify::make('Hello World');                  // hello-world
  Slugify::make('مرحبا بالعالم');                 // مرحبا-بالعالم
  Slugify::make('Crème brûlée', '-', true);      // creme-brulee

  Slugify::of('Äpfel & Öl im XMLHttpRequest')
      ->ascii('de')
      ->rule('&', ' and ')
      ->maxLength(30)
      ->toString();                              // aepfel-and-oel-im-xml-http
---
