<?php
// Protect from session fixation via session adoption.
//ini_set('session.use_strict_mode', true);
# Only send session id cookie over SSL.
//ini_set('session.cookie_secure', true);
# Session IDs may only be passed via cookies, not appended to URL.
//ini_set('session.use_only_cookies', true);
//ini_set('session.cookie_path', rawurlencode(dirname($_SERVER['PHP_SELF'])));
session_start();

?>