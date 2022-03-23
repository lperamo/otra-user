<?php
declare(strict_types=1);
namespace tests\tools;

/**
 * Normalizing an HTML response by replacing dynamic content by static content
 *
 * @param string $htmlContent
 *
 * @return string
 */
function normalizingHtmlResponse(string $htmlContent) : string
{
  return preg_replace(
    [
      '@<link rel="stylesheet" nonce="[0-9a-f]{64}" href="/cache/css/[0-9a-f]{40}@',
      '@<script nonce="[0-9a-f]{64}"@',
      '@src="([^"]+)[0-9a-f]{40}@'
    ],
    [
      '<link rel="stylesheet" nonce="my-nonce" href="/cache/css/my-nonce',
      '<script nonce="my-nonce"',
      'src="$1my-nonce'
    ],
    $htmlContent
  );
}
