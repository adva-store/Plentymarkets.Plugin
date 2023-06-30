<?php /** @noinspection PhpUnused */

namespace Advastore\Models\Request;

class RequestModel
{
    public string $contentType = 'application/json';
	public mixed  $postfields  = false;
	public mixed  $requestBody = false;
    public ?string $requestURL;
}
