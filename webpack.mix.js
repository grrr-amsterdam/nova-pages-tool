let mix = require('laravel-mix');

require('./nova.mix');

mix
  .setPublicPath('dist')
  .js('resources/js/field.js', 'js')
  .vue({ version: 3 })
  .sass('resources/sass/field.scss', 'css')
  .css('resources/css/tool.css', 'css')
  .nova('grrr/nova-pages-tool');
