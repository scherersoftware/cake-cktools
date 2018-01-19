<?php
declare(strict_types = 1);
namespace CkTools\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * Security component. Sets response headers to increase security. The headers can be configured and
 * also be overwritten in specific actions or controller callbacks.
 */
class SecurityComponent extends Component
{

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event): void
    {
        $securityConfig = Configure::read('CkTools.Security');
        if (isset($securityConfig['HSTS']) && $securityConfig['HSTS']) {
            $this->response->header('Strict-Transport-Security', $securityConfig['HSTS']);
        }
        if (isset($securityConfig['CSP']) && $securityConfig['CSP']) {
            $headerValue = '';
            if (is_string($securityConfig['CSP'])) {
                $headerValue = $securityConfig['CSP'];
            }
            if (is_array($securityConfig['CSP'])) {
                foreach ($securityConfig['CSP'] as $area => $allowed) {
                    if (is_array($allowed)) {
                        $allowed = implode(' ', $allowed);
                    }
                    $headerValue .= $area . ' ' . $allowed . ';';
                }
            }

            if (!empty($headerValue)) {
                $this->response->header('Content-Security-Policy', $headerValue);
            }
        }
        if (isset($securityConfig['denyFraming']) && $securityConfig['denyFraming'] === true) {
            // Superceded by the Content Security Policy's frame-ancestors directive, but as frame-ancestors
            // is not yet supported in IE11 and older, Edge, Safari 9.1 (desktop), and Safari 9.2 (iOS),
            // it is recommended that sites employ X-Frame-Options in addition to using CSP.
            $this->response->header('X-Frame-Options', 'DENY');
        }

        // Prevent browsers from incorrectly detecting non-scripts as scripts
        $this->response->header('X-Content-Type-Options', 'nosniff');
        $this->response->header('X-XSS-Protection', '1; mode=block');
    }
}
