<?php

declare(strict_types=1);

namespace BackendBase\Shared\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ulid\Ulid;
use function base64_decode;
use function explode;
use function fclose;
use function fopen;
use function fwrite;

final class CatchUploadedFile implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $contentType             = $request->getHeaderLine('Content-Type');
        $contentTransferEncoding = $request->getHeaderLine('Content-Transfer-Encoding');
        if ($contentType === 'application/octet-stream') {
            $request = $request
                ->withAttribute('uploadedFilePath', $this->storeUploadedFile($request->getBody(), $contentTransferEncoding));
        }

        return $handler->handle($request);
    }

    private function storeUploadedFile(StreamInterface $body, ?string $contentTransferEncoding) : string
    {
        $fileName    = (string) Ulid::generate();
        $filePath    = 'data/storage/temp/' . $fileName;
        $fileContent = '';
        while (! $body->eof()) {
            $fileContent .= $body->read(4096);
        }
        $fileHandle = fopen($filePath, 'w');
        if ($contentTransferEncoding === 'Base64') {
            $data        = explode(',', $fileContent);
            $fileContent = base64_decode($data[1]);
        }
        fwrite($fileHandle, $fileContent);
        fclose($fileHandle);

        return 'temp/' . $fileName;
    }
}
