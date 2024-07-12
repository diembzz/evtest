<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use stdClass;

class CustomApiResponse
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        if (!$response instanceof JsonResponse) {
            return $response;
        }

        $status = $response->status();

        if ($status === 204) {
            return $response;
        }

        $data = $response->getData();

        if (isset($data->errors)) {
            return $this->error($response, get_object_vars($data->errors));
        }

        if (isset($data->message)) {
            return $this->error($response, $data->message);
        }

        if (isset($data->data)) {
            $data = $data->data;
        }

        return $this->success($response, $data);
    }

    /**
     * @param JsonResponse $response
     * @param stdClass|array|null $data
     * @return JsonResponse
     */
    protected function success(JsonResponse $response, stdClass|array|null $data = null): JsonResponse
    {
        $result = [
            'success' => true,
            'status' => $response->status(),
            'data' => $data,
        ];

        $paginator = $response->original instanceof LengthAwarePaginator
            ? $response->original
            : null;

        if ($paginator) {
            $result['meta']['pagination'] = [
                'total' => $paginator->total(),
                'pages' => $paginator->lastPage(),
                'page' => $paginator->currentPage(),
                'size' => $paginator->perPage(),
            ];
        }

        return $response->setData($result);
    }

    /**
     * @param JsonResponse $response
     * @param array|string $errors
     * @return JsonResponse
     */
    protected function error(JsonResponse $response, array|string $errors): JsonResponse
    {
        if (is_array($errors)) {
            return $response->setData([
                'success' => false,
                'status' => $response->status(),
                'errors' => array_map(fn($v) => $v[0], $errors),
            ]);
        }

        return $response->setData([
            'success' => false,
            'status' => $response->status(),
            'errors' => ['' => $errors],
        ]);
    }
}
