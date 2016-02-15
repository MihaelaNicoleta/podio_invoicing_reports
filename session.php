<?php
class PodioBrowserSession {

  /**
   * For sessions to work they must be started. We make sure to start
   * sessions whenever a new object is created.
   */
  public function __construct() {
    if(!session_id()) {
      session_start();
    }
  }

  /**
   * Get oauth object from session, if present. We ignore $auth_type since
   * it doesn't work with server-side authentication.
   */
  public function get($auth_type = null) {

    // Check if we have a stored session
    if (!empty($_SESSION['podio-php-session'])) {

      // We have a session, create new PodioOauth object and return it
      return new PodioOAuth(
        $_SESSION['podio-php-session']['access_token'],
        $_SESSION['podio-php-session']['refresh_token'],
        $_SESSION['podio-php-session']['expires_in'],
        $_SESSION['podio-php-session']['ref']
      );
    }

    // Else return an empty object
    return new PodioOAuth();
  }

  /**
   * Store the oauth object in the session. We ignore $auth_type since
   * it doesn't work with server-side authentication.
   */
  public function set($oauth, $auth_type = null) {

    // Save all properties of the oauth object in a session
    $_SESSION['podio-php-session'] = array(
      'access_token' => $oauth->access_token,
      'refresh_token' => $oauth->refresh_token,
      'expires_in' => $oauth->expires_in,
      'ref' => $oauth->ref,
    );

  }
}