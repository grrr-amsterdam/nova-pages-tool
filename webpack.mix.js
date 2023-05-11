let mix = require('laravel-mix');

require('./nova.mix');

mix
  .setPublicPath('dist')
  .vue({ version: 3 })
  .css('resources/css/tool.css', 'css')
  .nova('grrr/nova-pages-tool');
