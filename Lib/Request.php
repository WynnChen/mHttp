<?php
namespace mHttp;
class Request extends \ArrayObject
{
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';

	private string $url;
	private string $method = self::METHOD_GET;
	private ?array $data = null;
	private ?array $options = null;
	private ?array $headers = null;
	private ?Client $client = null;

	private bool $auto_retry = true;


	/**
	 * @var callable|null;
	 */
	private $on_complete = null;

	public function __construct(string $url = null, ?array $data = null)
	{
		$url and $this->setUrl($url);
		$data and $this->setData($data);
	}

	public function retry()
	{
		if($this->client){
			$this->client->addRequest($this);
			return true;
		}
		return false;
	}

	public function onComplete($response, $request_info)
	{
		//默认处理，保存就完事了。
		$this['response'] = $response;
		$this['request_info'] = $request_info;
		//检查是否需要retry
		if(!$response and $this->isAutoRetry()){
			$this->retry();
			return;
		}
		if($this->on_complete) {
			//有额外的处理
			call_user_func($this->on_complete, $this, $response, $request_info);
		}
	}

	/**
	 * callback 函数接受3个参数 $request, $response, $request_info
	 * @param callback $callback
	 * @return Request
	 */
	public function setOnCompleteHandle(callable $callback):self
	{
		$this->on_complete = $callback;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string
	{
		return $this->url;
	}
	/**
	 * @param string $url
	 * @return Request
	 */
	public function setUrl(string $url): Request
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMethod(): string
	{
		return $this->method;
	}

	/**
	 * @param string $method
	 * @return Request
	 */
	public function setMethod(string $method): Request
	{
		$this->method = $method;
		return $this;
	}

	/**
	 * @return array|null
	 */
	public function getData(): ?array
	{
		return $this->data;
	}

	/**
	 * @param array|null $data
	 * @return Request
	 */
	public function setData(?array $data): Request
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * @return array|null
	 */
	public function getOptions(): ?array
	{
		return $this->options;
	}

	/**
	 * @param array|null $options
	 * @return Request
	 */
	public function setOptions(?array $options): Request
	{
		$this->options = $options;
		return $this;
	}

	/**
	 * @return array|null
	 */
	public function getHeaders(): ?array
	{
		return $this->headers;
	}

	/**
	 * @param array|null $headers
	 * @return Request
	 */
	public function setHeaders(?array $headers): Request
	{
		$this->headers = $headers;
		return $this;
	}

	/**
	 * @return Client|null
	 */
	public function getClient(): ?Client
	{
		return $this->client;
	}

	/**
	 * @param Client|null $client
	 * @return Request
	 */
	public function setClient(?Client $client): Request
	{
		$this->client = $client;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAutoRetry(): bool
	{
		return $this->auto_retry;
	}

	/**
	 * @param bool $auto_retry
	 * @return Request
	 */
	public function setAutoRetry(bool $auto_retry): Request
	{
		$this->auto_retry = $auto_retry;
		return $this;
	}


}