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
namespace Fratily\Bundle\Doctrine\Container;

use Fratily\Bundle\Doctrine\ManagerRegistry;
use Fratily\Container\Builder\AbstractContainer;
use Fratily\Container\Builder\ContainerBuilder;

/**
 *
 */
class DoctrineContainer extends AbstractContainer{

    /**
     * {@inheritdoc}
     */
    public static function build(ContainerBuilder $builder, array $options){
        $builder
            ->add(
                "doctrine",
                ManagerRegistry::class,
                [],
                [ManagerRegistry::class]
            )
        ;

        $builder->parameter(ManagerRegistry::class)
            ->add(
                "managers",
                $builder->lazyGetTaggedIdList("doctrine.entityManager")
            )
            ->add(
                "default",
                $builder->lazyCallable(
                    function($list){
                        return array_shift($list);
                    },
                    $builder->lazyGetTaggedIdList("doctrine.entityManager.default")
                )
            )
        ;

        $builder->isSingleton(ManagerRegistry::class);
    }

    /**
     * {@inheritdoc}
     */
    public static function modify(\Fratily\Container\Container $container){
    }
}