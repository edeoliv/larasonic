<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="Larasonic is a modern Laravel boilerplate for the VILT stack (Vue, Inertia, Laravel, TailwindCSS). Clone and start building scalable, maintainable, and production-ready applications quickly." />
    <link rel="canonical" href="https://larasonic.com/" />

    <!-- Robots & Indexing -->
    <meta name="robots" content="index, follow" />

    <!-- Keyword Tags (Less impactful nowadays, but still can be included) -->
    <meta name="keywords" content="Larasonic, Laravel boilerplate, Laravel VILT, Vue, Inertia, TailwindCSS, Laravel Octane, Docker, FilamentPHP, OpenAI integration, Laravel Cashier, Laravel Sanctum" />

    <!-- Favicon & App Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#000000">

    <!-- Structured Data (Example: JSON-LD Schema.org) -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Larasonic",
            "url": "https://larasonic.com/",
            "image": "https://larasonic.com/images/og.webp",
            "description": "A modern Laravel SaaS starter kit for the VILT stack. Clone the repo, start building scalable and maintainable applications quickly.",
            "applicationCategory": "DeveloperTool",
            "operatingSystem": "All",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "USD",
                "category": "Free"
            }
        }
    </script>

    <!-- Basic OG Tags -->
    <meta property="og:title" content="Larasonic - Your Laravel VILT Boilerplate" />
    <meta property="og:description" content="Larasonic is a modern Laravel SaaS starter kit for the VILT stack. Clone the repo, start building scalable and maintainable applications quickly." />
    <meta property="og:url" content="https://larasonic.com" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="https://larasonic.com/images/og.webp" />

    <!-- Optional: Additional Tags -->
    <meta property="og:site_name" content="Larasonic" />
    <meta property="og:locale" content="en_US" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Larasonic - Your Laravel VILT Boilerplate" />
    <meta name="twitter:description" content="A modern Laravel boilerplate that boosts your developer experience with built-in tools and integrations. Clone and start building!" />
    <meta name="twitter:image" content="https://larasonic.com/images/og.webp" />
    <meta name="twitter:site" content="@YourTwitterHandle" />

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
