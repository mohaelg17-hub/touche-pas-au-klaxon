<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Associe une adresse (URL) a une methode de controleur.
 *
 * Exemple : $router->get('/trajets/{id}/edit', [TrajetController::class, 'edit']);
 */
final class Router
{
    /** @var list<array{method: string, pattern: string, handler: array{0: class-string<Controller>, 1: string}}> */
    private array $routes = [];

    /**
     * Declare une route accessible en GET.
     *
     * @param array{0: class-string<Controller>, 1: string} $handler [classe du controleur, nom de la methode]
     */
    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    /**
     * Declare une route accessible en POST (formulaires).
     *
     * @param array{0: class-string<Controller>, 1: string} $handler [classe du controleur, nom de la methode]
     */
    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    /**
     * Trouve la route correspondant a la requete et appelle son controleur.
     * Affiche une page 404 si aucune route ne correspond.
     */
    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $path = is_string($path) ? '/' . trim($path, '/') : '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['pattern'], $path, $matches) !== 1) {
                continue;
            }

            // On ne garde que les parametres nommes, par exemple ['id' => '5'].
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            [$class, $action] = $route['handler'];
            $controller = new $class();
            $controller->$action($params);

            return;
        }

        http_response_code(404);
        View::render('errors/404');
    }

    /**
     * Enregistre une route en transformant {nom} en motif d'expression reguliere.
     *
     * @param array{0: class-string<Controller>, 1: string} $handler
     */
    private function add(string $method, string $path, array $handler): void
    {
        $pattern = '#^' . (string) preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path) . '$#';

        $this->routes[] = [
            'method'  => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }
}