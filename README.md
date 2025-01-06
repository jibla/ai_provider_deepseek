# DeepSeek Provider

DeepSeek Provider is a module that integrates DeepSeek's Large Language Model (LLM) 
with Drupal's AI module. This integration allows you to use DeepSeek's powerful 
language capabilities within your Drupal site.

## Requirements

* [AI module](https://www.drupal.org/project/ai)
* [Key module](https://www.drupal.org/project/key)

## Installation

1. Install the module using composer:
   ```bash
   composer require drupal/ai_provider_deepseek
   ```

2. Enable the module:
   ```bash
   drush en ai_provider_deepseek
   ```

## Configuration

1. Go to `/admin/config/system/keys/add` to add your Deepseek API key.
2. Create a new key with the following settings:
   * Key type: Authentication
   * Key provider: Configuration
   * Key input: Text field
   * Enter your Deepseek API key value

3. Go to `/admin/config/ai/provider/deepseek` to configure the Deepseek provider:
   * Select the key you created
   * Configure any additional settings specific to your use case

## Usage

Once configured, you can use Deepseek's LLM capabilities through any feature that 
utilizes Drupal's AI module. The provider will be available as an option in the AI 
module's configuration interfaces.

## Maintainers

Current maintainers:
* Giorgi Jibladze - https://www.drupal.org/u/jibla
* Giorgi Gagoshidze - https://www.drupal.org/u/gagosha
