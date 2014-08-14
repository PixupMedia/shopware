<?php
/**
 * Shopware 4
 * Copyright © shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Bundle\StoreFrontBundle\Gateway\DBAL;

use Doctrine\DBAL\Connection;
use Shopware\Components\Model\ModelManager;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Bundle\StoreFrontBundle\Gateway;

/**
 * @category  Shopware
 * @package   Shopware\Bundle\StoreFrontBundle\Gateway\DBAL
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class MediaGateway implements Gateway\MediaGatewayInterface
{
    /**
     * @var ModelManager
     */
    private $entityManager;

    /**
     * @var FieldHelper
     */
    private $fieldHelper;

    /**
     * @var Hydrator\MediaHydrator
     */
    private $hydrator;

    /**
     * @param ModelManager $entityManager
     * @param FieldHelper $fieldHelper
     * @param Hydrator\MediaHydrator $hydrator
     */
    public function __construct(
        ModelManager $entityManager,
        FieldHelper $fieldHelper,
        Hydrator\MediaHydrator $hydrator
    ) {
        $this->entityManager = $entityManager;
        $this->fieldHelper = $fieldHelper;
        $this->hydrator = $hydrator;
    }

    /**
     * @inheritdoc
     */
    public function get($id, Struct\Context $context)
    {
        $media = $this->getList(array($id), $context);

        return array_shift($media);
    }

    /**
     * @inheritdoc
     */
    public function getList($ids, Struct\Context $context)
    {
        $query = $this->getQuery($context);

        $query->setParameter(':ids', $ids, Connection::PARAM_INT_ARRAY);

        /**@var $statement \Doctrine\DBAL\Driver\ResultStatement */
        $statement = $query->execute();

        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $result = array();
        foreach ($data as $row) {
            $mediaId = $row['__media_id'];
            $result[$mediaId] = $this->hydrator->hydrate($row);
        }

        return $result;
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getQuery()
    {
        $query = $this->entityManager->getDBALQueryBuilder();

        $query->select($this->fieldHelper->getMediaFields())
            ->addSelect($this->fieldHelper->getMediaSettingFields());

        $query->from('s_media', 'media')
            ->innerJoin('media', 's_media_album_settings', 'mediaSettings', 'mediaSettings.albumID = media.albumID')
            ->leftJoin('media', 's_media_attributes', 'mediaAttribute', 'mediaAttribute.mediaID = media.id');

        $query->where('media.id IN (:ids)');

        return $query;
    }
}