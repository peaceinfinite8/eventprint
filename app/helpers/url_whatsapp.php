/**
* Truncate text to specified length
* @param string $text Text to truncate
* @param int $length Maximum length
* @param string $suffix Suffix to add when truncated
* @return string Truncated text
*/
function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
if (mb_strlen($text) <= $length) { return $text; } return mb_substr($text, 0, $length) . $suffix; } /** * Normalize
    WhatsApp number to wa.me URL * @param string $input Phone number or URL * @return string WhatsApp chat URL or empty
    string */ function normalizeWhatsApp(string $input): string { if (empty($input)) { return '' ; } // If already a
    URL, use it directly if (filter_var($input, FILTER_VALIDATE_URL)) { return $input; } // Clean phone number: remove
    spaces, dashes, parentheses, etc. $cleaned=preg_replace('/[^0-9+]/', '' , $input); // Remove leading 0 and add 62
    (Indonesia country code) if (substr($cleaned, 0, 1)==='0' ) { $cleaned='62' . substr($cleaned, 1); } // Remove + if
    present at the start $cleaned=ltrim($cleaned, '+' ); // Must be numeric after cleaning if (!is_numeric($cleaned)) {
    return '' ; } return 'https://wa.me/' . $cleaned; }