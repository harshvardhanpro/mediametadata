<?php
/**
 * ownCloud - mediametadata
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Jalpreet Singh Nanda (:imjalpreet) <jalpreetnanda@gmail.com>
 * @copyright Jalpreet Singh Nanda (:imjalpreet) 2016
 */

namespace OCA\MediaMetadata\Hooks;


use OC\Files\Node\Root;
use OCA\MediaMetadata\Services\ExtractMetadata;
use OCA\MediaMetadata\Services\ImageDimension;
use OCA\MediaMetadata\Services\ImageDimensionMapper;
use OCA\MediaMetadata\Services\StoreMetadata;
use OCP\Files\Node;

class ImageHooks {
	protected $root;
	protected $mapper;
	protected $dataDirectory;
	protected $metadataExtractor;
	protected $dbManager;

	/**
	 * @param Root $root
	 * @param ImageDimensionMapper $mapper
	 * @param ExtractMetadata $metadataExtractor
	 * @param StoreMetadata $dbManager
	 * @param $dataDirectory
	 */
	public function __construct(Root $root, ImageDimensionMapper $mapper, ExtractMetadata $metadataExtractor, StoreMetadata $dbManager, $dataDirectory) {
		$this->root = $root;
		$this->mapper = $mapper;
		$this->dataDirectory = $dataDirectory;
		$this->metadataExtractor = $metadataExtractor;
		$this->dbManager = $dbManager;
	}

	public function register() {
		$reference = $this;

		$callback = function (Node $node) use($reference) {
			$reference->postCreate($node);
		};

		$this->root->listen('\OC\Files', 'postCreate', $callback);
	}

	/**
	 * @param Node $node
	 * @return bool
	 */
	public function postCreate(Node $node) {
		$absolutePath = $this->dataDirectory.$node->getPath();

		$metadata = $this->metadataExtractor->extract($absolutePath);

		$logger = \OC::$server->getLogger();

		if($metadata != null && sizeof($metadata) > 0) {
			$result = $this->dbManager->store($metadata, $node);

			if($result == false) {
				$logger->debug('Metadata could not be inserted', array('app' => 'MediaMetadata'));
			}
		}
		else {
			$logger->debug('No metadata could be extracted', array('app' => 'MediaMetadata'));
			$result = false;
		}

		return $result;
	}
}
