<?php

namespace App\Form;

use App\Entity\Episode;
use App\Entity\Season;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EpisodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('number')
            ->add('synopsis')
            ->add('season', null, ['choice_label' => 'nbseason'
//            ->add('season', EntityType::class, [
//                'class' => Season::class,
//
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('s')
//                        ->andWhere('s.name', 'ASC');
//                },
//                'choice_label' => 'nbseason',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Episode::class,
        ]);
    }
}
