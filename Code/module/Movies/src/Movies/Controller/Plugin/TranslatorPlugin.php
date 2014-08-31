<?php
namespace Movies\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
 
class TranslatorPlugin extends AbstractPlugin{
	private $serviceLocator;
    private $translator;

	public function getTranslator()
	{
		if(!$this->translator){
			$this->translator = $this->getController()->getServiceLocator()->get('translator');
		}

		return $this->translator;
	}

    public function translate($text){
        return $this->getTranslator()->translate($text);
    }
}