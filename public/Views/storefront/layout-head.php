<?php
/**
 * Requires $seo (title/description/keywords/image/tags — resolved by
 * SeoMeta::resolve() in StorefrontController, editable at /admin/seo).
 * Product pages also set $seo['type'] = 'product' and $seo['product'], so a
 * shared product link previews with its featured image, the store logo and
 * the description the admin recorded.
 */
$storeName = storeDisplayName();
$product = $seo['product'] ?? null;
$isProduct = ($seo['type'] ?? '') === 'product' && $product;
$metaTitle = seoText($seo['title'], 70);
$metaDescription = seoText($seo['description'], 160);
$shareDescription = seoText($seo['description'], 300);

$logoPath = storeLogoPath();
$logoUrl = $logoPath ? imageUrl($logoPath) : null;
$featuredImage = !empty($seo['image']) ? imageUrl($seo['image']) : null;
// Featured image leads (it's what WhatsApp/Facebook/X show); the logo follows as a secondary image.
$shareImages = array_values(array_unique(array_filter([$featuredImage, $logoUrl])));
$tagList = !empty($seo['tags']) ? array_filter(array_map('trim', explode(',', $seo['tags']))) : [];

$canonicalUrl = url($seo['path'] ?? \App\Core\Url::currentPath());

$jsonLd = [[
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $storeName,
    'url' => url('/'),
] + ($logoUrl ? ['logo' => $logoUrl] : [])];
if ($isProduct) {
    $jsonLd[] = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product['name'],
        'sku' => $product['id'],
        'description' => $shareDescription,
        'image' => array_map('imageUrl', $product['images'] ?: array_filter([$seo['image'] ?? null])),
        'brand' => ['@type' => 'Brand', 'name' => $storeName],
        'offers' => [
            '@type' => 'Offer',
            'url' => $canonicalUrl,
            'priceCurrency' => 'KES',
            'price' => convertPrice((float) $product['price'], 'KSH'),
            'availability' => !empty($product['inStock']) ? 'https://schema.org/InStock' : 'https://schema.org/PreOrder',
        ],
    ];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($metaTitle) ?></title>
  <meta name="description" content="<?= e($metaDescription) ?>" />
  <?php if (!empty($seo['keywords'])): ?><meta name="keywords" content="<?= e($seo['keywords']) ?>" /><?php endif; ?>
  <meta name="robots" content="index, follow, max-image-preview:large" />
  <link rel="canonical" href="<?= e($canonicalUrl) ?>" />
  <?php if ($googleVerification = storeSettingValue('google_site_verification')): ?><meta name="google-site-verification" content="<?= e($googleVerification) ?>" /><?php endif; ?>
  <?php if ($bingVerification = storeSettingValue('bing_site_verification')): ?><meta name="msvalidate.01" content="<?= e($bingVerification) ?>" /><?php endif; ?>

  <?= faviconTags() ?>

  <!-- Open Graph (Facebook, WhatsApp, LinkedIn, Telegram…) -->
  <meta property="og:type" content="<?= $isProduct ? 'product' : 'website' ?>" />
  <meta property="og:url" content="<?= e($canonicalUrl) ?>" />
  <meta property="og:title" content="<?= e($metaTitle) ?>" />
  <meta property="og:description" content="<?= e($shareDescription) ?>" />
  <meta property="og:site_name" content="<?= e($storeName) ?>" />
  <meta property="og:locale" content="en_KE" />
  <?php foreach ($shareImages as $i => $img): ?>
    <meta property="og:image" content="<?= e($img) ?>" />
    <?php if (str_starts_with($img, 'https://')): ?><meta property="og:image:secure_url" content="<?= e($img) ?>" /><?php endif; ?>
    <meta property="og:image:alt" content="<?= e($i === 0 && $featuredImage ? ($product['name'] ?? $metaTitle) : $storeName . ' logo') ?>" />
  <?php endforeach; ?>
  <?php if ($isProduct): ?>
    <meta property="product:price:amount" content="<?= e((string) convertPrice((float) $product['price'], 'KSH')) ?>" />
    <meta property="product:price:currency" content="KES" />
    <meta property="product:availability" content="<?= !empty($product['inStock']) ? 'in stock' : 'preorder' ?>" />
    <meta property="product:retailer_item_id" content="<?= e($product['id']) ?>" />
  <?php endif; ?>
  <?php foreach ($tagList as $tag): ?><meta property="article:tag" content="<?= e($tag) ?>" /><?php endforeach; ?>

  <!-- Twitter / X -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= e($metaTitle) ?>" />
  <meta name="twitter:description" content="<?= e($metaDescription) ?>" />
  <?php if ($shareImages): ?>
    <meta name="twitter:image" content="<?= e($shareImages[0]) ?>" />
    <meta name="twitter:image:alt" content="<?= e($product['name'] ?? $storeName) ?>" />
  <?php endif; ?>

  <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>?v=<?= (int) @filemtime(dirname(__DIR__, 2) . '/assets/css/app.css') ?>">
</head>
<body class="bg-white text-[#1a1a1a] antialiased selection:bg-[#1a1a1a] selection:text-white">
  <div id="app" class="min-h-screen flex flex-col bg-white text-[#0a0a0a] font-sans antialiased">
