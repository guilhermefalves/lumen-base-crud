<?php

namespace LumenBaseCRUD;

use Illuminate\Http\JsonResponse;

/**
 * Trait para padronizar as respostas da API
 *
 * @author Guilherme Alves <guilherme.alves@jurid.com.br>
 */
trait APIResponse
{
    /**
     * Representação textual dos HTTP Status Code
     *
     * @see https://developer.mozilla.org/pt-BR/docs/Web/HTTP/Status
     * @var array
     */
    private array $statusCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        307 => 'Redirecionamento temporário',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    ];

    /**
     * Retorna uma resposta como um json
     *
     * @param integer $code O código HTTP da resposta, também gera o status da resposta
     * @param array $data Dados adicionais
     * @param string $message A mensagem a ser enviada como message
     * @return JsonResponse
     */
    final protected function response(int $code, array $data = [], string $message = ''): JsonResponse
    {
        [$status, $message] = $this->prepareStatusAndMessage($code, $data, $message);
        $data = array_merge(compact(['status', 'message']), $data);

        $headers = [
            'Content-Type' => 'application/json',
            'Charset' => 'utf8',
        ];

        return response()->json($data, $code, $headers, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Trata o status e a mensagem da resposta
     *
     * @param integer $code
     * @param array $data
     * @param string $message
     * @return array
     */
    private function prepareStatusAndMessage(int $code, array $data, string $message): array
    {
        // Define se foi uma requisição bem sucedida ou um erro
        $status = $this->statusCodes[$code];

        // Tenta encontrar uma mensagem para o código
        $message = (!$message && isset($data['message'])) ? $data['message'] : $message;

        return [$status, $message];
    }
}
