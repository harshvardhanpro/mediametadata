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

namespace OCA\MediaMetadata\Services;


use OCP\AppFramework\Db\Mapper;
use OCP\IDb;
use OCP\IDBConnection;

class ImageDimensionMapper extends Mapper {
	/**
	 * @param IDBConnection $database
	 */
	public function __construct(IDBConnection $database) {
		parent::__construct($database, 'mediametadata', '\OCA\MediaMetadata\Services\ImageDimension');
	}

	/**
	 * @param $imageID
	 * @return \OCP\AppFramework\Db\Entity
	 */
	public function find($imageID) {
		$sql = 'SELECT * FROM *PREFIX*mediametadata WHERE image_id = ?';
		return $this->findEntity($sql, [$imageID]);
	}
}
