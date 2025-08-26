<?php 
namespace Root0x7;

use Root0x7\Keyboard;
use Root0x7\Files;

class Api
{
	public $keyboard;
	public $files;
	private string $token;
	private string $apiUrl;
	private array $defaultOptions;
	private ?array $lastResponse = null;
	private ?array $lastError = null;

	public function __construct($token,$options)
	{
		$this->keyboard = new Keyboard();
		$this->files = new Files();
		$this->token = $token;
		$this->apiUrl = "https://api.telegram.org/bot{$token}/";
		$this->defaultOptions = array_merge([
			'parse_mode' => 'HTML',
			'timeout' => 30,
			'connect_timeout' => 10,
		], $options);
	}
	public function request($method,$datas=[])
	{
		$url = "https://api.telegram.org/bot".$this->token."/".$method;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
		$res = curl_exec($ch);
		if(curl_error($ch)){
			var_dump(curl_error($ch));
		}else{
			return json_decode($res);
		}
	}

	public function sendMessage(
		$chatId, 
		string $text, 
		array $options = []
	): array {
		return $this->request('sendMessage', array_merge([
			'chat_id' => $chatId,
			'text' => $text,
		], $options));
	}

	public function editMessage(
		$chatId, 
		int $messageId, 
		string $text, 
		array $options = []
	): array {
		return $this->request('editMessageText', array_merge([
			'chat_id' => $chatId,
			'message_id' => $messageId,
			'text' => $text,
		], $options));
	}

	public function deleteMessage($chatId, int $messageId): array
	{
		return $this->request('deleteMessage', [
			'chat_id' => $chatId,
			'message_id' => $messageId,
		]);
	}

	public function forwardMessage($chatId, $fromChatId, int $messageId): array
	{
		return $this->request('forwardMessage', [
			'chat_id' => $chatId,
			'from_chat_id' => $fromChatId,
			'message_id' => $messageId,
		]);
	}

    // =============== MEDIA METODLAR ===============

	public function sendPhoto(
		$chatId, 
		$photo, 
		string $caption = '', 
		array $options = []
	): array {
		$params = array_merge([
			'chat_id' => $chatId,
			'photo' => $photo,
		], $options);

		if ($caption) {
			$params['caption'] = $caption;
		}

		return $this->request('sendPhoto', $params);
	}

	public function sendAudio(
		$chatId, 
		$audio, 
		array $options = []
	): array {
		return $this->request('sendAudio', array_merge([
			'chat_id' => $chatId,
			'audio' => $audio,
		], $options));
	}

	public function sendVideo(
		$chatId, 
		$video, 
		array $options = []
	): array {
		return $this->request('sendVideo', array_merge([
			'chat_id' => $chatId,
			'video' => $video,
		], $options));
	}

	public function sendDocument(
		$chatId, 
		$document, 
		array $options = []
	): array {
		return $this->request('sendDocument', array_merge([
			'chat_id' => $chatId,
			'document' => $document,
		], $options));
	}

	public function sendSticker($chatId, $sticker, array $options = []): array
	{
		return $this->request('sendSticker', array_merge([
			'chat_id' => $chatId,
			'sticker' => $sticker,
		], $options));
	}

	public function sendAnimation($chatId, $animation, array $options = []): array
	{
		return $this->request('sendAnimation', array_merge([
			'chat_id' => $chatId,
			'animation' => $animation,
		], $options));
	}

	public function sendVoice($chatId, $voice, array $options = []): array
	{
		return $this->request('sendVoice', array_merge([
			'chat_id' => $chatId,
			'voice' => $voice,
		], $options));
	}

	public function sendLocation(
		$chatId, 
		float $latitude, 
		float $longitude, 
		array $options = []
	): array {
		return $this->request('sendLocation', array_merge([
			'chat_id' => $chatId,
			'latitude' => $latitude,
			'longitude' => $longitude,
		], $options));
	}

	public function answerCallbackQuery(
		string $callbackQueryId, 
		string $text = '', 
		bool $showAlert = false
	): array {
		return $this->request('answerCallbackQuery', [
			'callback_query_id' => $callbackQueryId,
			'text' => $text,
			'show_alert' => $showAlert,
		]);
	}

	public function answerInlineQuery(
		string $inlineQueryId, 
		array $results, 
		array $options = []
	): array {
		return $this->request('answerInlineQuery', array_merge([
			'inline_query_id' => $inlineQueryId,
			'results' => json_encode($results),
		], $options));
	}


	public function getChat($chatId): array
	{
		return $this->request('getChat', ['chat_id' => $chatId]);
	}

	public function getChatMember($chatId, int $userId): array
	{
		return $this->request('getChatMember', [
			'chat_id' => $chatId,
			'user_id' => $userId,
		]);
	}

	public function kickChatMember($chatId, int $userId, int $untilDate = 0): array
	{
		return $this->request('kickChatMember', [
			'chat_id' => $chatId,
			'user_id' => $userId,
			'until_date' => $untilDate,
		]);
	}

	public function unbanChatMember($chatId, int $userId): array
	{
		return $this->request('unbanChatMember', [
			'chat_id' => $chatId,
			'user_id' => $userId,
		]);
	}

	public function promoteChatMember($chatId, int $userId, array $permissions = []): array
	{
		return $this->request('promoteChatMember', array_merge([
			'chat_id' => $chatId,
			'user_id' => $userId,
		], $permissions));
	}

	public function restrictChatMember($chatId, int $userId, array $permissions = []): array
	{
		return $this->request('restrictChatMember', array_merge([
			'chat_id' => $chatId,
			'user_id' => $userId,
		], $permissions));
	}


	public function getMe(): array
	{
		return $this->request('getMe');
	}

	public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 0): array
	{
		return $this->request('getUpdates', [
			'offset' => $offset,
			'limit' => $limit,
			'timeout' => $timeout,
		]);
	}

	public function setWebhook(string $url, array $options = []): array
	{
		return $this->request('setWebhook', array_merge([
			'url' => $url,
		], $options));
	}

	public function deleteWebhook(): array
	{
		return $this->request('deleteWebhook');
	}

	public function getWebhookInfo(): array
	{
		return $this->request('getWebhookInfo');
	}

	public function getWebhookUpdate(): ?array
	{
		$input = file_get_contents('php://input');
		if ($input) {
			return json_decode($input, true);
		}
		return null;
	}

	public function downloadFile(string $filePath, string $savePath = null): string
	{
		$url = "https://api.telegram.org/file/bot{$this->token}/{$filePath}";
		$content = file_get_contents($url);

		if ($savePath) {
			file_put_contents($savePath, $content);
			return $savePath;
		}

		return $content;
	}

	public function getFile(string $fileId): array
	{
		return $this->request('getFile', ['file_id' => $fileId]);
	}


	public function getLastResponse(): ?array
	{
		return $this->lastResponse;
	}

	public function getLastError(): ?array
	{
		return $this->lastError;
	}

	public function hasError(): bool
	{
		return $this->lastError !== null;
	}


	private function UserRequest(string $method, array $params = []): array
	{
		$this->lastError = null;

        // Default optionsni qo'shish
		$params = array_merge($this->defaultOptions, $params);

		$url = $this->apiUrl . $method;

		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $params,
			CURLOPT_TIMEOUT => $this->defaultOptions['timeout'],
			CURLOPT_CONNECTTIMEOUT => $this->defaultOptions['connect_timeout'],
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_USERAGENT => 'phpTeleBot/2.0',
		]);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		curl_close($ch);

		if ($error) {
			$this->lastError = ['curl_error' => $error];
			throw new Exception("CURL Error: {$error}");
		}

		if ($httpCode !== 200) {
			$this->lastError = ['http_code' => $httpCode, 'response' => $response];
			throw new Exception("HTTP Error: {$httpCode}");
		}

		$decodedResponse = json_decode($response, true);
		$this->lastResponse = $decodedResponse;

		if (!$decodedResponse['ok']) {
			$this->lastError = $decodedResponse;
			throw new Exception("API Error: " . $decodedResponse['description']);
		}

		return $decodedResponse;
	}
}