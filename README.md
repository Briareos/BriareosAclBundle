# ACL bundle for Symfony2

This bundle integrates access permissions using the following relationship model:

**User** \<many-to-many> **Role** \<many-to-many> **Permission**

So one user can have multiple roles and each role may grant multiple permissions. Permissions are pulled from permission
containers that are tagged with `security.permission_container`. There should be a maximum of one permission container
per bundle. It integrates nicely with *SonataAdminBundle*.

## Instructions

1.  Your user class must implement `Briareos\AclBundle\Entity\AclSubject` interface.

1.  Map the interface to your user bundle, so that relationships can work:

        # app/config/config.yml
        doctrine:
            orm:
                resolve_target_entities:
                    Briareos\AclBundle\Entity\AclSubject: App\UserBundle\Entity\User

1.  Since the user entity must be on the owning side, map the AclRole entity from your user entity:

    Annotation:

        # App\UserBundle\Entity\User
        /**
         * @var ArrayCollection
         *
         * @ORM\ManyToMany(targetEntity="Briareos\AclBundle\Entity\AclRole", inversedBy="subjects")
         * @ORM\JoinTable(name="acl__subject_role",
         *  joinColumns={@ORM\JoinColumn(name="subject_id", referencedColumnName="id", onDelete="CASCADE")},
         *  inverseJoinColumns={@ORM\JoinColumn(name="aclRole_id", referencedColumnName="id", onDelete="CASCADE")}
         * )
         */
        private $aclRoles;

    Yaml:

        # App\UserBundle\Resources\config\doctrine\User.orm.yml
        joinTable:
            name: acl__subject_role
            inversedBy: subjects
            joinColumns:
                subject:
                    name: subject_id
                    referencedColumnName: id
                    onDelete: CASCADE
            inverseJoinColumns:
                aclRole:
                    name: aclRole_id
                    referencedColumnName: id
                    onDelete: CASCADE

1.  Update your schema:

        $ php app/console doctrine:schema:update --force

1.  Update your `config.yml` file to include additional form resources, for nice display of the permission tree:

        # app/config/config.yml
        twig:
            resources:
                form:
                    - "BriareosAclBundle::fields.html.twig"

1.  By default, there are 3 services in Symfony tagged as security voters.
    As this service should replace two of them (for now), it is recommended to disable others than `AuthenticatedVoter`:

        # app/config/security.yml
        jms_security_extra:
            voters:
                disable_role: true
                disable_acl: true

### If you're using *SonataAdminBundle*:

1.  Chance the security handler to `sonata.admin.security.handler.briareos_acl`:

        sonata_admin:
            security:
                handler: sonata.admin.security.handler.briareos_acl


## Example

If you're using *SonataMediaBundle*, this permission container should take care of adding permissions for *Media* and *Gallery* entities:

    <?php

    namespace Application\Sonata\MediaBundle\Admin;

    use Briareos\AclBundle\Security\Authorization\PermissionContainerInterface;

    class PermissionContainer implements PermissionContainerInterface
    {
        public function getPermissions()
        {
            return array(
                'admin' => array(
                    '__children' => array(
                        'applicationsonatamediabundle_media' => array(
                            'weight' => 11,
                            '__children' => array(
                                'create' => array(),
                                'list' => array(),
                                'edit' => array(),
                                'delete' => array(),
                            ),
                        ),
                        'applicationsonatamediabundle_gallery' => array(
                            'weight' => 12,
                            '__children' => array(
                                'create' => array(),
                                'list' => array(),
                                'edit' => array(),
                                'delete' => array(),
                            ),
                        ),
                    ),
                ),
            );
        }

    }

The permission keys are interpreted as `admin.applicationsonatamediabundle_gallery.edit`, but can be as short as `main_bundle.user.create`.
Permissions can be nested to suit your application's needs, like `admin.userbundle_user.edit.groups`.

## Todo

1.  Make it possible to configure `AclSecurityHandler` (that implements `Sonata\AdminBundle\Security\Handler\RoleSecurityHandler`)

1.  Implement caching mechanism inside `AclVoter`.

1.  Have an interface that enables entities to return their 'owners' and other related entities.