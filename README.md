# Semantic Suggestion NLP Extension

This TYPO3 extension extends the functionality of the 'semantic_suggestion' extension by adding Natural Language Processing (NLP) capabilities.

## Installation

1. Install the extension using Composer:
   ```
   composer require vendor/semantic-suggestion-nlp
   ```
   Replace `vendor` with the actual vendor name used in your extension.

2. Activate the extension in the TYPO3 Extension Manager.

## Configuration

1. In the TYPO3 backend, go to "Admin Tools" > "Settings" > "Extension Configuration".
2. Find and click on "semantic_suggestion_nlp".
3. Configure the following options:
   - Enable NLP Analysis: Turn on/off the NLP functionality.
   - Selected Scenario: Choose the NLP analysis scenario to use.

## Usage

Once installed and configured, this extension will automatically enhance the semantic suggestions provided by the main 'semantic_suggestion' extension with NLP-based analysis and recommendations.

The NLP analysis will be triggered during the page analysis process, and the results will be integrated into the existing suggestions.

## Extending the Extension

### Adding New Scenarios

1. Create a new class in `Classes/Scenario/` that extends `AbstractScenario`.
2. Implement the `process` method with your custom NLP logic.
3. Add your new scenario to the list of available scenarios in `ConfigurationController.php`.

## Troubleshooting

If you encounter any issues:

1. Check that both 'semantic_suggestion' and 'semantic_suggestion_nlp' extensions are installed and active.
2. Verify that the NLP analysis is enabled in the extension configuration.
3. Check the TYPO3 log files for any error messages.

For further assistance, please contact the extension maintainer.