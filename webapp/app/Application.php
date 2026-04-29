<?php

declare(strict_types=1);

namespace App;

// Autoload classes from App namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__;

    if (strpos($class, $prefix) === 0) {
        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . '/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
});

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/TreeRepository.php';
require_once __DIR__ . '/../lib/AuthRepository.php';
require_once __DIR__ . '/../lib/PythonBridge.php';

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Strict']);
    session_start();
}

// Auth guard
if (!isset($_SESSION['user'])) {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    header('Location: /public/login.php?redirect=' . urlencode($uri));
    exit;
}

use Database;
use TreeRepository;
use PythonBridge;
use App\Models\TreeModel;
use App\Models\PredictionModel;
use App\Controllers\HomeController;
use App\Controllers\TreeController;
use App\Controllers\MapController;
use App\Controllers\PredictionController;
use App\Routes\Router;

class Application
{
    private static ?self $instance = null;
    private Router $router;
    private TreeModel $treeModel;
    private PredictionModel $predictionModel;

    private function __construct()
    {
        $this->initialize();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize the application
     */
    private function initialize(): void
    {
        // Initialize database and repositories
        $db = (new Database())->pdo();
        $treeRepository = new TreeRepository($db);
        $pythonBridge = new PythonBridge();

        // Initialize models
        $this->treeModel = new TreeModel($treeRepository);
        $this->predictionModel = new PredictionModel($pythonBridge);

        // Initialize controllers
        $homeController = new HomeController($this->treeModel);
        $treeController = new TreeController($this->treeModel);
        $mapController = new MapController($this->treeModel);
        $predictionController = new PredictionController($this->predictionModel);

        // Initialize router
        $this->router = new Router(
            $homeController,
            $treeController,
            $mapController,
            $predictionController
        );
    }

    /**
     * Run the application
     */
    public function run(): void
    {
        $response = $this->router->dispatch();

        if ($response['status'] !== 200) {
            http_response_code($response['status']);
            header('Content-Type: application/json');
            echo json_encode(['error' => $response['error']]);
            return;
        }

        $result = $response['result'];

        // Handle API responses (JSON)
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            header('Content-Type: application/json');
            echo json_encode($result);
            return;
        }

        // Handle page responses (HTML)
        $view = $result['view'] ?? null;
        $data = $result['data'] ?? [];

        if ($view) {
            $this->renderView($view, $data);
        }
    }

    /**
     * Render a view with data
     */
    private function renderView(string $view, array $data = []): void
    {
        $viewPath = __DIR__ . '/Views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(404);
            echo "Vue non trouvée: $view";
            return;
        }

        extract($data);
        require_once __DIR__ . '/../public/partials/header.php';
        require_once $viewPath;
        require_once __DIR__ . '/../public/partials/footer.php';
    }

    /**
     * Get tree model
     */
    public function getTreeModel(): TreeModel
    {
        return $this->treeModel;
    }

    /**
     * Get prediction model
     */
    public function getPredictionModel(): PredictionModel
    {
        return $this->predictionModel;
    }

    /**
     * Get router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }
}
