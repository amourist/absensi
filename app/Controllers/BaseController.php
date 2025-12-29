<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * Helpers that will be loaded automatically.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Shared properties
     */
    protected $session;
    protected $generalSettings;

    /**
     * Constructor
     */
    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        // Wajib
        parent::initController($request, $response, $logger);

        // Session
        $this->session = \Config\Services::session();

        // General settings
        $schoolConfigurations  = new \Config\School();
        $this->generalSettings = $schoolConfigurations::$generalSettings;

        // Global variable ke semua view
        $view = \Config\Services::renderer();
        $view->setData([
            'generalSettings' => $this->generalSettings
        ]);
    }

    /**
     * Kirim notifikasi WhatsApp (dipakai oleh Scan, dll)
     */
    protected function sendNotification(array $message)
    {
        $token    = getenv('WHATSAPP_TOKEN');
        $provider = getenv('WHATSAPP_PROVIDER');

        if (empty($provider) || empty($token)) {
            return;
        }

        switch ($provider) {
            case 'Fonnte':
                $whatsapp = new \App\Libraries\Whatsapp\Fonnte\Fonnte($token);
                break;
            default:
                return;
        }

        $whatsapp->sendMessage($message);
    }
}