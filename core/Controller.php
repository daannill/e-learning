<?php

namespace Core;

class Controller {

    protected array $middleware = [];

    public function runMiddleware(string $method) {
        if (Request::isPost()) {
            Middleware::validateCsrf();
        }

        foreach ($this->middleware as $middleware => $options) {
            if (!method_exists(Middleware::class, $middleware)) {
                continue;
            }

            if (isset($options['only']) && in_array($method, $options['only'], true)) {
                Middleware::$middleware();
            }

            if (isset($options['except']) && !in_array($method, $options['except'], true)) {
                Middleware::$middleware();
            }

            if (!isset($options['only']) && !isset($options['except'])) {
                Middleware::$middleware();
            }
        }
    }
    
    protected function view(string $view, array $data = []) {
        extract($data);

        require "app/views/$view.php";

        Old::clear();
        Flash::clear();
    }

    protected function failIf(
        mixed $condition, 
        string $redirect, 
        string|array $errors, 
        ?string $message = null
    ) {
        if (!$condition) {
            return;
        }

        Old::set();

        if (is_array($errors)) {
            Flash::set('errors', $errors);
        } else {
            Flash::set($errors, $message);
        }

        Redirect::to($redirect);
    }

    protected function redirectIf(
        mixed $condition,
        string $redirect,
        ?string $flashKey = null,
        ?string $flashMessage = null
    ) {
        if (!$condition) {
            return;
        }

        if ($flashKey !== null) {
            Flash::set($flashKey, $flashMessage);
        }

        Redirect::to($redirect);
    }

    protected function abortIf(mixed $condition, int $code = 404) {
        if (!$condition) {
            return;
        }

        Abort::error($code);
    }
}