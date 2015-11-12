OJezu/ConfigurableDiscriminationBundle
====

This Bundle solves Doctrine problem with inheritance: discriminatorMap
has to be declared on parent entity, and has to have all children declared
in it. If you leave it empty, Doctrine will scan ALL files to look for
classes that extend the parent class, and will auto-generate entries for
them in discriminatorMap along with discriminator values - over which
programmer in that case has absolutely no control - they will be derived from
name of child class and that's it.

That is especially nasty, if some of the children classes are in other bundles,
programmer has to make the parent bundle aware of all the child classes!

This Bundle allows specifying discriminator values and children classes in
child bundle configuration - making the code of parent bundle clear of any
references to children bundles, avoids scanning all class files in search of
children classes and allows specifying custom discriminator values.

Usage
==

Register this bundle in app/appKernel.php:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new OJezu\ConfigurableDiscriminationBundle\ConfigurableDiscriminationBundle(),
            // app bundles
            new Acme\TestBundle\ParentBundle(),
            new Acme\TestBundle\ChildABundle(),
            new Acme\TestBundle\ChildBBundle(),
        );
        return $bundles;
    }
}
```

In ParentBundle:
=
You have to declare non-empty discriminatorMap or otherwise Doctrine will try
to auto-generate it. OJezu/ConfigurableDiscriminationBundle will only add entries
to it.

```php
#Acme/AcmeParent/Entity/AcmeParent.php

/**
 * (...)
 * @ORM\DiscriminatorMap({"base" = "AcmeParent"})
 */
class AcmeParent
{
    (...)
}
```

In ChildABundle:
=
Add some faux-services tagged with `ojezu.configurable_discrimination` to your
bundle internal config so that OJezu/ConfigurableDiscriminationBundle can read
your custom configuration.

Name of the service does not matter.

```php
# service.yml
services:
    acme.acme_entity.link_task_state:
        class: 'Acme\AcmeChildA\Entity\AcmeChildA'
        public: false
        tags:
            - name: ojezu.configurable_discrimination
              discriminator_value: 'acme_child_a'
```

`Acme\AcmeChildA\Entity\AcmeChildA` of course must be defined, and directly
extend `Acme\AcmeParent\Entity\AcmeParent`

In ChildBBundle:
=

```php
# service.yml
services:
    acme.acme_entity.link_task_state:
        class: 'Acme\AcmeChildB\Entity\AcmeChildB'
        public: false
        tags:
            - name: ojezu.configurable_discrimination
              discriminator_value: 'acme_child_b'
```

Acknowledgments
===
Thanks to https://github.com/sredni who helped me to get into Symfony and
did some code reviewing of this package.

This package is somewhat based on
[DCSDynamicDiscriminatorMapBundle](https://github.com/damianociarla/DCSDynamicDiscriminatorMapBundle)
and on
[Defining Discriminator Maps at Child-level in Doctrine 2](https://medium.com/@jasperkuperus/defining-discriminator-maps-at-child-level-in-doctrine-2-1cd2ded95ffb)

License
===
MIT
