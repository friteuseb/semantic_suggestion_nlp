<?php
namespace TalanHdf\SemanticSuggestionNlp\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationController extends ActionController
{
    protected $extensionConfiguration;

    public function __construct(ExtensionConfiguration $extensionConfiguration)
    {
        $this->extensionConfiguration = $extensionConfiguration;
    }

    public function indexAction()
    {
        $configuration = $this->extensionConfiguration->get('semantic_suggestion_nlp');
        $scenarios = [
            'DefaultScenario' => 'Default Scenario',
            // Add more scenarios here as they are implemented
        ];

        $this->view->assign('configuration', $configuration);
        $this->view->assign('scenarios', $scenarios);
    }

    public function saveAction()
    {
        $configuration = $this->request->getArgument('configuration');
        
        try {
            $this->extensionConfiguration->set('semantic_suggestion_nlp', $configuration);
            $this->addFlashMessage('Configuration saved successfully.', 'Success', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        } catch (\Exception $e) {
            $this->addFlashMessage('Failed to save configuration: ' . $e->getMessage(), 'Error', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        }

        $this->redirect('index');
    }
}