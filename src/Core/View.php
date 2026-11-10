<?php

declare(strict_types=1);

namespace App\Core;

use DateTimeImmutable;
use RuntimeException;

/**
 * Affiche des vues (fichiers PHP/HTML) dans une mise en page commune.
 */
final class View
{
    private static string $basePath = '';

    /**
     * Definit le dossier qui contient les vues.
     */
    public static function setBasePath(string $path): void
    {
        self::$basePath = $path;
    }

    /**
     * Affiche une vue dans un layout.
     *
     * @param string               $view   Nom de la vue, ex. 'trajets/index'
     * @param array<string, mixed> $data   Variables disponibles dans la vue
     * @param string               $layout Nom de la mise en page
     */
    public static function render(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        $content = self::capture($view, $data);
        echo self::capture($layout, $data + ['content' => $content]);
    }

    /**
     * Echappe une valeur pour l'afficher sans risque dans du HTML (protection XSS).
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars(is_scalar($value) ? (string) $value : '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Formate la partie date d'un DATETIME SQL (ex. 15/11/26).
     */
    public static function date(string $datetime): string
    {
        return (new DateTimeImmutable($datetime))->format('d/m/y');
    }

    /**
     * Formate la partie heure d'un DATETIME SQL (ex. 08:30).
     */
    public static function time(string $datetime): string
    {
        return (new DateTimeImmutable($datetime))->format('H:i');
    }

    /**
     * Execute un fichier de vue et retourne son rendu sous forme de texte.
     *
     * @param array<string, mixed> $data
     *
     * @throws RuntimeException Si le fichier de vue n'existe pas.
     */
    private static function capture(string $template, array $data): string
    {
        $file = self::$basePath . '/' . $template . '.php';

        if (!is_file($file)) {
            throw new RuntimeException('Vue introuvable : ' . $template);
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;

        return (string) ob_get_clean();
    }
}