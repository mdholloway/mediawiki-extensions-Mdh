<?php

namespace MediaWiki\Extension\Mdh;

use ApiBase;
use ApiQuery;
use ApiQueryBase;
use LogicException;
use Wikimedia\ParamValidator\ParamValidator;

class ApiQueryApiModules extends ApiQueryBase {

	private const PROP_NAME = 'name';
	private const PROP_EXAMPLES = 'examples';
	private const GROUP_ACTION = 'action';
	private const GROUP_QUERY = 'query';

	/**
	 * ApiQueryApiModules constructor.
	 * @param ApiQuery $queryModule
	 * @param $moduleName
	 * @param string $paramPrefix
	 */
	public function __construct(ApiQuery $queryModule, $moduleName, $paramPrefix = 'apimodules') {
		parent::__construct($queryModule, $moduleName, $paramPrefix);
	}

	/** @inheritDoc */
	public function execute() : void {
		$groups = $this->getParameter( 'group' );
		$props = $this->getParameter( 'prop' );

		$result['groups'] = [];
		foreach ( $groups as $group ) {
			$result['groups'][] = $this->collectModulePropsForGroup( $group, $props );
		}
		$this->getResult()->addValue( 'query', 'apimodules', $result );
	}

	/** @inheritDoc */
	protected function getAllowedParams() : array {
		return [
			'prop' => [
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_TYPE => [ self::PROP_NAME, self::PROP_EXAMPLES ],
				ParamValidator::PARAM_ISMULTI => true,
				ParamValidator::PARAM_DEFAULT => self::PROP_NAME,
			],
			'group' => [
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_TYPE => [ self::GROUP_ACTION, self::GROUP_QUERY ],
				ParamValidator::PARAM_ISMULTI => true,
			]
		];
	}

	/** @inheritDoc */
	protected function getExamplesMessages() : array {
		return [
			'action=query&meta=apimodules&apimodulesgroup=action|query&apimodulesprop=name|examples' =>
				'apihelp-query+apimodules-example',
		];
	}

	/**
	 * @param string $group group name
	 * @param array $props requested properties
	 * @return array associative array of module data
	 */
	private function collectModulePropsForGroup( string $group, array $props ) : array {
		$result['name'] = $group;
		$moduleManager = $this->getModuleForGroup( $group )->getModuleManager();
		$moduleNames = $moduleManager->getNames();
		foreach ( $moduleNames as $moduleName ) {
			$moduleData[self::PROP_NAME] = $moduleName;
			if ( in_array( self::PROP_EXAMPLES, $props ) ) {
				$module = $moduleManager->getModule( $moduleName );
				$examplesMessages = $module->getExamplesMessages();
				$moduleData[self::PROP_EXAMPLES] = [];
				foreach ( $examplesMessages as $url => $messageKey ) {
					$moduleData[self::PROP_EXAMPLES][] = [
						'url' => $url,
						'text' => $this->msg( $messageKey )
					];
				}
			}
			$result['modules'][] = $moduleData;
		}
		return $result;
	}

	private function getModuleForGroup( string $group ) : ApiBase {
		switch ( $group ) {
			case self::GROUP_ACTION:
				return $this->getMain();
			case self::GROUP_QUERY:
				return $this->getQuery();
			default:
				throw new LogicException( 'Invalid group. Check allowed params!' );
		}
	}

}
