<?php
/**
 * FratilyPHP Doctrine Bundle
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento-oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\Bundle\Doctrine;

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 *
 */
class ManagerRegistry{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string[]
     */
    private $managers;

    /**
     * @var string
     */
    private $default;

    /**
     * Constructor
     *
     * @param   ContainerInterface  $container
     *  サービスコンテナ
     * @param   string  $managers
     *  エンティティマネージャのサービスIDのリスト
     * @param   string  $default
     *  デフォルトエンティティマネージャのサービスID
     */
    public function __construct(ContainerInterface $container, array $managers, string $default = null){
        if(empty($managers) && null !== $default){
            $managers[] = $default;
        }

        foreach($managers as $manager){
            if(!is_string($manager)){
                throw new \InvalidArgumentException;
            }

            if(!$container->has($manager)){
                throw new \InvalidArgumentException;
            }

            if(null === $default){
                $default    = $manager;
            }

            if($default === $manager){
                $find   = true;
            }
        }

        if(!$find){
            throw new \InvalidArgumentException;
        }

        $this->container    = $container;
        $this->managers     = array_flip($managers);
        $this->default      = $default;
    }

    /**
     * エンティティマネージャを取得する
     *
     * @param   string  $manager
     *  マネージャサービスID
     *
     * @return  EntityManagerInterface
     */
    public function get(string $manager = null){
        if(null !== $manager && !array_key_exists($manager, $this->managers)){
            throw new \InvalidArgumentException();
        }

        return $this->container->get($manager ?? $this->default);
    }

    /**
     * エンティティマネージャが存在するか確認する
     *
     * @param   string  $manager
     *  マネージャサービスID
     *
     * @return  string
     */
    public function has(string $manager){
        return array_key_exists($manager, $this->managers);
    }

    /**
     * リポジトリを取得する
     *
     * @param   string  $entity
     *  エンティティクラス
     * @param   string  $manager
     *  エンティティマネージャID
     *
     * @return  ObjectRepository
     */
    public function getRepository(string $entity, string $manager = null){
        return $this->get($manager)->getRepository($entity);
    }
}