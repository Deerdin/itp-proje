<?php
declare(strict_types=1);
function production(): bool { return getenv('APP_ENV') === 'production'; }
function secureRequest(): bool {
    // Do not trust client-supplied forwarding headers. cPanel/Apache terminates HTTPS directly.
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (int)($_SERVER['SERVER_PORT'] ?? 0) === 443;
}
function inputString(array $input, string $key, string $default=''): string {
    if (!array_key_exists($key,$input)) return $default;
    if (!is_string($input[$key])) throw new DomainException('Lütfen form alanlarını geçerli metin değerleriyle doldurun.');
    return $input[$key];
}
function enforceProductionConfig(): void {
    if (!production()) return;
    if (PHP_VERSION_ID < 80200) throw new RuntimeException('Canlı ortam PHP 8.2 veya üzerini gerektirir.');
    $url=getenv('APP_URL') ?: '';
    if (filter_var($url,FILTER_VALIDATE_URL)===false || parse_url($url,PHP_URL_SCHEME)!=='https' || !parse_url($url,PHP_URL_HOST)) {
        throw new RuntimeException('APP_URL geçerli bir HTTPS adresi olmalıdır.');
    }
    if ((getenv('DB_DRIVER') ?: '') !== 'mysql' || !getenv('DB_NAME') || !getenv('DB_USER') || !getenv('DB_PASSWORD')) {
        throw new RuntimeException('Canlı ortam için MySQL bağlantı ayarları gereklidir.');
    }
    if (strlen(getenv('APP_KEY') ?: '') < 64) throw new RuntimeException('APP_KEY en az 64 karakter olmalıdır.');
    if (getenv('KVKK_TEST_DB')) throw new RuntimeException('Test veritabanı canlı ortamda kullanılamaz.');
}
