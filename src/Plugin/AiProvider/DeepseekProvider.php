<?php

namespace Drupal\ai_provider_deepseek\Plugin\AiProvider;

use Drupal\ai\Attribute\AiProvider;
use Drupal\ai\Base\AiProviderClientBase;
use Drupal\ai\Exception\AiResponseErrorException;
use Drupal\ai\OperationType\Chat\ChatInput;
use Drupal\ai\OperationType\Chat\ChatInterface;
use Drupal\ai\OperationType\Chat\ChatMessage;
use Drupal\ai\OperationType\Chat\ChatOutput;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use \DeepseekPhp\DeepseekClient;
use \DeepseekPhp\Enums\Models;
use Symfony\Component\Yaml\Yaml;

/**
 * Plugin implementation of the DeepSeek LLM.
 */
#[AiProvider(
  id: 'deepseek',
  label: new TranslatableMarkup('DeepSeek')
)]
class DeepSeekProvider extends AiProviderClientBase implements ChatInterface {

  /**
   * DeepSeek client
   * 
   * @var DeepseekClient
   */
  protected $client;

  /**
   * API Key.
   *
   * @var string
   */
  protected string $apiKey = '';

  /**
   * Run moderation call, before a normal call.
   *
   * @var bool
   */
  protected bool $moderation = TRUE;

  /**
   * {@inheritdoc}
   * @param string|null $operation_type
   * @param array $capabilities
   */
  public function isUsable(string $operation_type = NULL, array $capabilities = []): bool {
    if (!$this->getConfig()->get('api_key')) {
      return FALSE;
    }

    if ($operation_type) {
      return in_array($operation_type, $this->getSupportedOperationTypes());
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSupportedOperationTypes(): array {
    return ['chat'];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(): ImmutableConfig {
    return $this->configFactory->get('ai_provider_deepseek.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getApiDefinition(): array {
    $definition = Yaml::parseFile(
      $this->moduleHandler->getModule('ai_provider_deepseek')
        ->getPath() . '/definitions/api_defaults.yml'
    );
    return $definition;
  }

  /**
   * {@inheritdoc}
   */
  public function getModelSettings(string $model_id, array $generalConfig = []): array {
    return $generalConfig;
  }

  /**
   * {@inheritdoc}
   */
  public function setAuthentication(mixed $authentication): void {
    $this->apiKey = $authentication;
    $this->client = NULL;
  }

  /**
   * Gets the raw client.
   *
   * @param string $api_key
   *   If the API key should be hot swapped.
   *
   * @return \DeepseekPhp\DeepseekClient
   *   The Deepseek client.
   */
  public function getClient(string $api_key = '') {
    if ($api_key) {
      $this->setAuthentication($api_key);
    }

    $this->loadClient();
    return $this->client;
  }

  /**
   * Loads the DeepSeek Client with authentication if not initialized.
   */
  protected function loadClient(): void {
    if (!$this->client) {
      if (!$this->apiKey) {
        $this->setAuthentication($this->loadApiKey());
      }

      $this->client = DeepseekClient::build($this->apiKey);
    }
  }

  /**
   * {@inheritdoc}
   * @param string|null $operation_type
   * @param array $capabilities
   */
  public function getConfiguredModels(string $operation_type = NULL, array $capabilities = []): array {
    return [
      'deepseek-chat' => 'DeepSeek Chat',
      'deepseek-coder' => 'DeepSeek Coder'
    ];
  }

  /**
   * Load API key from key module.
   *
   * @return string
   *   The API key.
   */
  protected function loadApiKey(): string {
    return $this->keyRepository->getKey($this->getConfig()->get('api_key'))
      ->getKeyValue();
  }
  
  public function chat(array|string|ChatInput $input, string $model_id, array $tags = []): ChatOutput {
    $this->loadClient();

    $response = $this->client->query('HEY')->withModel($model_id)->run();
    $message = new ChatMessage('user', (string)$response);

    return new ChatOutput($message, $response, []);
  }

}
