const mix = require('laravel-mix');

// Compile CSS
mix.css('resources/css/welcome.css', 'public/css');

// Enable versioning for cache busting (optional)
if (mix.inProduction()) {
    mix.version();
}
