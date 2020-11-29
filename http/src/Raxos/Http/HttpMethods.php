<?php
declare(strict_types=1);

namespace Raxos\Http;

/**
 * Class HttpConstants
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Http
 * @since 2.0.0
 */
final class HttpMethods
{

	public const ALL = ['delete', 'get', 'head', 'options', 'patch', 'post', 'put', 'any'];

	public const ANY = 'any';
	public const DELETE = 'delete';
	public const GET = 'get';
	public const HEAD = 'head';
	public const OPTIONS = 'options';
	public const PATCH = 'patch';
	public const POST = 'post';
	public const PUT = 'put';

}
