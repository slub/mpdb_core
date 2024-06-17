<?php

/**
 * This class wraps around Elasticsearch's ClientBuilder and offers an automatic configuration
 */

namespace Slub\MpdbCore\Common;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Elastic\Elasticsearch\ClientBuilder;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ElasticClientBuilder extends ClientBuilder {

    public static function create(): ElasticClientBuilder
    {
        return new ElasticClientBuilder();
    }

    public function autoconfig ()
    {
		$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('mpdb_core');
		$hosts = [ $extConf['elasticHostName'] ];
		$password = '';
        $passwordFilePath = $this->getFileName($extConf['elasticPwdPath']);
		if ($passwordFilePath != '') {
            $passwordFile = fopen($passwordFilePath, 'r') or die($passwordFilePath . ' not found. Check your extension\'s configuration');
			$password = trim(fgets($passwordFile));
			fclose($passwordFile);
		} else {
			$hosts = [ 'http://' . $extConf['elasticHostName'] . ':9200' ];
		}
		$caFilePath = $this->getFileName($extConf['elasticCaFilePath']);

		$this->sethosts($hosts);
		if ($password) {
			$this->setBasicAuthentication('elastic', $password);
		}
		if ($caFilePath) {
			$this->setSSLVerification($caFilePath);
		}

		return $this;
	}

    private function getFileName ($fileName)
    {
        if ($fileName == '')
            return '';
        if (in_array($fileName, scandir('.')))
            return $fileName;
        if (in_array($fileName, scandir('..')))
            return '../' . $fileName;
        return '../../' . $fileName;
    }
}
